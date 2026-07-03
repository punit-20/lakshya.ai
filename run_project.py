import os
import sys
import subprocess
import threading
import time
import socket
import signal

# Detect OS
IS_WINDOWS = sys.platform.startswith('win')

# Configure UTF-8 on Windows to display emojis and special characters correctly
if IS_WINDOWS:
    os.system('chcp 65001 >nul 2>&1')
    if hasattr(sys.stdout, 'reconfigure'):
        try:
            sys.stdout.reconfigure(encoding='utf-8')
        except:
            pass
    if hasattr(sys.stderr, 'reconfigure'):
        try:
            sys.stderr.reconfigure(encoding='utf-8')
        except:
            pass

# Color codes for terminal output
class Colors:
    HEADER = '\033[95m'
    BLUE = '\033[94m'
    CYAN = '\033[96m'
    GREEN = '\033[92m'
    YELLOW = '\033[93m'
    RED = '\033[91m'
    MAGENTA = '\033[95m'
    WHITE = '\033[97m'
    GRAY = '\033[90m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'

    # Semantic Styles
    INFO = '\033[96m'     # Cyan
    SUCCESS = '\033[92m'  # Green
    FAILED = '\033[91m'   # Red
    WARNING = '\033[93m'  # Yellow

def typewriter_print(text, color=Colors.WHITE, delay=0.012):
    for char in text:
        sys.stdout.write(color + char + Colors.ENDC)
        sys.stdout.flush()
        time.sleep(delay)
    sys.stdout.write("\n")
    sys.stdout.flush()

def print_banner():
    typewriter_print("==================================================================", Colors.HEADER, 0.003)
    typewriter_print("        🚀 LAKSHYA.AI - UNIFIED PROJECT RUNNER (CROSS-PLATFORM)   ", Colors.HEADER + Colors.BOLD, 0.008)
    typewriter_print("==================================================================", Colors.HEADER, 0.003)

def is_port_in_use(port):
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.settimeout(0.5)
        return s.connect_ex(('127.0.0.1', port)) == 0

def check_dependencies():
    # Check ports 8000 and 5000
    if is_port_in_use(8000):
        typewriter_print("[WARNING] Port 8000 is already in use. Laravel server might fail to start.", Colors.WARNING, 0.005)
    if is_port_in_use(5000):
        typewriter_print("[WARNING] Port 5000 is already in use. VM Agent Flask server might fail to start.", Colors.WARNING, 0.005)

# Resolve PHP executable path
def get_php_executable():
    if IS_WINDOWS:
        xampp_php = "C:\\xampp\\php\\php.exe"
        if os.path.exists(xampp_php):
            return xampp_php
    return "php"

# Resolve python command
PYTHON_BIN = sys.executable if sys.executable else "python"

processes = []

def log_stream(stream, prefix, color):
    try:
        while True:
            line = stream.readline()
            if not line:
                break
            if isinstance(line, bytes):
                line = line.decode('utf-8', errors='replace')
            line_str = line.rstrip()
            if line_str:
                # Format prefix with clean color
                print(f"{color}[{prefix}]{Colors.ENDC} {line_str}")
    except Exception as e:
        pass

def run_service(cmd, prefix, color, cwd=None):
    try:
        # Use shell=True for Windows, configure explicit UTF-8 decoding to fix emojis/arrows
        creationflags = 0
        if IS_WINDOWS:
            creationflags = subprocess.CREATE_NEW_PROCESS_GROUP
            
        p = subprocess.Popen(
            cmd,
            stdin=subprocess.DEVNULL,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            shell=IS_WINDOWS,
            cwd=cwd,
            text=True,
            encoding='utf-8',
            errors='replace',
            bufsize=1,
            universal_newlines=True,
            creationflags=creationflags
        )
        processes.append((p, prefix))
        
        # Start threads to read stdout and stderr
        t1 = threading.Thread(target=log_stream, args=(p.stdout, prefix, color), daemon=True)
        t2 = threading.Thread(target=log_stream, args=(p.stderr, prefix, color), daemon=True)
        t1.start()
        t2.start()
        return p
    except Exception as e:
        typewriter_print(f"[FAILED] Failed to start service '{prefix}': {e}", Colors.FAILED, 0.008)
        return None

def kill_process_tree(p, name):
    if IS_WINDOWS:
        try:
            subprocess.run(["taskkill", "/F", "/T", "/PID", str(p.pid)], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        except Exception:
            pass
    else:
        try:
            os.killpg(os.getpgid(p.pid), signal.SIGTERM)
        except Exception:
            try:
                p.terminate()
            except Exception:
                pass

def shutdown_handler(sig, frame):
    print("")
    typewriter_print("[SHUTDOWN] Ctrl+C detected. Stopping all services cleanly...", Colors.WARNING, 0.008)
    for p, name in processes:
        print(f"Stopping {name}...")
        kill_process_tree(p, name)
    typewriter_print("[SUCCESS] All services stopped. Goodbye!", Colors.SUCCESS, 0.008)
    sys.exit(0)

def open_browser_when_ready(url, port):
    start_wait = time.time()
    while time.time() - start_wait < 15:
        if is_port_in_use(port):
            time.sleep(1.5)
            print("")
            typewriter_print(f"[SUCCESS] Server is online! Opening browser to: {url}", Colors.SUCCESS + Colors.BOLD, 0.01)
            print("")
            import webbrowser
            try:
                webbrowser.open(url)
            except Exception as e:
                typewriter_print(f"[WARNING] Failed to open browser automatically: {e}", Colors.WARNING, 0.01)
            return
        time.sleep(0.5)
    typewriter_print(f"[FAILED] Port {port} did not become active within 15 seconds.", Colors.FAILED, 0.01)

def main():
    print_banner()
    check_dependencies()
    
    # Register Ctrl+C handler
    signal.signal(signal.SIGINT, shutdown_handler)
    signal.signal(signal.SIGTERM, shutdown_handler)
    
    php_path = get_php_executable()
    typewriter_print("System details:", Colors.INFO, 0.005)
    typewriter_print(f" - Operating System: {'Windows' if IS_WINDOWS else 'Linux/Android/macOS'}", Colors.WHITE, 0.003)
    typewriter_print(f" - PHP Command:      {php_path}", Colors.WHITE, 0.003)
    typewriter_print(f" - Python Command:   {PYTHON_BIN}", Colors.WHITE, 0.003)
    typewriter_print(f" - Project Directory: {os.getcwd()}", Colors.WHITE, 0.003)
    print("-" * 66)
    time.sleep(0.2)
    
    typewriter_print("Starting Lakshya.ai services concurrently...", Colors.INFO, 0.008)
    print("")

    # Start Laravel web server
    run_service([php_path, "artisan", "serve", "--port=8000"], "Laravel-Server", Colors.BLUE)
    
    # Start Laravel queue listener
    run_service([php_path, "artisan", "queue:listen", "--tries=1", "--timeout=0"], "Laravel-Queue", Colors.MAGENTA)
    
    # Start npm run dev (Vite)
    run_service(["npm", "run", "dev"], "Vite-Assets", Colors.YELLOW)
    
    # Start Python VM Agent Flask API
    run_service([PYTHON_BIN, "vm_agent/api.py"], "VM-Agent-API", Colors.GREEN)
    
    # Start Python VM Agent Scraper Runner
    run_service([PYTHON_BIN, "vm_agent/runner.py"], "VM-Agent-Runner", Colors.CYAN)
    
    # Start Python VM Agent Automation Worker
    run_service([PYTHON_BIN, "vm_agent/worker.py"], "VM-Agent-Worker", Colors.RED)
    
    # Start Python VM Agent Task Execution Worker
    run_service([PYTHON_BIN, "vm_agent/agent_worker.py"], "VM-Agent-AI-Worker", Colors.WHITE)
    
    time.sleep(1.0)
    typewriter_print("[SUCCESS] All services launched! Press Ctrl+C to terminate.", Colors.SUCCESS + Colors.BOLD, 0.008)
    print("")

    # Start browser auto-opening thread in the background
    browser_thread = threading.Thread(
        target=open_browser_when_ready, 
        args=("http://localhost:8000/admin/dashboard", 8000), 
        daemon=True
    )
    browser_thread.start()
    
    # Keep main thread alive
    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        shutdown_handler(None, None)

if __name__ == "__main__":
    main()
