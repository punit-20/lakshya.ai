# Lakshya.ai — AI Lead Generation & Outreach Automation

Lakshya.ai is a state-of-the-art AI-driven lead generation and outreach automation platform. It scrapes social platforms (Reddit, Twitter, LinkedIn) for active discussions based on targeting keywords, qualifies intent scores using LLM reasoning (Gemini API / Heuristics), and drafts personalized outreach pitches to maximize B2B customer conversions.

## Features

- **AI Lead Qualification**: Leverages the Gemini API to analyze sales intent (0-100 score) and categorize potential buyers.
- **Outreach Automation**: Generates tailored response drafts referencing context from the user's posts.
- **CRM Kanban Board**: Intuitively transition leads across states from `New`, `Discovered`, `Contacted`, `Qualified`, to `Closed`.
- **Campaign Keywords & Accounts**: Manage keyword campaigns and schedule crawlers.
- **VM Agent Crawler Service**: Background daemon (`vm_agent`) that automates periodic data scraping, classification, and CRM sync.
- **Dynamic Quotas & Billing**: Monitor monthly usage and tier limits with dynamic gauges.

## Project Structure

```
├── app/                      # Backend Controllers, Models, Traits
├── config/                   # System config files
├── database/                 # SQLite database & migrations
├── public/                   # CSS, JS, compiled assets
├── resources/views/          # Blade template layouts & views
├── routes/                   # Routing definitions (web.php)
├── vm_agent/                 # Background crawler service daemon
```

## Getting Started

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Setup Environment**
   Copy `.env.example` to `.env` and set database connections, along with your `GEMINI_API_KEY`.

3. **Run Migrations & Seed DB**
   ```bash
   php artisan migrate --seed
   ```

4. **Start Web Server**
   ```bash
   php artisan serve
   ```

5. **Start Crawler Service Daemon**
   ```bash
   python vm_agent/runner.py
   ```

## License

This software is proprietary and confidential. All rights reserved.
