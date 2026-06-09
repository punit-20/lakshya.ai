import time
import random
from colorama import Fore, Style

# List of typical User Agents to rotate
USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2.1 Safari/605.1.15",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0"
]

# Simulated post pool for keywords
REDDIT_TEMPLATES = [
    {
        "content": "Hey guys, need blunt feedback on my landing page: https://saastester.io. The conversion rate is currently 0.5%. What copy changes should I do?",
        "author": "u/saas_hustler_9",
        "title": "Blunt landing page roast requested",
        "platform": "reddit"
    },
    {
        "content": "I am struggling with landing page conversions. We get 100 signups but 0 paying users. Can someone audit our design? Site: https://metricly.com.",
        "author": "u/dev_founder_bob",
        "title": "Why are my landing page signups not converting to sales?",
        "platform": "reddit"
    },
    {
        "content": "Is there any software that reviews website copywriting automatically? Like an AI that roasts the UX and text? Looking for recommendations.",
        "author": "u/growth_seeker",
        "title": "Automated website audit tools?",
        "platform": "reddit"
    }
]

TWITTER_TEMPLATES = [
    {
        "content": "Our new sales landing page looks great but converts like trash (0.7%). Need UX advice or a brutal website roast.",
        "author": "@startup_carol",
        "title": None,
        "platform": "twitter"
    },
    {
        "content": "Just launched our feedback tool! Looking for a harsh critic to roast our landing page structure. Link in bio.",
        "author": "@indie_maker_dan",
        "title": None,
        "platform": "twitter"
    }
]

LINKEDIN_TEMPLATES = [
    {
        "content": "We are looking to optimize our B2B landing page conversions. Currently seeing very high bounce rates. Can anyone recommend a landing page roast expert or tool?",
        "author": "john_doe_marketing",
        "title": "B2B Conversion Rate Optimization",
        "platform": "linkedin"
    },
    {
        "content": "Need an expert to audit our SaaS product homepage copy. Willing to pay for a detailed UX and copywriting roast.",
        "author": "sara_connor_saas",
        "title": "SaaS Homepage Copy Audit",
        "platform": "linkedin"
    }
]

def simulate_platform_scrape(platform, keyword):
    """
    Simulates crawling a platform with rotated user agents and randomized request delays.
    Returns list of posts matching keyword context.
    """
    ua = random.choice(USER_AGENTS)
    delay = random.uniform(1.2, 2.8)
    
    print(Fore.BLUE + f"🌐 [SCRAPER] Initializing browser profile for {platform}...")
    print(Fore.LIGHTBLACK_EX + f"🕵️ [SCRAPER] User-Agent: {ua}")
    print(Fore.BLUE + f"🕷 [SCRAPER] Crawling for search query: '{keyword}'...")
    time.sleep(delay)  # simulate loading delay
    
    posts = []
    
    if platform == "reddit":
        templates = REDDIT_TEMPLATES
    elif platform == "twitter":
        templates = TWITTER_TEMPLATES
    elif platform == "linkedin":
        templates = LINKEDIN_TEMPLATES
    else:
        templates = REDDIT_TEMPLATES + TWITTER_TEMPLATES + LINKEDIN_TEMPLATES
        
    # Pick a random template and add keyword markers to simulate search result matches
    template = random.choice(templates)
    
    # Randomize ID to avoid collision
    ext_id = f"sim_{platform}_{random.randint(100000, 999999)}"
    
    post = {
        "platform": platform,
        "external_id": ext_id,
        "title": template["title"],
        "content": template["content"].replace("landing page", f"landing page ({keyword})"),
        "author": template["author"],
        "url": f"https://{platform}.com/status/{ext_id}" if platform == 'twitter' else (f"https://linkedin.com/feed/update/{ext_id}" if platform == 'linkedin' else f"https://reddit.com/r/SaaS/{ext_id}")
    }
    
    posts.append(post)
    print(Fore.GREEN + f"📥 [SCRAPER] Scraped 1 matched post from {platform} by user {post['author']}")
    return posts
