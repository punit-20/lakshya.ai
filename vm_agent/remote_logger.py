import sys
import re
import json
import urllib.request
import threading

class RemoteLogStream:
    def __init__(self, original_stdout):
        self.original_stdout = original_stdout
        # Regular expression to remove terminal colors (ANSI codes)
        self.ansi_escape = re.compile(r'\x1B(?:[@-Z\\-_]|\[[0-?]*[ -/]*[@-~])')
        
    def write(self, message):
        self.original_stdout.write(message)
        clean_msg = message.strip()
        if clean_msg:
            plain_msg = self.ansi_escape.sub('', clean_msg)
            # Post log asynchronously to the Flask API
            threading.Thread(target=self._send_log, args=(plain_msg,), daemon=True).start()
            
    def _send_log(self, message):
        try:
            data = json.dumps({"message": message}).encode('utf-8')
            req = urllib.request.Request(
                "http://127.0.0.1:5000/publish-log",
                data=data,
                headers={'Content-Type': 'application/json'}
            )
            # Use short timeout to prevent blocking
            with urllib.request.urlopen(req, timeout=0.5) as response:
                response.read()
        except:
            pass
            
    def flush(self):
        self.original_stdout.flush()

def setup_remote_logging():
    # Wrap standard output to capture prints
    sys.stdout = RemoteLogStream(sys.stdout)
