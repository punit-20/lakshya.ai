<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
  <title>Lakshya AI — AI-Powered Lead Generation & Outreach Automation</title>
  <meta name="description" content="Lakshya.ai autonomously monitors Reddit, Twitter & LinkedIn to discover qualified B2B leads, score them with Gemini AI, and draft personalized pitches — automatically.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

<!-- ═══════════════════════════════════════════════════════
     PRELOADER
═══════════════════════════════════════════════════════ -->
<div id="preloader">
  <div class="preloader-inner">
    <!-- Spinning rings + big logo -->
    <div class="preloader-logo-wrap">
      <div class="preloader-ring preloader-ring-1"></div>
      <div class="preloader-ring preloader-ring-2"></div>
      <div class="preloader-ring preloader-ring-3"></div>
      <img src="{{ asset('images/logo.png') }}" alt="Lakshya.ai" class="preloader-logo-img">
    </div>
    <!-- Brand name -->
    <div class="preloader-brand">LAKSHYA.AI</div>
    <div class="preloader-tagline">AI-Powered Lead Generation</div>
    <!-- Loading bar -->
    <div class="preloader-bar">
      <div class="preloader-fill"></div>
    </div>
    <div class="preloader-status" id="preloaderStatus">Initializing AI Systems...</div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     NAVBAR
═══════════════════════════════════════════════════════ -->
<nav id="navbar">
  <a href="{{ url('/') }}" class="nav-brand logo-anim-3" id="navBrand">
    <div class="nav-brand-icon" style="background:transparent;box-shadow:none;padding:0;overflow:visible;">
      <img src="{{ asset('images/logo.png') }}" alt="Lakshya.ai Logo" class="logo-img" style="width:4rem;height:4rem;object-fit:contain;image-rendering:-webkit-optimize-contrast;">
    </div>
    <span class="nav-brand-text">Lakshya.ai</span>
  </a>

  <ul class="nav-links">
    <li><a href="#features">Features</a></li>
    <li><a href="#dashboard-preview">Platform</a></li>
    <li><a href="#pricing">Pricing</a></li>
    <li><a href="#testimonials">Clients</a></li>
    <li><a href="#careers">Careers</a></li>
  </ul>

  <div class="nav-actions">
    <a href="{{ route('login') }}" class="btn-ghost">Login</a>
    <a href="{{ route('register') }}" class="btn-primary">Get Started →</a>
  </div>

  <button class="nav-hamburger" id="hamburgerBtn" aria-label="Toggle Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- ═══════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════ -->
<section id="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-grid"></div>
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>

  <div class="hero-content">
    <div class="hero-badge" data-animate="fade-up">
      <span class="hero-badge-dot"></span>
      Now powered by Gemini 2.5 Flash AI
    </div>

    <h1 class="hero-title" data-animate="fade-up">
      <span class="hero-title-line1">Turn Social Noise</span>
      <span class="hero-title-line2">Into B2B Revenue</span>
    </h1>

    <p class="hero-subtitle" data-animate="fade-up">
      Lakshya.ai autonomously scrapes Reddit, Twitter & LinkedIn, qualifies leads with AI intent scoring (0–100), and drafts personalized outreach pitches — 24/7, on autopilot.
    </p>

    <div class="hero-pipeline" data-animate="fade-up">
      <div class="pipeline-step">Scrape</div>
      <span class="pipeline-arrow">→</span>
      <div class="pipeline-step">Qualify</div>
      <span class="pipeline-arrow">→</span>
      <div class="pipeline-step">Pitch</div>
      <span class="pipeline-arrow">→</span>
      <div class="pipeline-step">Convert</div>
    </div>

    <div class="hero-cta" data-animate="fade-up">
      <a href="{{ route('register') }}" class="btn-primary btn-large">Start Free Trial →</a>
      <a href="#dashboard-preview" class="btn-secondary-large">See Platform ↓</a>
    </div>

    <div class="hero-stats" data-animate="fade-up" data-stagger>
      <div class="hero-stat-item">
        <div class="hero-stat-value">10K+</div>
        <div class="hero-stat-label">Leads Discovered</div>
      </div>
      <div class="hero-stat-item">
        <div class="hero-stat-value">92%</div>
        <div class="hero-stat-label">AI Accuracy</div>
      </div>
      <div class="hero-stat-item">
        <div class="hero-stat-value">3x</div>
        <div class="hero-stat-label">Conversion Lift</div>
      </div>
      <div class="hero-stat-item">
        <div class="hero-stat-value">24/7</div>
        <div class="hero-stat-label">Always-On Crawler</div>
      </div>
    </div>
  </div>

  <div class="scroll-indicator">
    <span>Scroll</span>
    <div class="scroll-mouse"><div class="scroll-mouse-dot"></div></div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     TECH STACK MARQUEE
═══════════════════════════════════════════════════════ -->
<div id="tech-stack">
  <div class="marquee-track">
    <div class="tech-badge"><span class="tech-badge-icon">⚡</span> Laravel 12</div>
    <div class="tech-badge"><span class="tech-badge-icon">🐘</span> PHP 8.2+</div>
    <div class="tech-badge"><span class="tech-badge-icon">🐍</span> Python 3.10+</div>
    <div class="tech-badge"><span class="tech-badge-icon">🤖</span> Gemini AI</div>
    <div class="tech-badge"><span class="tech-badge-icon">🗄️</span> SQLite</div>
    <div class="tech-badge"><span class="tech-badge-icon">⚡</span> Vite 7</div>
    <div class="tech-badge"><span class="tech-badge-icon">🔴</span> Reddit API</div>
    <div class="tech-badge"><span class="tech-badge-icon">🐦</span> Twitter/X</div>
    <div class="tech-badge"><span class="tech-badge-icon">💼</span> LinkedIn</div>
    <div class="tech-badge"><span class="tech-badge-icon">🌐</span> Flask API</div>
    <div class="tech-badge"><span class="tech-badge-icon">📊</span> Chart.js</div>
    <div class="tech-badge"><span class="tech-badge-icon">🔑</span> OAuth 2.0</div>
    <!-- Duplicate for seamless loop -->
    <div class="tech-badge"><span class="tech-badge-icon">⚡</span> Laravel 12</div>
    <div class="tech-badge"><span class="tech-badge-icon">🐘</span> PHP 8.2+</div>
    <div class="tech-badge"><span class="tech-badge-icon">🐍</span> Python 3.10+</div>
    <div class="tech-badge"><span class="tech-badge-icon">🤖</span> Gemini AI</div>
    <div class="tech-badge"><span class="tech-badge-icon">🗄️</span> SQLite</div>
    <div class="tech-badge"><span class="tech-badge-icon">⚡</span> Vite 7</div>
    <div class="tech-badge"><span class="tech-badge-icon">🔴</span> Reddit API</div>
    <div class="tech-badge"><span class="tech-badge-icon">🐦</span> Twitter/X</div>
    <div class="tech-badge"><span class="tech-badge-icon">💼</span> LinkedIn</div>
    <div class="tech-badge"><span class="tech-badge-icon">🌐</span> Flask API</div>
    <div class="tech-badge"><span class="tech-badge-icon">📊</span> Chart.js</div>
    <div class="tech-badge"><span class="tech-badge-icon">🔑</span> OAuth 2.0</div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     FEATURES
═══════════════════════════════════════════════════════ -->
<section id="features">
  <div class="container">
    <div data-animate="fade-up">
      <span class="section-label">Core Capabilities</span>
      <h2 class="section-title">Everything you need to <span>dominate B2B outreach</span></h2>
      <p class="section-desc">From automated discovery to AI-qualified pitches — Lakshya handles the full pipeline so your team focuses on closing deals.</p>
    </div>

    <div class="features-grid" data-stagger>
      <div class="feature-card">
        <div class="feature-icon">🧠</div>
        <h3 class="feature-title">AI Lead Qualification</h3>
        <p class="feature-desc">Gemini 2.5 Flash scores every lead 0–100 based on purchase intent, urgency signals, and conversation context. Only ≥70 score leads enter your CRM.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">🤖</div>
        <h3 class="feature-title">24/7 Background Crawler</h3>
        <p class="feature-desc">Python daemon scrapes Reddit (OAuth API), Twitter/X & LinkedIn via DuckDuckGo indexing every 30 seconds — completely hands-free.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">📨</div>
        <h3 class="feature-title">Personalized Outreach</h3>
        <p class="feature-desc">AI drafts platform-native replies referencing the original post — casual for Reddit, professional for LinkedIn. One-click send or manual review.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">📋</div>
        <h3 class="feature-title">CRM Kanban Pipeline</h3>
        <p class="feature-desc">Visual drag-and-drop pipeline: New → Discovered → Contacted → Qualified → Closed. Full conversation history and meeting scheduling built-in.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">📊</div>
        <h3 class="feature-title">SaaS Economics Dashboard</h3>
        <p class="feature-desc">Real-time MRR/ARR tracking, OPEX breakdown, CAC, LTV, burn rate & runway — everything you need to monitor your B2B SaaS P&L live.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">👥</div>
        <h3 class="feature-title">Multi-Tenant Client Portal</h3>
        <p class="feature-desc">Manage multiple clients from one admin panel. Client impersonation, isolated campaign dashboards, per-client subscription tiers & quotas.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">🎨</div>
        <h3 class="feature-title">AI Marketing Generator</h3>
        <p class="feature-desc">Generate social posts, ad campaigns, growth suite content, and full marketing bundles with Gemini AI — delivered in seconds.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">🔑</div>
        <h3 class="feature-title">Campaign & Keyword Management</h3>
        <p class="feature-desc">Create isolated project pipelines, define targeting keywords per project, and toggle crawl targets active/inactive in real-time.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">🌍</div>
        <h3 class="feature-title">Multi-Language Support</h3>
        <p class="feature-desc">Built-in locale switching (EN / ES / HI) across the entire admin interface — making Lakshya truly global from day one.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     DASHBOARD PREVIEW
═══════════════════════════════════════════════════════ -->
<section id="dashboard-preview">
  <div class="container">
    <div class="dashboard-showcase">
      <div class="dashboard-info" data-animate="fade-right">
        <span class="section-label">The Platform</span>
        <h2 class="section-title">A cockpit built for <span>AI-driven growth</span></h2>
        <p class="section-desc">Every data point at your fingertips — from real-time lead streams to P&L economics, all in one sleek dark-mode dashboard.</p>

        <ul class="feature-checklist">
          <li>
            <div class="checklist-icon">✓</div>
            <span>Live lead feed with AI intent scores updated every 30 seconds</span>
          </li>
          <li>
            <div class="checklist-icon">✓</div>
            <span>Kanban CRM with drag-and-drop status management</span>
          </li>
          <li>
            <div class="checklist-icon">✓</div>
            <span>MRR / ARR / CAC / LTV metrics with historical trend graphs</span>
          </li>
          <li>
            <div class="checklist-icon">✓</div>
            <span>Platform distribution analytics (Reddit / Twitter / LinkedIn)</span>
          </li>
          <li>
            <div class="checklist-icon">✓</div>
            <span>One-click AI reply generation and campaign launching</span>
          </li>
        </ul>

        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
          <a href="{{ route('register') }}" class="btn-primary btn-large">Try Free →</a>
          <a href="{{ route('login') }}" class="btn-secondary-large">Login</a>
        </div>
      </div>

      <div class="dashboard-visual" data-animate="fade-left">
        <div class="dashboard-image-wrapper">
          <img src="{{ asset('images/dashboard-preview.png') }}" alt="Lakshya.ai Dashboard Preview">
          <div class="dashboard-pill dashboard-pill-1">🟢 Live • 247 leads today</div>
          <div class="dashboard-pill dashboard-pill-2">🤖 AI Score: 87/100</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     COMPARISON TABLE
═══════════════════════════════════════════════════════ -->
<section id="comparison">
  <div class="container">
    <div data-animate="fade-up" style="text-align:center;">
      <span class="section-label">Why Lakshya?</span>
      <h2 class="section-title" style="margin-bottom:0.5rem;">Traditional lead gen is <span>broken</span></h2>
      <p class="section-desc" style="margin:0 auto 3rem;">See exactly how Lakshya compares to manual outreach</p>
    </div>

    <div data-animate="fade-up" style="overflow-x:auto;">
      <table class="comparison-table">
        <thead>
          <tr>
            <th>Capability</th>
            <th>❌ Traditional</th>
            <th class="highlight">✅ Lakshya.ai</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Lead Discovery</td>
            <td class="comp-bad">Manual platform browsing (hours/day)</td>
            <td class="highlight">Automated 24/7 multi-platform crawling</td>
          </tr>
          <tr>
            <td>Lead Scoring</td>
            <td class="comp-bad">Gut-feel guesswork</td>
            <td class="highlight">AI intent scoring 0–100 (Gemini)</td>
          </tr>
          <tr>
            <td>Outreach Quality</td>
            <td class="comp-bad">Generic cold templates</td>
            <td class="highlight">Context-aware personalized pitches</td>
          </tr>
          <tr>
            <td>Pipeline Management</td>
            <td class="comp-bad">Spreadsheets & sticky notes</td>
            <td class="highlight">Visual Kanban CRM with AI replies</td>
          </tr>
          <tr>
            <td>Analytics</td>
            <td class="comp-bad">No real-time insights</td>
            <td class="highlight">Real-time P&L, MRR, CAC, LTV dashboard</td>
          </tr>
          <tr>
            <td>Scale</td>
            <td class="comp-bad">Limited by human bandwidth</td>
            <td class="highlight">Infinite — Python daemon never sleeps</td>
          </tr>
          <tr>
            <td>Multi-Client</td>
            <td class="comp-bad">One account per agency</td>
            <td class="highlight">Multi-tenant with impersonation support</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     PRICING
═══════════════════════════════════════════════════════ -->
<section id="pricing">
  <div class="container">
    <div data-animate="fade-up" style="text-align:center;">
      <span class="section-label">Pricing Plans</span>
      <h2 class="section-title">Simple, transparent <span>pricing</span></h2>
      <p class="section-desc" style="margin:0 auto 3rem;">Start free. Scale as you grow. No hidden fees.</p>
    </div>

    <div class="pricing-grid" data-stagger>
      <div class="pricing-card">
        <div class="pricing-icon">🆓</div>
        <div class="pricing-tier">Free Trial</div>
        <div class="pricing-price">
          <span class="pricing-currency">₹</span>
          <span class="pricing-amount">0</span>
          <span class="pricing-period">/month</span>
        </div>
        <ul class="pricing-features">
          <li><span>✅</span><span>50 leads/month</span></li>
          <li><span>✅</span><span>Basic CRM pipeline</span></li>
          <li><span>✅</span><span>Reddit crawling only</span></li>
          <li><span>✅</span><span>AI scoring (limited)</span></li>
          <li><span>❌</span><span style="opacity:0.4;">Marketing generator</span></li>
          <li><span>❌</span><span style="opacity:0.4;">Client management</span></li>
        </ul>
        <a href="{{ route('register') }}" class="btn-secondary-large" style="display:block;text-align:center;">Get Started Free</a>
      </div>

      <div class="pricing-card featured">
        <div class="pricing-badge">POPULAR</div>
        <div class="pricing-icon">🟢</div>
        <div class="pricing-tier">Starter</div>
        <div class="pricing-price">
          <span class="pricing-currency">₹</span>
          <span class="pricing-amount">1,499</span>
          <span class="pricing-period">/month</span>
        </div>
        <ul class="pricing-features">
          <li><span>✅</span><span>500 leads/month</span></li>
          <li><span>✅</span><span>Full CRM + Kanban</span></li>
          <li><span>✅</span><span>Reddit + Twitter/X crawling</span></li>
          <li><span>✅</span><span>Full AI scoring & pitches</span></li>
          <li><span>✅</span><span>AI Marketing Generator</span></li>
          <li><span>❌</span><span style="opacity:0.4;">Multi-client portal</span></li>
        </ul>
        <a href="{{ route('register') }}" class="btn-primary btn-large" style="display:block;text-align:center;">Start Starter</a>
      </div>

      <div class="pricing-card">
        <div class="pricing-icon">🟣</div>
        <div class="pricing-tier">Pro</div>
        <div class="pricing-price">
          <span class="pricing-currency">₹</span>
          <span class="pricing-amount">4,999</span>
          <span class="pricing-period">/month</span>
        </div>
        <ul class="pricing-features">
          <li><span>✅</span><span>Unlimited leads</span></li>
          <li><span>✅</span><span>All Starter features</span></li>
          <li><span>✅</span><span>LinkedIn crawling</span></li>
          <li><span>✅</span><span>Priority AI processing</span></li>
          <li><span>✅</span><span>SaaS Economics Dashboard</span></li>
          <li><span>✅</span><span>Multi-client management</span></li>
        </ul>
        <a href="{{ route('register') }}" class="btn-secondary-large" style="display:block;text-align:center;">Go Pro</a>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     TESTIMONIALS
═══════════════════════════════════════════════════════ -->
<section id="testimonials">
  <div class="container">
    <div data-animate="fade-up">
      <span class="section-label">Client Reviews</span>
      <h2 class="section-title">What our clients <span>say</span></h2>
      <p class="section-desc" style="margin-bottom:3rem;">Real results from real businesses using Lakshya.ai</p>
    </div>

    <div class="testimonials-grid" data-stagger>
      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Lakshya completely replaced our SDR team's manual prospecting. We went from 3 qualified leads per week to 30+ — and the AI-generated replies are indistinguishable from human-written ones."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background: linear-gradient(135deg,#00f5ff,#0ea5e9);">AK</div>
          <div>
            <div class="testimonial-name">Arjun Kapoor</div>
            <div class="testimonial-role">Head of Sales, NexaTech Solutions</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"The Gemini AI scoring is scarily accurate. We only ever see leads that are genuinely interested — our conversion rate jumped from 4% to 14% in the first month."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background: linear-gradient(135deg,#a855f7,#6366f1);">PM</div>
          <div>
            <div class="testimonial-name">Priya Mehta</div>
            <div class="testimonial-role">Founder, GrowthLoop Agency</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"As an agency we manage 8 clients from one Lakshya account. The client impersonation mode is incredible for demos. Saved us 40+ hours/month on manual reporting."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background: linear-gradient(135deg,#f472b6,#f59e0b);">RS</div>
          <div>
            <div class="testimonial-name">Rohit Singh</div>
            <div class="testimonial-role">CEO, Pixel Arc Marketing</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★☆</div>
        <p class="testimonial-text">"The SaaS economics dashboard alone is worth the Pro plan. I can see our CAC, LTV, burn rate and runway in real-time — things we used to calculate manually in spreadsheets."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background: linear-gradient(135deg,#10b981,#059669);">DN</div>
          <div>
            <div class="testimonial-name">Divya Nair</div>
            <div class="testimonial-role">CFO, SaaSify India</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"Set it up on a Monday, had qualified leads in our pipeline by Wednesday. The Reddit crawler + AI reply combo is pure magic for our developer-tools SaaS."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background: linear-gradient(135deg,#f59e0b,#ef4444);">KV</div>
          <div>
            <div class="testimonial-name">Karan Verma</div>
            <div class="testimonial-role">CTO, DevStack Labs</div>
          </div>
        </div>
      </div>

      <div class="testimonial-card">
        <div class="testimonial-stars">★★★★★</div>
        <p class="testimonial-text">"We scaled from ₹0 to ₹2.4L MRR in 6 months using Lakshya as our primary lead source. The Starter plan paid for itself 30x over in the first quarter."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background: linear-gradient(135deg,#3b82f6,#6366f1);">SM</div>
          <div>
            <div class="testimonial-name">Sneha Mishra</div>
            <div class="testimonial-role">Founder, CloudReach.io</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     CAREERS
═══════════════════════════════════════════════════════ -->
<section id="careers">
  <div class="container">
    <div data-animate="fade-up">
      <span class="section-label">Join the Team</span>
      <h2 class="section-title">Build the future of <span>AI outreach</span></h2>
    </div>

    <div class="careers-intro">
      <div data-animate="fade-right">
        <p style="color:var(--text-secondary);font-size:1rem;line-height:1.8;margin-bottom:2rem;">
          We're a small, ambitious team on a mission to automate B2B lead generation with AI. If you love building products that have real impact and want to work at the bleeding edge of AI + SaaS, you'll fit right in.
        </p>
        <div class="careers-values">
          <div class="value-item">
            <span class="value-icon">🚀</span>
            <div>
              <div class="value-title">Move Fast</div>
              <div class="value-desc">Ship fast, learn fast. We iterate weekly and push to production daily.</div>
            </div>
          </div>
          <div class="value-item">
            <span class="value-icon">🤖</span>
            <div>
              <div class="value-title">AI-First Thinking</div>
              <div class="value-desc">Every feature starts with "can AI do this better?" — and usually, yes.</div>
            </div>
          </div>
          <div class="value-item">
            <span class="value-icon">🌏</span>
            <div>
              <div class="value-title">Remote & Async</div>
              <div class="value-desc">Work from anywhere. We trust you to own your output.</div>
            </div>
          </div>
          <div class="value-item">
            <span class="value-icon">📈</span>
            <div>
              <div class="value-title">Equity + Growth</div>
              <div class="value-desc">Competitive equity, learning budget, and a seat at the table from day one.</div>
            </div>
          </div>
        </div>
      </div>

      <div data-animate="fade-left">
        <div class="jobs-section-title">Open Positions</div>
        <div class="jobs-list">
          <a href="mailto:punitsoni202@gmail.com?subject=Application: Senior Laravel Developer" class="job-card">
            <div class="job-info">
              <div class="job-title">Senior Laravel Developer</div>
              <div class="job-meta">
                <span class="job-tag tag-dept">Engineering</span>
                <span class="job-tag tag-type">Full-time</span>
                <span class="job-tag tag-remote">Remote</span>
              </div>
            </div>
            <div class="job-apply">Apply <span>→</span></div>
          </a>

          <a href="mailto:punitsoni202@gmail.com?subject=Application: Python AI Engineer" class="job-card">
            <div class="job-info">
              <div class="job-title">Python AI/ML Engineer</div>
              <div class="job-meta">
                <span class="job-tag tag-dept">Engineering</span>
                <span class="job-tag tag-type">Full-time</span>
                <span class="job-tag tag-remote">Remote</span>
              </div>
            </div>
            <div class="job-apply">Apply <span>→</span></div>
          </a>

          <a href="mailto:punitsoni202@gmail.com?subject=Application: Growth Marketer" class="job-card">
            <div class="job-info">
              <div class="job-title">B2B Growth Marketer</div>
              <div class="job-meta">
                <span class="job-tag tag-dept">Marketing</span>
                <span class="job-tag tag-type">Full-time</span>
                <span class="job-tag tag-remote">Remote</span>
              </div>
            </div>
            <div class="job-apply">Apply <span>→</span></div>
          </a>

          <a href="mailto:punitsoni202@gmail.com?subject=Application: Frontend Developer" class="job-card">
            <div class="job-info">
              <div class="job-title">Frontend Developer (Blade/JS)</div>
              <div class="job-meta">
                <span class="job-tag tag-dept">Engineering</span>
                <span class="job-tag tag-type">Part-time</span>
                <span class="job-tag tag-remote">Remote</span>
              </div>
            </div>
            <div class="job-apply">Apply <span>→</span></div>
          </a>

          <a href="mailto:punitsoni202@gmail.com?subject=Application: Sales Development Rep" class="job-card">
            <div class="job-info">
              <div class="job-title">Sales Development Representative</div>
              <div class="job-meta">
                <span class="job-tag tag-dept">Sales</span>
                <span class="job-tag tag-type">Full-time</span>
                <span class="job-tag tag-remote">Remote</span>
              </div>
            </div>
            <div class="job-apply">Apply <span>→</span></div>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     CTA BANNER
═══════════════════════════════════════════════════════ -->
<section id="cta">
  <div class="container">
    <div class="cta-box" data-animate="fade-up">
      <span class="section-label">Ready to grow?</span>
      <h2 class="cta-title">Start generating qualified leads <span style="background:var(--gradient-mixed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">today</span></h2>
      <p class="cta-desc">Join hundreds of B2B companies automating their lead pipeline with AI. Free trial, no credit card required.</p>
      <div class="cta-actions">
        <a href="{{ route('register') }}" class="btn-primary btn-large">Start Free Trial →</a>
        <a href="mailto:punitsoni202@gmail.com" class="btn-secondary-large">Talk to Sales</a>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════════════ -->
<footer id="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.75rem;">
          <img src="{{ asset('images/logo.png') }}" alt="Lakshya.ai" style="width:36px;height:36px;object-fit:contain;filter:drop-shadow(0 0 8px rgba(0,245,255,0.5));">
          <div class="footer-brand-text">Lakshya.ai</div>
        </div>
        <p class="footer-tagline">AI-powered B2B lead generation & outreach automation. Scrape → Qualify → Pitch → Convert — on autopilot.</p>
        <div class="footer-socials">
          <a href="https://github.com/punit-20" class="social-btn" target="_blank" title="GitHub">🐙</a>
          <a href="https://www.linkedin.com/in/punit-soni-227467203" class="social-btn" target="_blank" title="LinkedIn">💼</a>
          <a href="https://www.instagram.com/heispunit/" class="social-btn" target="_blank" title="Instagram">📷</a>
          <a href="mailto:punitsoni202@gmail.com" class="social-btn" title="Email">📧</a>
        </div>
      </div>

      <div>
        <div class="footer-col-title">Platform</div>
        <ul class="footer-links">
          <li><a href="#features">Features</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#dashboard-preview">Dashboard</a></li>
          <li><a href="#comparison">Why Lakshya?</a></li>
        </ul>
      </div>

      <div>
        <div class="footer-col-title">Company</div>
        <ul class="footer-links">
          <li><a href="#careers">Careers</a></li>
          <li><a href="#testimonials">Client Stories</a></li>
          <li><a href="mailto:punitsoni202@gmail.com">Contact Us</a></li>
          <li><a href="https://github.com/punit-20" target="_blank">GitHub</a></li>
        </ul>
      </div>

      <div>
        <div class="footer-col-title">Account</div>
        <ul class="footer-links">
          <li><a href="{{ route('login') }}">Login</a></li>
          <li><a href="{{ route('register') }}">Sign Up Free</a></li>
          <li><a href="{{ route('login') }}">Admin Panel</a></li>
        </ul>
        <div style="margin-top:1.5rem;">
          <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.1em;">Tech Stack</div>
          <div style="display:flex;flex-wrap:wrap;gap:0.4rem;">
            <span style="font-size:0.7rem;padding:0.2rem 0.5rem;background:rgba(255,45,32,0.08);border:1px solid rgba(255,45,32,0.2);border-radius:4px;color:#ff6b6b;">Laravel 12</span>
            <span style="font-size:0.7rem;padding:0.2rem 0.5rem;background:rgba(55,118,171,0.08);border:1px solid rgba(55,118,171,0.2);border-radius:4px;color:#5bc8f5;">Python</span>
            <span style="font-size:0.7rem;padding:0.2rem 0.5rem;background:rgba(142,117,178,0.08);border:1px solid rgba(142,117,178,0.2);border-radius:4px;color:#c084fc;">Gemini AI</span>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="footer-copyright">© {{ date('Y') }} Lakshya.ai — Built with ❤️ by <a href="https://github.com/punit-20" style="color:var(--neon-cyan);text-decoration:none;">Punit</a>. All rights reserved.</div>
      <div class="footer-bottom-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Proprietary License</a>
      </div>
    </div>
  </div>
</footer>


<!-- ═══════════════════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════════════════ -->
<script>
// ── Preloader — min 3s / max 5s ──────────────────────────
(function() {
  var messages = [
    'Initializing AI Systems...',
    'Loading Neural Networks...',
    'Connecting to Gemini AI...',
    'Calibrating Lead Scanner...',
    'Systems Online — Welcome!'
  ];
  var statusEl = document.getElementById('preloaderStatus');
  var msgIdx = 0;
  var msgInterval = setInterval(function() {
    msgIdx = (msgIdx + 1) % messages.length;
    if (statusEl) statusEl.textContent = messages[msgIdx];
  }, 900);

  var hidePL = function() {
    clearInterval(msgInterval);
    var pl = document.getElementById('preloader');
    if (!pl || pl._hiding) return;
    pl._hiding = true;
    pl.style.transition = 'opacity 0.5s ease';
    pl.style.opacity = '0';
    setTimeout(function() { if (pl && pl.parentNode) pl.parentNode.removeChild(pl); }, 500);
  };

  // Minimum 3 seconds always shown
  var minDone = false;
  setTimeout(function() {
    minDone = true;
    // If page already loaded, hide now
    if (document.readyState === 'complete') hidePL();
  }, 3000);

  // On page load, only hide if min time passed
  window.addEventListener('load', function() {
    if (minDone) hidePL();
    // else wait for minTimer to fire
  });

  // Absolute maximum 5 seconds
  setTimeout(hidePL, 5000);
})();


// ── Navbar scroll effect ──────────────────────────────────
var navbar = document.getElementById('navbar');
window.addEventListener('scroll', function() {
  if (window.scrollY > 50) navbar.classList.add('scrolled');
  else navbar.classList.remove('scrolled');
}, { passive: true });

// ── Hamburger toggle ──────────────────────────────────────
var hamburger   = document.getElementById('hamburgerBtn');
var navLinksList  = document.querySelector('.nav-links');
var navActionsEl  = document.querySelector('.nav-actions');
if (hamburger) {
  hamburger.addEventListener('click', function() {
    var open = navLinksList.style.display === 'flex';
    navLinksList.style.cssText  = open ? '' : 'display:flex;flex-direction:column;position:fixed;top:70px;left:0;right:0;background:rgba(5,5,15,0.98);backdrop-filter:blur(20px);padding:1.5rem 2rem;gap:1rem;border-bottom:1px solid rgba(0,245,255,0.08);z-index:999;';
    navActionsEl.style.cssText  = open ? '' : 'display:flex;flex-direction:column;position:fixed;top:280px;left:0;right:0;background:rgba(5,5,15,0.98);padding:1rem 2rem 2rem;gap:0.75rem;z-index:999;';
  });
}

// ── Scroll Animations (IntersectionObserver) ──────────────
var scrollObserver = new IntersectionObserver(function(entries) {
  entries.forEach(function(entry) {
    if (entry.isIntersecting) {
      entry.target.classList.add('animate');
      scrollObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

document.querySelectorAll('[data-animate], [data-stagger]').forEach(function(el) {
  scrollObserver.observe(el);
});

// ── Smooth anchor scroll ──────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(function(a) {
  a.addEventListener('click', function(e) {
    var target = document.querySelector(a.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

</script>

</body>
</html>
