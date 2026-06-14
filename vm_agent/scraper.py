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

import urllib.request
import urllib.parse
import xml.etree.ElementTree as ET
import re
import html
import json
import base64
from config import REDDIT_CLIENT_ID, REDDIT_CLIENT_SECRET

def clean_html(raw_html):
    """
    Remove HTML tags and unescape HTML characters to get clean text.
    """
    if not raw_html:
        return ""
    # Remove HTML tags
    cleanr = re.compile('<.*?>')
    cleantext = re.sub(cleanr, '', raw_html)
    # Unescape HTML entities like &amp; &lt;
    return html.unescape(cleantext).strip()

def scrape_reddit_api(keyword, client_id, client_secret):
    """
    Queries Reddit's search endpoint using client ID and client secret (OAuth).
    Bypasses standard 429 rate limit blocks securely.
    """
    print(Fore.BLUE + f"[SCRAPER] Authenticating and crawling Reddit API for query: '{keyword}'...")
    
    auth_url = "https://www.reddit.com/api/v1/access_token"
    auth_header = base64.b64encode(f"{client_id}:{client_secret}".encode("utf-8")).decode("utf-8")
    
    data = urllib.parse.urlencode({"grant_type": "client_credentials"}).encode("utf-8")
    req = urllib.request.Request(
        auth_url,
        data=data,
        headers={
            "Authorization": f"Basic {auth_header}",
            "User-Agent": "python:lakshya.leadgen.agent:v1.0 (by /u/lakshya_dev)"
        }
    )
    
    try:
        # Request access token
        with urllib.request.urlopen(req, timeout=10) as response:
            token_data = json.loads(response.read().decode("utf-8"))
            access_token = token_data.get("access_token")
            
        if not access_token:
            raise Exception("Access token was not returned by Reddit.")
            
        # Execute query
        encoded_query = urllib.parse.quote(keyword)
        search_url = f"https://oauth.reddit.com/search.json?q={encoded_query}&sort=new&limit=5"
        
        req = urllib.request.Request(
            search_url,
            headers={
                "Authorization": f"Bearer {access_token}",
                "User-Agent": "python:lakshya.leadgen.agent:v1.0 (by /u/lakshya_dev)"
            }
        )
        
        with urllib.request.urlopen(req, timeout=10) as response:
            result = json.loads(response.read().decode("utf-8"))
            
        posts = []
        children = result.get("data", {}).get("children", [])
        for child in children:
            data = child.get("data", {})
            title = data.get("title", "")
            content = data.get("selftext", "")
            author = f"u/{data.get('author', 'anonymous')}"
            ext_id = data.get("name") # e.g. t3_xxxx
            post_url = f"https://reddit.com{data.get('permalink')}"
            
            if not content.strip():
                content = title
                
            posts.append({
                "platform": "reddit",
                "external_id": ext_id,
                "title": title,
                "content": content,
                "author": author,
                "url": post_url
            })
            
        print(Fore.GREEN + f"[SCRAPER] Successfully fetched {len(posts)} posts from Reddit API.")
        return posts[:3]
        
    except Exception as e:
        print(Fore.RED + f"[SCRAPER ERROR] Reddit API fetch failed: {e}")
        return []

def scrape_reddit_rss(keyword):
    """
    Queries Reddit's search RSS feed for a keyword, parses the XML response,
    and returns a list of matching posts.
    """
    print(Fore.BLUE + f"[SCRAPER] Querying live Reddit RSS feed for query: '{keyword}'...")
    
    encoded_query = urllib.parse.quote(keyword)
    # Fetch posts sorted by new to get real-time leads
    url = f"https://www.reddit.com/search.rss?q={encoded_query}&sort=new"
    
    req = urllib.request.Request(
        url,
        headers={
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
            "Accept": "application/xml,text/xml,application/xhtml+xml,text/html;q=0.9,*/*;q=0.8",
            "Accept-Language": "en-US,en;q=0.5",
            "Accept-Encoding": "identity"
        }
    )
    
    posts = []
    try:
        with urllib.request.urlopen(req, timeout=10) as response:
            xml_data = response.read()
            
            # Parse XML feed
            root = ET.fromstring(xml_data)
            
            # XML namespace map for Atom feed structure (Reddit RSS is Atom format)
            ns = {'atom': 'http://www.w3.org/2005/Atom'}
            
            for entry in root.findall('atom:entry', ns):
                title_elem = entry.find('atom:title', ns)
                author_elem = entry.find('atom:author/atom:name', ns)
                content_elem = entry.find('atom:content', ns)
                id_elem = entry.find('atom:id', ns)
                link_elem = entry.find('atom:link', ns)
                
                title = title_elem.text if title_elem is not None else ""
                author = author_elem.text if author_elem is not None else "anonymous"
                raw_content = content_elem.text if content_elem is not None else ""
                ext_id = id_elem.text if id_elem is not None else f"reddit_{random.randint(100000, 999999)}"
                # Link is in link attribute 'href'
                post_url = link_elem.attrib.get('href') if link_elem is not None else f"https://reddit.com"
                
                # Extract clean text from HTML content
                clean_content = clean_html(raw_content)
                # If content is empty (e.g. title-only post), fallback to title
                if not clean_content.strip():
                    clean_content = title
                
                posts.append({
                    "platform": "reddit",
                    "external_id": ext_id,
                    "title": title,
                    "content": clean_content,
                    "author": author,
                    "url": post_url
                })
                
        print(Fore.GREEN + f"[SCRAPER] Successfully fetched {len(posts)} posts from Reddit RSS.")
        # Limit to top 3 recent posts to respect API rate limits
        return posts[:3]
        
    except Exception as e:
        print(Fore.RED + f"[SCRAPER ERROR] Reddit RSS scrape failed: {e}")
        return []

def scrape_ddg_search_posts(site_domain, keyword):
    """
    Queries DuckDuckGo's static HTML search page to fetch indexed pages matching a query
    without triggering JavaScript requirements or bot-detection screens.
    """
    query = f"site:{site_domain} {keyword}"
    url = "https://html.duckduckgo.com/html/"
    data = urllib.parse.urlencode({"q": query}).encode("utf-8")
    
    ua = random.choice(USER_AGENTS)
    headers = {
        "User-Agent": ua,
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Accept-Language": "en-US,en;q=0.5",
        "Content-Type": "application/x-www-form-urlencoded"
    }
    
    posts = []
    try:
        req = urllib.request.Request(url, data=data, headers=headers, method="POST")
        with urllib.request.urlopen(req, timeout=10) as response:
            html_content = response.read().decode("utf-8", errors="ignore")
            
        parts = html_content.split('class="result results_links results_links_deep web-result ')
        if len(parts) <= 1:
            return [] # No results or rate limited
            
        for p in parts[1:]:
            link_match = re.search(r'<a[^>]*class="result__a"[^>]*href="([^"]*)"[^>]*>(.*?)</a>', p, re.DOTALL)
            if not link_match:
                continue
                
            raw_url = link_match.group(1)
            title = re.sub('<[^>]*>', '', link_match.group(2)).strip()
            title = html.unescape(title)
            
            snippet = ""
            snippet_match = re.search(r'<a[^>]*class="result__snippet"[^>]*>(.*?)</a>', p, re.DOTALL)
            if snippet_match:
                snippet = re.sub('<[^>]*>', '', snippet_match.group(1)).strip()
                snippet = html.unescape(snippet)
                
            # Clean redirect URL
            post_url = raw_url
            if "uddg=" in raw_url:
                parsed_url = urllib.parse.urlparse(raw_url)
                query_params = urllib.parse.parse_qs(parsed_url.query)
                if "uddg" in query_params:
                    post_url = query_params["uddg"][0]
            elif raw_url.startswith("//"):
                post_url = "https:" + raw_url
                
            # Parse author username
            author = "anonymous"
            if "twitter.com" in post_url:
                parts_url = post_url.split("twitter.com/")
                if len(parts_url) > 1:
                    subparts = parts_url[1].split("/")
                    if len(subparts) > 0 and subparts[0] not in ["search", "hashtag", "status"]:
                        author = f"@{subparts[0]}"
            elif "linkedin.com" in post_url:
                parts_url = post_url.split("linkedin.com/in/")
                if len(parts_url) > 1:
                    subparts = parts_url[1].split("/")
                    author = subparts[0]
                else:
                    parts_url = post_url.split("linkedin.com/posts/")
                    if len(parts_url) > 1:
                        subparts = parts_url[1].split("-")
                        author = subparts[0] if subparts else "linkedin_user"
            
            # Verify if this URL is likely to be a post/status or profile
            is_valid_post = False
            if "twitter.com" in site_domain:
                if "status/" in post_url or len(post_url.split("/")) > 4:
                    is_valid_post = True
            else:
                if "posts/" in post_url or "/in/" in post_url or len(post_url.split("/")) > 4:
                    is_valid_post = True
                    
            if is_valid_post:
                ext_id = f"ddg_{site_domain.split('.')[0]}_{random.randint(100000, 999999)}"
                posts.append({
                    "platform": "twitter" if "twitter" in site_domain else "linkedin",
                    "external_id": ext_id,
                    "title": title,
                    "content": snippet or title,
                    "author": author,
                    "url": post_url
                })
                
        print(Fore.GREEN + f"[SCRAPER] Successfully parsed {len(posts)} live posts from DuckDuckGo for {site_domain}.")
        return posts[:3] # Limit to 3 posts
        
    except Exception as e:
        print(Fore.RED + f"[SCRAPER ERROR] DuckDuckGo crawl failed: {e}")
        return []

def simulate_platform_scrape(platform, keyword):
    """
    Crawls a platform. Checks for live channels (Reddit API/RSS, Twitter/LinkedIn DDG indices) first,
    and falls back cleanly to simulated templates on error or rate-limiting.
    """
    if platform == "reddit":
        # 1. Try official API first if credentials configured
        if REDDIT_CLIENT_ID and REDDIT_CLIENT_SECRET:
            api_posts = scrape_reddit_api(keyword, REDDIT_CLIENT_ID, REDDIT_CLIENT_SECRET)
            if api_posts:
                return api_posts
                
        # 2. Fall back to live RSS query
        live_posts = scrape_reddit_rss(keyword)
        if live_posts:
            return live_posts
            
        print(Fore.YELLOW + "[SCRAPER] All live Reddit scrape channels failed/rate-limited. Falling back to simulation.")
        
    elif platform == "twitter":
        # 1. Try live search crawling via DuckDuckGo first
        live_posts = scrape_ddg_search_posts("twitter.com", keyword)
        if live_posts:
            return live_posts
        print(Fore.YELLOW + "[SCRAPER] Live Twitter search failed or was rate-limited. Falling back to simulation.")
        
    elif platform == "linkedin":
        # 1. Try live search crawling via DuckDuckGo first
        live_posts = scrape_ddg_search_posts("linkedin.com", keyword)
        if live_posts:
            return live_posts
        print(Fore.YELLOW + "[SCRAPER] Live LinkedIn search failed or was rate-limited. Falling back to simulation.")
        
    # Simulation Fallback
    ua = random.choice(USER_AGENTS)
    delay = random.uniform(1.2, 2.8)
    
    print(Fore.BLUE + f"[SCRAPER] Initializing browser profile for {platform} (Simulation fallback)...")
    print(Fore.LIGHTBLACK_EX + f"[SCRAPER-UA] User-Agent: {ua}")
    print(Fore.BLUE + f"[SCRAPER] Crawling for search query: '{keyword}'...")
    time.sleep(delay)
    
    posts = []
    if platform == "reddit":
        templates = REDDIT_TEMPLATES
    elif platform == "twitter":
        templates = TWITTER_TEMPLATES
    elif platform == "linkedin":
        templates = LINKEDIN_TEMPLATES
    else:
        templates = REDDIT_TEMPLATES + TWITTER_TEMPLATES + LINKEDIN_TEMPLATES
        
    template = random.choice(templates)
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
    print(Fore.GREEN + f"[SCRAPER] Scraped 1 matched post from {platform} by user {post['author']} (Simulated)")
    return posts
