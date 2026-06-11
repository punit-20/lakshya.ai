import sys
import os
import warnings

# Suppress deprecation and future warnings for clean output
warnings.filterwarnings("ignore", category=FutureWarning)
warnings.filterwarnings("ignore", category=DeprecationWarning)

# Reconfigure stdout/stderr to UTF-8 to prevent Unicode encoding issues (for emojis) on Windows Terminal
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

# Add parent directory to path so we can import config
sys.path.append(os.path.dirname(os.path.abspath(__file__)))
from config import GEMINI_API_KEY

def test_old_sdk():
    print("\n--- Testing OLD SDK (google-generativeai) ---")
    try:
        import google.generativeai as old_genai
        old_genai.configure(api_key=GEMINI_API_KEY)
        model = old_genai.GenerativeModel('gemini-1.5-flash')
        print("[INFO] Sending request using old SDK...")
        response = model.generate_content('Say "Old SDK works!"')
        print(f"[SUCCESS] Response: {response.text.strip()}")
        return True
    except Exception as e:
        print(f"❌ [ERROR] Old SDK failed: {e}")
        return False

def test_new_sdk():
    print("\n--- Testing NEW SDK (google-genai) ---")
    try:
        from google import genai
        client = genai.Client(api_key=GEMINI_API_KEY)
        print("[INFO] Sending request using new SDK (gemini-1.5-flash)...")
        response = client.models.generate_content(
            model='gemini-1.5-flash',
            contents='Say "New SDK works!"',
        )
        print(f"[SUCCESS] Response: {response.text.strip()}")
        return True
    except Exception as e:
        print(f"❌ [ERROR] New SDK (gemini-1.5-flash) failed: {e}")
        
    try:
        from google import genai
        client = genai.Client(api_key=GEMINI_API_KEY)
        print("[INFO] Trying new SDK with newer model (gemini-2.5-flash)...")
        response = client.models.generate_content(
            model='gemini-2.5-flash',
            contents='Say "New SDK with 2.5 works!"',
        )
        print(f"[SUCCESS] Response: {response.text.strip()}")
        return True
    except Exception as e:
        print(f"❌ [ERROR] New SDK (gemini-2.5-flash) failed: {e}")
        return False

if __name__ == "__main__":
    print("==============================================")
    print("      GEMINI API DIAGNOSTICS & TEST RUN       ")
    print("==============================================")
    print(f"API Key: {GEMINI_API_KEY[:6]}...{GEMINI_API_KEY[-4:] if len(GEMINI_API_KEY) > 10 else ''}")
    
    if not GEMINI_API_KEY:
        print("\n⚠️ [WARNING] GEMINI_API_KEY is not set in config.")
        sys.exit(1)
        
    old_ok = test_old_sdk()
    new_ok = test_new_sdk()
    
    print("\n================ Conclusion ================")
    if old_ok and new_ok:
        print("Both SDKs are working correctly!")
    elif old_ok:
        print("Only the OLD SDK (google-generativeai) is working.")
    elif new_ok:
        print("Only the NEW SDK (google-genai) is working.")
    else:
        print("Both SDKs failed. The API key might not be fully active yet or is restricted.")

