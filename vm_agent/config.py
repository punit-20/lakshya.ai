import os

def load_env(env_path="../.env"):
    config = {
        "DB_HOST": "127.0.0.1",
        "DB_PORT": "3306",
        "DB_DATABASE": "lakshya",
        "DB_USERNAME": "root",
        "DB_PASSWORD": "",
        "GEMINI_API_KEY": ""
    }
    
    # Try finding .env file in current or parent directory
    paths = [env_path, ".env", "../.env", "../../.env"]
    found_path = None
    for p in paths:
        if os.path.exists(p):
            found_path = p
            break
            
    if found_path:
        with open(found_path, "r") as f:
            for line in f:
                line = line.strip()
                if not line or line.startswith("#") or "=" not in line:
                    continue
                key, val = line.split("=", 1)
                key = key.strip()
                val = val.strip().strip('"').strip("'")
                config[key] = val
                
    return config

# Load settings
env_config = load_env()

DB_HOST = env_config.get("DB_HOST", "127.0.0.1")
DB_PORT = int(env_config.get("DB_PORT", "3306"))
DB_DATABASE = env_config.get("DB_DATABASE", "lakshya")
DB_USERNAME = env_config.get("DB_USERNAME", "root")
DB_PASSWORD = env_config.get("DB_PASSWORD", "")
GEMINI_API_KEY = env_config.get("GEMINI_API_KEY", "")
LARAVEL_PUBLIC_PATH = env_config.get("LARAVEL_PUBLIC_PATH", "../marketing/public")
REDDIT_CLIENT_ID = env_config.get("REDDIT_CLIENT_ID", "")
REDDIT_CLIENT_SECRET = env_config.get("REDDIT_CLIENT_SECRET", "")
