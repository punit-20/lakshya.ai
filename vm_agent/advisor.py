import os
import re
from config import GEMINI_API_KEY

HAS_GEMINI_SDK = False
try:
    import google.generativeai as genai
    if GEMINI_API_KEY:
        genai.configure(api_key=GEMINI_API_KEY)
        HAS_GEMINI_SDK = True
except ImportError:
    HAS_GEMINI_SDK = False

def qualify_and_draft(post_content, author, platform, project_pitch="our product", project_cta="our website"):
    """
    Qualifies the lead score (0-100), extracts intent, and drafts a reply.
    Uses Gemini API if SDK is present and key is set. Otherwise, uses rules.
    """
    if HAS_GEMINI_SDK:
        try:
            print("[AI ADVISOR] Calling Gemini API for lead qualification...")
            model = genai.GenerativeModel('gemini-1.5-flash')
            prompt = f"""
            Analyze the following social media post for B2B lead generation suitability:
            Post: "{post_content}"
            Author: {author}
            Platform: {platform}
            
            We sell/offer: {project_pitch}
            Our Call to Action / Link is: {project_cta}
            
            Your response must be formatted as exactly 3 lines:
            Line 1: Score (an integer from 0 to 100 representing sales qualification fit)
            Line 2: Intent Category (2-5 words e.g. "High Intent - Roast Request")
            Line 3: Draft Reply Pitch (A personalized, helpful, slightly informal reply pitching our offering and referencing the CTA link)
            """
            response = model.generate_content(prompt)
            lines = [line.strip() for line in response.text.split("\n") if line.strip()]
            if len(lines) >= 3:
                try:
                    score_str = re.sub(r"\D", "", lines[0])
                    score = int(score_str) if score_str else 50
                except ValueError:
                    score = 50
                category = lines[1]
                reply = "\n".join(lines[2:])
                return score, category, reply
        except Exception as e:
            print(f"[AI ADVISOR WARNING] Gemini API call failed: {e}. Falling back to rule engine...")
            
    # Fallback Rule/Heuristic Engine
    print("[AI ADVISOR] Running heuristic NLP classifier...")
    score = 50
    category = "General Match"
    
    # Calculate score based on keyword signals
    content_lower = post_content.lower()
    signals = {
        "roast": 25,
        "feedback": 20,
        "audit": 20,
        "conversions": 15,
        "bounce": 10,
        "help": 10,
        "redesign": 10
    }
    
    for word, weight in signals.items():
        if word in content_lower:
            score += weight
            
    score = min(score, 99)  # cap fallback at 99
    
    if score >= 85:
        category = "High Intent - Direct request"
    elif score >= 70:
        category = "Medium Intent - Problem identified"
    else:
        category = "Low Intent - General advice request"
        
    reply = f"""Hey {author}! 

It looks like you are searching for custom software developer recommendations or AI chatbot setups.

Check out our team. {project_pitch} 

We build customized workflows and integrations. You can book a free consultation call here: {project_cta}

Hope this helps!"""

    return score, category, reply
