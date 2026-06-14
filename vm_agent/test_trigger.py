import sys
import os
import traceback

sys.path.append(os.path.dirname(os.path.abspath(__file__)))
from db import fetch_active_keywords, save_scraped_post, save_lead, update_keyword_scraped_time
from scraper import simulate_platform_scrape
from advisor import qualify_and_draft

print("1. Fetching active keywords...")
try:
    keywords = fetch_active_keywords()
    print(f"Keywords fetched: {keywords}")
except Exception as e:
    print("Failed to fetch active keywords:")
    traceback.print_exc()
    sys.exit(1)

print("\n2. Simulating scrape...")
try:
    total_scraped = 0
    total_leads = 0
    
    for kw in keywords:
        keyword_text = kw["keyword"]
        project_id = kw["project_id"]
        keyword_id = kw.get("id")
        
        print(f"\nProcessing keyword: '{keyword_text}' for project: {project_id}")
        for platform in ["reddit", "twitter", "linkedin"]:
            print(f"Scraping platform: {platform}")
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
                print(f"Saved post ID: {post_id}")
                
                score, category, reply = qualify_and_draft(
                    post_content=post["content"],
                    author=post["author"],
                    platform=post["platform"],
                    project_pitch="our premium custom software development, AI chatbot integrations, and B2B marketing automation services.",
                    project_cta="https://saastester.io"
                )
                print(f"Qualify result: Score={score}, Category={category}")
                
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
                    print(f"Saved lead ID: {lead_id}")
                    if lead_id:
                        total_leads += 1
except Exception as e:
    print("Exception occurred during trigger logic:")
    traceback.print_exc()
