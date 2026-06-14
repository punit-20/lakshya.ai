import os
import sys
import time
import traceback
import subprocess
from colorama import init, Fore, Style

# Initialize colorama
init(autoreset=True)

# Add current folder to path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))
from db import fetch_next_pending_task, update_task_status, reset_orphaned_tasks, update_post_image
from automation import SeleniumAutomation
from config import LARAVEL_PUBLIC_PATH

def process_task(task, automator):
    task_id = task["id"]
    task_type = task["task_type"]
    payload = task["payload"]
    attempts = task["attempts"]
    
    print(Fore.YELLOW + f"[WORKER] Processing Task ID {task_id} of type '{task_type}' (Attempt {attempts})...")
    
    # Resolve public storage directory for outputs using LARAVEL_PUBLIC_PATH configuration
    storage_dir = os.path.abspath(os.path.join(LARAVEL_PUBLIC_PATH, "storage", "ad_creatives"))
    os.makedirs(storage_dir, exist_ok=True)
    
    if task_type == "generate_image":
        prompt = payload.get("prompt")
        post_id = payload.get("post_id")
        if not prompt:
            raise Exception("Missing parameter 'prompt' in payload.")
            
        filename = f"creative_{task_id}_{int(time.time())}.jpg"
        save_path = os.path.join(storage_dir, filename)
        public_url = f"storage/ad_creatives/{filename}"
        
        # Execute Selenium image search & download
        automator.generate_image_lexica(prompt, save_path)
        
        # Update database with success
        update_task_status(task_id, "Completed", result_path=public_url)
        
        # If this task is linked to a post, update the post's image_url
        if post_id:
            update_post_image(post_id, public_url)
            
        print(Fore.GREEN + f"[WORKER] Task ID {task_id} COMPLETED. Output saved to {public_url}")
        
    elif task_type == "social_post":
        platform = payload.get("platform")
        content = payload.get("content")
        image_path = payload.get("image_path") # relative path /storage/...
        
        if not platform or not content:
            raise Exception("Missing platform or content in payload.")
            
        # Convert public URL to absolute file path on disk using LARAVEL_PUBLIC_PATH configuration
        abs_image_path = None
        if image_path:
            clean_path = image_path.lstrip("/")
            abs_image_path = os.path.abspath(os.path.join(LARAVEL_PUBLIC_PATH, clean_path))
            
        # Execute Selenium social post
        post_url = automator.post_to_social(platform, content, abs_image_path)
        
        update_task_status(task_id, "Completed", result_path=post_url)
        print(Fore.GREEN + f"[WORKER] Task ID {task_id} POSTED. Live post URL: {post_url}")
        
    else:
        raise Exception(f"Unsupported task type: '{task_type}'")

def cleanup_orphaned_chrome():
    print(Fore.BLUE + "[WORKER] Cleaning up orphaned headless Chrome and ChromeDriver processes...")
    try:
        # Kill all chromedriver processes
        os.system("taskkill /f /im chromedriver.exe >nul 2>&1")
        # Specially target and kill only Chrome instances running with --headless flag (keeps user's normal browser safe)
        ps_cmd = 'Get-CimInstance Win32_Process -Filter "name = \'chrome.exe\'" | Where-Object { $_.CommandLine -like "*--headless*" } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force }'
        subprocess.run(["powershell", "-Command", ps_cmd], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        print(Fore.BLUE + "[WORKER] Orphaned process cleanup complete.")
    except Exception as e:
        print(Fore.RED + f"[WORKER] Process cleanup encountered error: {e}")

def main():
    print(Fore.CYAN + Style.BRIGHT + "==========================================================")
    print(Fore.MAGENTA + Style.BRIGHT + "   [ ] LAKSHYA AUTOMATION WORKER - RUNNING DAEMON         ")
    print(Fore.CYAN + Style.BRIGHT + "==========================================================")
    
    # Run startup cleanup of orphaned browser processes
    cleanup_orphaned_chrome()
    
    # Recover any tasks left stuck in 'Processing' state due to a crash/VM reboot
    reset_orphaned_tasks()
    
    # Initialize a shared Selenium controller
    automator = SeleniumAutomation()
    task_counter = 0
    
    while True:
        try:
            task = fetch_next_pending_task()
            if task:
                task_id = task["id"]
                try:
                    process_task(task, automator)
                    task_counter += 1
                    
                    # Recycle Chrome every 30 tasks to clear browser memory bloat
                    if task_counter >= 30:
                        print(Fore.BLUE + "[WORKER] Memory safety recycling: Re-spawning Chrome driver...")
                        automator.close_driver()
                        task_counter = 0
                        
                except Exception as task_err:
                    tb = traceback.format_exc()
                    print(Fore.RED + f"[WORKER-ERROR] Task ID {task_id} failed: {task_err}")
                    print(Fore.LIGHTBLACK_EX + tb)
                    
                    # Retry logic (max 3 attempts)
                    attempts = task.get("attempts", 0)
                    if attempts < 3:
                        update_task_status(task_id, "Pending", error_message=str(task_err))
                        print(Fore.YELLOW + f"[WORKER-RETRY] Task ID {task_id} set back to Pending for retry (Attempt {attempts} of 3).")
                    else:
                        update_task_status(task_id, "Failed", error_message=str(task_err))
                        print(Fore.RED + f"[WORKER-FAILED] Task ID {task_id} exceeded retries and marked as Failed.")
            else:
                # No tasks, sleep to preserve CPU
                time.sleep(3)
                
        except KeyboardInterrupt:
            print(Fore.YELLOW + "\n[WORKER] Exiting worker daemon.")
            automator.close_driver()
            break
        except Exception as global_err:
            print(Fore.RED + f"[WORKER-CRASH] {global_err}")
            traceback.print_exc()
            time.sleep(5)

if __name__ == "__main__":
    main()
