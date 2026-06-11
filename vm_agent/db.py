import sys
import os
import datetime
from config import DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

HAS_MYSQL = False
mysql_conn = None

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
    global mysql_conn
    if not HAS_MYSQL:
        print("[WARNING] MySQL database library (mysql-connector-python or PyMySQL) is not installed.")
        print("Run: pip install mysql-connector-python")
        return None
    
    try:
        if mysql_conn is None or not is_connected(mysql_conn):
            mysql_conn = connect_fn()
        return mysql_conn
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
