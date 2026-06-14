import sys
import os
import datetime
import threading
from config import DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

HAS_MYSQL = False
thread_local = threading.local()

try:
    import mysql.connector
    HAS_MYSQL = True
    connect_fn = lambda: mysql.connector.connect(
        host=DB_HOST,
        port=DB_PORT,
        database=DB_DATABASE,
        user=DB_USERNAME,
        password=DB_PASSWORD
    )
except ImportError:
    try:
        import pymysql
        HAS_MYSQL = True
        connect_fn = lambda: pymysql.connect(
            host=DB_HOST,
            port=DB_PORT,
            database=DB_DATABASE,
            user=DB_USERNAME,
            password=DB_PASSWORD,
            cursorclass=pymysql.cursors.DictCursor
        )
    except ImportError:
        HAS_MYSQL = False

def get_connection():
    if not HAS_MYSQL:
        print("[WARNING] MySQL database library (mysql-connector-python or PyMySQL) is not installed.")
        print("Run: pip install mysql-connector-python")
        return None
    
    try:
        conn = getattr(thread_local, "mysql_conn", None)
        if conn is None or not is_connected(conn):
            conn = connect_fn()
            # Set autocommit to True to prevent frozen transaction read snapshots
            try:
                conn.autocommit = True
            except:
                pass
            thread_local.mysql_conn = conn
        return conn
    except Exception as e:
        print(f"[ERROR] Failed to connect to MySQL database: {e}")
        return None


def is_connected(conn):
    try:
        if hasattr(conn, "is_connected"):
            return conn.is_connected()
        conn.ping(reconnect=True)
        return True
    except:
        return False

# Database queries helpers
def fetch_active_keywords():
    conn = get_connection()
    if not conn:
        # Return mock keywords for simulation mode
        return [
            {"id": 1, "project_id": 1, "keyword": "roast my landing page", "status": "Active"},
            {"id": 2, "project_id": 1, "keyword": "website feedback", "status": "Active"}
        ]
    
    is_mysql_connector = "mysql.connector" in type(conn).__module__
    cursor = conn.cursor(dictionary=True) if is_mysql_connector else conn.cursor()
    try:
        cursor.execute("SELECT * FROM keywords WHERE status = 'Active'")
        # Handle dict cursor variations between pymysql and mysql-connector
        if hasattr(cursor, "fetchall"):
            res = cursor.fetchall()
            # Convert tuple list to dict list if cursor is not dict-based
            if res and isinstance(res[0], tuple):
                cols = [desc[0] for desc in cursor.description]
                res = [dict(zip(cols, row)) for row in res]
            return res
        return []
    except Exception as e:
        print(f"[DB ERROR] fetch_active_keywords: {e}")
        return []
    finally:
        cursor.close()

def save_scraped_post(project_id, platform, external_id, title, content, author, url):
    conn = get_connection()
    if not conn:
        print(f"[SIMULATED DB] Saved post from {platform} by {author}")
        return 999
        
    cursor = conn.cursor()
    try:
        # Check if already exists
        cursor.execute("SELECT id FROM posts WHERE platform = %s AND external_id = %s", (platform, external_id))
        row = cursor.fetchone()
        if row:
            return row[0]
            
        now = datetime.datetime.now(datetime.timezone.utc).replace(tzinfo=None).strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute(
            "INSERT INTO posts (project_id, platform, external_id, title, content, author, url, status, scraped_at, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s, %s, 'Pending', %s, %s, %s)",
            (project_id, platform, external_id, title, content, author, url, now, now, now)
        )
        conn.commit()
        return cursor.lastrowid
    except Exception as e:
        print(f"[DB ERROR] save_scraped_post: {e}")
        return None
    finally:
        cursor.close()

def save_lead(post_id, project_id, contact_name, contact_email, score, intent_category, notes, generated_reply):
    conn = get_connection()
    if not conn:
        print(f"[SIMULATED DB] Qualified lead {contact_name} (Score: {score})")
        return 999
        
    cursor = conn.cursor()
    try:
        # Check if lead already exists for this post
        cursor.execute("SELECT id FROM leads WHERE post_id = %s", (post_id,))
        if cursor.fetchone():
            return None
            
        now = datetime.datetime.now(datetime.timezone.utc).replace(tzinfo=None).strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute(
            "INSERT INTO leads (post_id, project_id, contact_name, contact_email, score, intent_category, status, notes, generated_reply, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s, 'New', %s, %s, %s, %s)",
            (post_id, project_id, contact_name, contact_email, score, intent_category, notes, generated_reply, now, now)
        )
        # Also update post status to Qualified
        cursor.execute("UPDATE posts SET status = 'Qualified' WHERE id = %s", (post_id,))
        
        # Create system notification
        cursor.execute(
            "INSERT INTO notifications (user_id, title, message, is_read, created_at, updated_at) VALUES (%s, %s, %s, 0, %s, %s)",
            (1, "New Qualified Lead Found!", f"AI qualified {contact_name} as {intent_category} ({score}/100)", now, now)
        )
        
        # Log audit action
        cursor.execute(
            "INSERT INTO audit_logs (user_id, action, target_table, ip_address, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s)",
            (None, f"Scraper qualified lead '{contact_name}' ({score}/100)", 'leads', '127.0.0.1', now, now)
        )
        
        conn.commit()
        return cursor.lastrowid
    except Exception as e:
        print(f"[DB ERROR] save_lead: {e}")
        return None
    finally:
        cursor.close()

def update_keyword_scraped_time(keyword_id):
    conn = get_connection()
    if not conn:
        return
    cursor = conn.cursor()
    try:
        now = datetime.datetime.now(datetime.timezone.utc).replace(tzinfo=None).strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute("UPDATE keywords SET last_scraped_at = %s, updated_at = %s WHERE id = %s", (now, now, keyword_id))
        conn.commit()
    except Exception as e:
        print(f"[DB ERROR] update_keyword_scraped_time: {e}")
    finally:
        cursor.close()

import json

def add_queue_task(client_id, task_type, payload_dict):
    conn = get_connection()
    if not conn:
        print(f"[SIMULATED DB] Enqueued task {task_type}")
        return 999
    
    cursor = conn.cursor()
    try:
        now = datetime.datetime.now(datetime.timezone.utc).replace(tzinfo=None).strftime('%Y-%m-%d %H:%M:%S')
        payload_str = json.dumps(payload_dict)
        cursor.execute(
            "INSERT INTO queue_tasks (client_id, task_type, payload, status, attempts, created_at, updated_at) VALUES (%s, %s, %s, 'Pending', 0, %s, %s)",
            (client_id, task_type, payload_str, now, now)
        )
        conn.commit()
        return cursor.lastrowid
    except Exception as e:
        print(f"[DB ERROR] add_queue_task: {e}")
        return None
    finally:
        cursor.close()

def fetch_next_pending_task():
    conn = get_connection()
    if not conn:
        return None
    
    is_mysql_connector = "mysql.connector" in type(conn).__module__
    cursor = conn.cursor(dictionary=True) if is_mysql_connector else conn.cursor()
    try:
        # Turn off autocommit temporarily to open a transaction block for row-level locks
        try:
            conn.autocommit = False
        except:
            pass
            
        cursor.execute("SELECT * FROM queue_tasks WHERE status = 'Pending' ORDER BY id ASC LIMIT 1 FOR UPDATE")
        res = cursor.fetchone()
        if not res:
            try:
                conn.commit()
                conn.autocommit = True
            except:
                pass
            return None
        
        # Convert tuple to dict if not dictionary cursor
        if res and isinstance(res, tuple):
            cols = [desc[0] for desc in cursor.description]
            res = dict(zip(cols, res))
        
        # De-serialize payload
        if "payload" in res and isinstance(res["payload"], str):
            try:
                res["payload"] = json.loads(res["payload"])
            except:
                pass
        
        task_id = res["id"]
        # Update state to Processing immediately to lock it
        now = datetime.datetime.now(datetime.timezone.utc).replace(tzinfo=None).strftime('%Y-%m-%d %H:%M:%S')
        
        cursor.execute(
            "UPDATE queue_tasks SET status = 'Processing', attempts = attempts + 1, updated_at = %s WHERE id = %s",
            (now, task_id)
        )
        conn.commit()
        
        # Restore autocommit mode
        try:
            conn.autocommit = True
        except:
            pass
            
        return res
    except Exception as e:
        try:
            conn.rollback()
            conn.autocommit = True
        except:
            pass
        print(f"[DB ERROR] fetch_next_pending_task: {e}")
        return None
    finally:
        cursor.close()

def reset_orphaned_tasks():
    conn = get_connection()
    if not conn:
        return
    cursor = conn.cursor()
    try:
        now = datetime.datetime.now(datetime.timezone.utc).replace(tzinfo=None).strftime('%Y-%m-%d %H:%M:%S')
        # Reset Processing tasks back to Pending
        cursor.execute("UPDATE queue_tasks SET status = 'Pending', error_message = 'Worker crash recovery reset', updated_at = %s WHERE status = 'Processing'", (now,))
        conn.commit()
        print("[DB] Automatically reset any orphaned 'Processing' tasks back to 'Pending'.")
    except Exception as e:
        print(f"[DB ERROR] reset_orphaned_tasks: {e}")
    finally:
        cursor.close()


def update_task_status(task_id, status, error_message=None, result_path=None):
    conn = get_connection()
    if not conn:
        print(f"[SIMULATED DB] Updated task {task_id} to status {status}")
        return
    
    cursor = conn.cursor()
    try:
        now = datetime.datetime.now(datetime.timezone.utc).replace(tzinfo=None).strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute(
            "UPDATE queue_tasks SET status = %s, error_message = %s, result_path = %s, updated_at = %s WHERE id = %s",
            (status, error_message, result_path, now, task_id)
        )
        conn.commit()
    except Exception as e:
        print(f"[DB ERROR] update_task_status: {e}")
    finally:
        cursor.close()

def fetch_task_by_id(task_id):
    conn = get_connection()
    if not conn:
        return None
    is_mysql_connector = "mysql.connector" in type(conn).__module__
    cursor = conn.cursor(dictionary=True) if is_mysql_connector else conn.cursor()
    try:
        cursor.execute("SELECT * FROM queue_tasks WHERE id = %s", (task_id,))
        res = cursor.fetchone()
        if res and isinstance(res, tuple):
            cols = [desc[0] for desc in cursor.description]
            res = dict(zip(cols, res))
        return res
    except Exception as e:
        print(f"[DB ERROR] fetch_task_by_id: {e}")
        return None
    finally:
        cursor.close()

def update_post_image(post_id, image_url):
    conn = get_connection()
    if not conn:
        return
    cursor = conn.cursor()
    try:
        now = datetime.datetime.now(datetime.timezone.utc).replace(tzinfo=None).strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute("UPDATE posts SET image_url = %s, updated_at = %s WHERE id = %s", (image_url, now, post_id))
        conn.commit()
        print(f"[DB] Successfully updated posts ID {post_id} with image_url: {image_url}")
    except Exception as e:
        print(f"[DB ERROR] update_post_image: {e}")
    finally:
        cursor.close()

