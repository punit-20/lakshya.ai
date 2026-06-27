import sys
import os
import subprocess
import time
import datetime
import warnings

# Suppress warnings
warnings.filterwarnings("ignore", category=FutureWarning)
warnings.filterwarnings("ignore", category=DeprecationWarning)

# Check and install Flask dependencies
def check_and_install_dependencies():
    import importlib
    required = {
        "mysql.connector": "mysql-connector-python",
        "google.genai": "google-genai",
        "colorama": "colorama",
        "flask": "flask"
    }
    for module, package in required.items():
        try:
            importlib.import_module(module)
        except ImportError:
            print(f"[BOOT] Package '{package}' is missing. Installing...")
            try:
                subprocess.check_call([sys.executable, "-m", "pip", "install", package])
                print(f"[BOOT] Successfully installed '{package}'.")
            except Exception as e:
                print(f"[BOOT ERROR] Failed to auto-install '{package}': {e}")
                sys.exit(1)

check_and_install_dependencies()

from flask import Flask, jsonify, request, Response
import queue
from colorama import init, Fore, Style

# Initialize colorama
init(autoreset=True)

import logging

class ColorRequestsFormatter(logging.Formatter):
    def format(self, record):
        msg = record.getMessage()
        if "POST /publish-log" in msg or "GET /get-logs" in msg:
            return Fore.LIGHTBLACK_EX + msg
        elif " 200 " in msg or " 201 " in msg:
            return Fore.GREEN + msg
        elif " 301 " in msg or " 302 " in msg or " 304 " in msg:
            return Fore.YELLOW + msg
        elif " 404 " in msg or " 400 " in msg:
            return Fore.RED + msg
        elif " 500 " in msg:
            return Fore.RED + Style.BRIGHT + msg
        return msg

log = logging.getLogger('werkzeug')
handler = logging.StreamHandler()
handler.setFormatter(ColorRequestsFormatter())
log.addHandler(handler)
log.propagate = False

# Imports from vm_agent folder
sys.path.append(os.path.dirname(os.path.abspath(__file__)))
from db import fetch_active_keywords, save_scraped_post, save_lead, update_keyword_scraped_time, get_connection, add_queue_task, fetch_task_by_id
from scraper import simulate_platform_scrape
from advisor import qualify_and_draft

import collections

app = Flask(__name__)
log_buffer = collections.deque(maxlen=1000)
log_counter = 0

@app.after_request
def add_cors_headers(response):
    response.headers['Access-Control-Allow-Origin'] = '*'
    response.headers['Access-Control-Allow-Headers'] = 'Content-Type,Authorization'
    response.headers['Access-Control-Allow-Methods'] = 'GET,POST,OPTIONS'
    return response

start_time = time.time()
last_run_time = None
last_run_stats = {}

@app.route("/status", methods=["GET"])
def status():
    uptime_seconds = time.time() - start_time
    uptime = str(datetime.timedelta(seconds=int(uptime_seconds)))
    
    # Check DB Connection
    db_conn = get_connection()
    db_status = "Connected" if db_conn else "Offline"
    
    # Get keyword counts
    keywords_count = 0
    try:
        keywords = fetch_active_keywords()
        keywords_count = len(keywords)
    except:
        pass
        
    return jsonify({
        "status": "healthy",
        "db_connection": db_status,
        "active_keywords_count": keywords_count,
        "uptime": uptime,
        "last_run_at": last_run_time,
        "last_run_stats": last_run_stats,
        "version": "1.0.0"
    })

@app.route("/trigger", methods=["POST"])
def trigger():
    global last_run_time, last_run_stats
    print(Fore.YELLOW + "\n[API-TRIGGER] Manual Scrape Trigger Received!")
    
    try:
        keywords = fetch_active_keywords()
        total_scraped = 0
        total_leads = 0
        
        # Process up to 2 active keywords to keep manual dashboard triggers fast and prevent API limits
        for kw in keywords[:2]:
            keyword_text = kw["keyword"]
            project_id = kw["project_id"]
            keyword_id = kw.get("id")
            
            for platform in ["reddit", "twitter", "linkedin"]:
                scraped_posts = simulate_platform_scrape(platform, keyword_text)
                for post in scraped_posts:
                    total_scraped += 1
                    post_id = save_scraped_post(
                        project_id=project_id,
                        platform=post["platform"],
                        external_id=post["external_id"],
                        title=post["title"],
                        content=post["content"],
                        author=post["author"],
                        url=post["url"]
                    )
                    
                    score, category, reply = qualify_and_draft(
                        post_content=post["content"],
                        author=post["author"],
                        platform=post["platform"],
                        project_pitch="our premium custom software development, AI chatbot integrations, and B2B marketing automation services.",
                        project_cta="https://lakshya.ai/consult"
                    )
                    
                    if score >= 70:
                        clean_author = "".join([c for c in post["author"] if c.isalnum() or c in '_-']).lower()
                        contact_email = f"{clean_author}_contact@example.com" if clean_author else "contact@example.com"
                        lead_id = save_lead(
                            post_id=post_id,
                            project_id=project_id,
                            contact_name=post["author"],
                            contact_email=contact_email,
                            score=score,
                            intent_category=category,
                            notes=f"Triggered qualification for match phrase: '{keyword_text}'",
                            generated_reply=reply
                        )
                        if lead_id:
                            total_leads += 1
                
                # Small delay to throttle requests
                time.sleep(1)
            
            if keyword_id:
                update_keyword_scraped_time(keyword_id)
        
        last_run_time = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        last_run_stats = {
            "posts_processed": total_scraped,
            "leads_found": total_leads
        }
        
        print(Fore.GREEN + f"[API-SUCCESS] Manual Scrape Completed! Scraped {total_scraped} posts, qualified {total_leads} leads.")
        return jsonify({
            "success": True,
            "message": "Scrape crawl cycle successfully executed.",
            "stats": last_run_stats
        })
        
    except Exception as e:
        import traceback
        tb_str = traceback.format_exc()
        print(Fore.RED + Style.BRIGHT + f"[API-ERROR] Scrape trigger failed: {e}")
        print(Fore.RED + tb_str)
        return jsonify({
            "success": False,
            "error": str(e),
            "traceback": tb_str
        }), 500

@app.route("/scrape-instant", methods=["POST"])
def scrape_instant():
    data = request.json or {}
    keyword = data.get("keyword")
    platform = data.get("platform", "reddit")
    project_id = data.get("project_id", 1)
    
    if not keyword:
        return jsonify({"success": False, "error": "Missing parameter 'keyword'"}), 400
        
    print(Fore.YELLOW + f"\n[API-INSTANT] Instant Scrape Request for keyword: '{keyword}' on platform: '{platform}'")
    
    try:
        scraped_posts = simulate_platform_scrape(platform, keyword)
        processed_posts = []
        leads_created = 0
        
        for post in scraped_posts:
            post_id = save_scraped_post(
                project_id=project_id,
                platform=post["platform"],
                external_id=post["external_id"],
                title=post["title"],
                content=post["content"],
                author=post["author"],
                url=post["url"]
            )
            
            score, category, reply = qualify_and_draft(
                post_content=post["content"],
                author=post["author"],
                platform=post["platform"],
                project_pitch="our SaaS lead qualification product solutions",
                project_cta="https://lakshya.ai"
            )
            
            is_lead = False
            lead_id = None
            if score >= 70:
                clean_author = "".join([c for c in post["author"] if c.isalnum() or c in '_-']).lower()
                contact_email = f"{clean_author}_contact@example.com"
                lead_id = save_lead(
                    post_id=post_id,
                    project_id=project_id,
                    contact_name=post["author"],
                    contact_email=contact_email,
                    score=score,
                    intent_category=category,
                    notes=f"Instant Scraped Lead for query: '{keyword}'",
                    generated_reply=reply
                )
                if lead_id:
                    leads_created += 1
                    is_lead = True
                    
            processed_posts.append({
                "author": post["author"],
                "content": post["content"],
                "score": score,
                "category": category,
                "is_qualified_lead": is_lead,
                "lead_id": lead_id
            })
            
        return jsonify({
            "success": True,
            "keyword": keyword,
            "platform": platform,
            "posts_processed": len(scraped_posts),
            "leads_qualified_count": leads_created,
            "details": processed_posts
        })
        
    except Exception as e:
        print(Fore.RED + Style.BRIGHT + f"[API-ERROR] Instant scrape failed: {e}")
        return jsonify({
            "success": False,
            "error": str(e)
        }), 500

@app.route("/queue-task", methods=["POST"])
def queue_task():
    data = request.json or {}
    client_id = data.get("client_id")
    task_type = data.get("task_type")
    payload = data.get("payload")
    
    if not task_type or not payload:
        return jsonify({"success": False, "error": "Missing parameters 'task_type' or 'payload'"}), 400
        
    print(Fore.CYAN + Style.BRIGHT + f"[API-QUEUE] Enqueuing new task of type: {task_type}")
    task_id = add_queue_task(client_id, task_type, payload)
    if task_id:
        return jsonify({
            "success": True,
            "task_id": task_id,
            "message": f"Task enqueued successfully with ID {task_id}"
        })
    else:
        return jsonify({"success": False, "error": "Failed to enqueue task in database"}), 500

@app.route("/task-status/<int:task_id>", methods=["GET"])
def task_status(task_id):
    task = fetch_task_by_id(task_id)
    if not task:
        return jsonify({"success": False, "error": f"Task with ID {task_id} not found"}), 404
        
    return jsonify({
        "success": True,
        "task_id": task["id"],
        "client_id": task["client_id"],
        "task_type": task["task_type"],
        "status": task["status"],
        "attempts": task["attempts"],
        "error_message": task["error_message"],
        "result_path": task["result_path"],
        "created_at": str(task["created_at"]),
        "updated_at": str(task["updated_at"])
    })


@app.route("/publish-log", methods=["POST"])
def publish_log():
    global log_counter
    data = request.json or {}
    message = data.get("message", "")
    if message:
        log_counter += 1
        log_entry = {
            "id": log_counter,
            "timestamp": datetime.datetime.now().strftime("%H:%M:%S"),
            "message": message
        }
        log_buffer.append(log_entry)
    return jsonify({"success": True})

@app.route("/get-logs", methods=["GET"])
def get_logs():
    last_id = request.args.get("last_id", default=0, type=int)
    if last_id == -1:
        return jsonify({
            "success": True,
            "logs": [],
            "last_id": log_counter
        })
    new_logs = [log for log in log_buffer if log["id"] > last_id]
    return jsonify({
        "success": True,
        "logs": new_logs,
        "last_id": log_counter
    })


if __name__ == "__main__":
    port = int(os.environ.get("PORT", 5000))
    print(Fore.CYAN + Style.BRIGHT + "==========================================================")
    print(Fore.MAGENTA + Style.BRIGHT + f"   [ ] LAKSHYA VM AGENT API - SERVER RUNNING ON PORT {port}   ")
    print(Fore.CYAN + Style.BRIGHT + "==========================================================")
    app.run(host="0.0.0.0", port=port, debug=True, use_reloader=False)
