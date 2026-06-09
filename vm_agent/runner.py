import time
import argparse
from db import fetch_active_keywords, save_scraped_post, save_lead, update_keyword_scraped_time
from scraper import simulate_platform_scrape
from advisor import qualify_and_draft

def run_agent_loop(single_run=False):
    print("==========================================================")
    print("     LAKSHYA AI AGENT RUNNER - BACKGROUND VM SERVICE      ")
    print("==========================================================")
    
    while True:
        print("\n[RUNNER] Querying active search campaigns from database...")
        keywords = fetch_active_keywords()
        print(f"[RUNNER] Found {len(keywords)} active tracking keywords.")
        
        for kw in keywords:
            keyword_text = kw["keyword"]
            project_id = kw["project_id"]
            keyword_id = kw.get("id")
            
            print(f"\n--- Processing Keyword Campaign: '{keyword_text}' ---")
            
            # 1. Scrape posts from Reddit, Twitter & LinkedIn
            for platform in ["reddit", "twitter", "linkedin"]:
                try:
                    scraped_posts = simulate_platform_scrape(platform, keyword_text)
                    
                    for post in scraped_posts:
                        # 2. Save post in DB
                        post_id = save_scraped_post(
                            project_id=project_id,
                            platform=post["platform"],
                            external_id=post["external_id"],
                            title=post["title"],
                            content=post["content"],
                            author=post["author"],
                            url=post["url"]
                        )
                        
                        if post_id == 999:
                            # Simulation fallback printout
                            print(f"[SIMULATOR] Scraped post context:\n - Author: {post['author']}\n - Content: {post['content']}")
                        
                        # 3. Analyze & qualify post with LLM advisor
                        score, category, reply = qualify_and_draft(
                            post_content=post["content"],
                            author=post["author"],
                            platform=post["platform"],
                            project_pitch="our premium custom software development, AI chatbot integrations, and B2B marketing automation services.",
                            project_cta="https://lakshya.ai/consult"
                        )
                        
                        print(f"[RUNNER] Lead Qualified! Score: {score}/100 | Category: {category}")
                        
                        # 4. Save as Lead if it meets minimum threshold
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
                                notes=f"Automated qualification for match phrase: '{keyword_text}'",
                                generated_reply=reply
                            )
                            if lead_id:
                                print(f"[SUCCESS] Lead registered in database with ID: {lead_id}")
                        else:
                            print(f"[RUNNER] Post by {post['author']} skipped (relevance score {score} is below threshold).")
                
                except Exception as e:
                    print(f"[ERROR] Scraping failed for {platform} under query '{keyword_text}': {e}")
            
            # Update keyword timestamp
            if keyword_id:
                update_keyword_scraped_time(keyword_id)
                
        if single_run:
            print("\n[RUNNER] Single run completed. Exiting.")
            break
            
        print("\n[RUNNER] Sleep for 30 seconds before next crawl cycle...")
        time.sleep(30)

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Lakshya VM Crawler Daemon")
    parser.add_argument("--single", action="store_true", help="Run once and exit")
    args = parser.parse_args()
    
    try:
        run_agent_loop(single_run=args.single)
    except KeyboardInterrupt:
        print("\n\n[RUNNER] Execution interrupted by user. Exiting cleanly...")
