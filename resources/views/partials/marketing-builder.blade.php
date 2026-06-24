@section('styles')
<style>
    .marketing-layout {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 2rem;
        margin-top: 1.5rem;
    }

    @media (max-width: 1024px) {
        .marketing-layout {
            grid-template-columns: 1fr;
        }
    }

    .section-title-wrapper {
        margin-bottom: 2rem;
    }

    .form-section, .preview-section {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .card-subtitle {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    /* Tabs Navigation */
    .tabs-nav-container {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        background: rgba(255, 255, 255, 0.02);
        padding: 0.5rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    
    .tab-nav-btn {
        flex: 1;
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 600;
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .tab-nav-btn:hover {
        color: var(--text-main);
        background: rgba(255, 255, 255, 0.05);
    }
    
    .tab-nav-btn.active {
        color: white;
        background: var(--primary-gradient);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.25);
    }

    /* Sub-tabs/Pills */
    .sub-tabs-container {
        display: flex;
        gap: 0.35rem;
        margin-bottom: 1.25rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
    }
    
    .sub-tab-pill {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.4rem 0.85rem;
        cursor: pointer;
        border-radius: 20px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .sub-tab-pill:hover {
        color: var(--text-main);
        background: rgba(255, 255, 255, 0.07);
    }
    
    .sub-tab-pill.active {
        background: rgba(99, 102, 241, 0.15);
        color: var(--primary-color);
        border-color: rgba(99, 102, 241, 0.4);
    }

    /* Post Preview Card */
    .mock-post-card {
        background: rgba(18, 24, 38, 0.6);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    .mock-post-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .mock-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        font-size: 1rem;
    }

    .mock-user-info {
        display: flex;
        flex-direction: column;
    }

    .mock-username {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .mock-timestamp {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .image-generator-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        background: rgba(0, 0, 0, 0.2);
        position: relative;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-loader {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(11, 15, 25, 0.8);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        z-index: 2;
    }

    .image-loader span {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* Growth Pack Specific */
    .growth-doc-card {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .growth-content {
        font-size: 0.85rem;
        color: var(--text-main);
        line-height: 1.6;
        white-space: pre-wrap;
    }

    /* Calendar Grid */
    .calendar-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .calendar-day-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .calendar-day-badge {
        background: var(--primary-gradient);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        min-width: 80px;
        text-align: center;
    }
    
    .calendar-day-text {
        font-size: 0.85rem;
        color: var(--text-main);
        line-height: 1.4;
    }

    .ad-badge {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        width: fit-content;
    }

    .copy-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-weight: 600;
    }

    .copy-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-main);
    }

    /* Stepper in Launch Modal */
    .step-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .step-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        opacity: 0.4;
        transition: opacity 0.3s;
    }

    .step-item.active {
        opacity: 1;
    }

    .step-item.completed {
        opacity: 1;
        color: #34d399;
    }

    .step-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.3s;
    }

    .step-item.active .step-icon {
        border-color: var(--primary-color);
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary-color);
        box-shadow: 0 0 10px rgba(99, 102, 241, 0.3);
    }

    .step-item.completed .step-icon {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .step-text {
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Loading Spinner */
    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-top-color: currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* NSFW Disclaimer Banner */
    .nsfw-disclaimer {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        padding: 0.75rem 1rem;
        margin-top: 0.75rem;
        background: rgba(239, 68, 68, 0.06);
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-radius: 10px;
        font-size: 0.75rem;
        line-height: 1.5;
        color: var(--text-muted);
    }
    .nsfw-disclaimer .nsfw-icon {
        font-size: 1rem;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .nsfw-disclaimer strong {
        color: #f43f5e;
        font-weight: 700;
    }
    .nsfw-disclaimer .penalty-highlight {
        color: #f59e0b;
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<div class="marketing-wrapper">
    <div class="section-title-wrapper">
        <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">{{ $title }}</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
            {{ $description }}
        </p>
    </div>

    <!-- Tabs Nav -->
    <div class="tabs-nav-container">
        <button id="tab-nav-social" class="tab-nav-btn active" onclick="switchTab('social')">
            <span>📱</span> Social Post Engine
        </button>
        <button id="tab-nav-growth" class="tab-nav-btn" onclick="switchTab('growth')">
            <span>📰</span> Weekly Growth Pack
        </button>
        <button id="tab-nav-campaign" class="tab-nav-btn" onclick="switchTab('campaign')">
            <span>🚀</span> Ad Campaign Suite
        </button>
    </div>

    <div class="marketing-layout">
        <!-- Input Form Column -->
        <div class="form-section">
            <!-- SOCIAL FORM -->
            <div id="form-social" class="card tab-form-content">
                <div class="card-header-flex">
                    <h2 style="font-size: 1.1rem; font-weight: 700;">Multi-Channel Context Creator</h2>
                    <span class="badge badge-new">Social Engine</span>
                </div>
                <form id="socialGeneratorForm" onsubmit="generateSocialSuite(event)">
                    <div class="form-group">
                        <label for="social_business_description">Client Business / Work Description <span style="color: #f43f5e;">*</span></label>
                        <textarea id="social_business_description" class="form-control" placeholder="Describe the business, its products, or services. What makes it special? (e.g., 'A local bakery that bakes organic, wood-fired sourdough bread daily using heritage grains.')" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="social_tone">Tone of Voice <span style="color: #f43f5e;">*</span></label>
                        <select id="social_tone" class="project-selector" style="width: 100%; height: 42px;">
                            <option value="Creative & Friendly">Creative & Friendly</option>
                            <option value="Professional & Authoritative">Professional & Authoritative</option>
                            <option value="Bold & Disruptive">Bold & Disruptive</option>
                            <option value="Helpful & Informative">Helpful & Informative</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="social_target_audience">Target Audience <span style="color: #f43f5e;">*</span></label>
                        <input type="text" id="social_target_audience" class="form-control" placeholder="e.g. Local foodies, tech startup founders, remote engineers" required>
                    </div>
                    <div class="form-group">
                        <label for="social_cta">Call to Action / Special Offers (Optional)</label>
                        <input type="text" id="social_cta" class="form-control" placeholder="e.g. Buy 1 get 1 free this weekend! / Sign up for free trial at website.com">
                    </div>
                    <div class="nsfw-disclaimer">
                        <span class="nsfw-icon">⚠️</span>
                        <span><strong>Content Policy:</strong> Submitting NSFW, vulgar, sexually explicit, violent, or prohibited content will result in an automatic <span class="penalty-highlight">5-credit penalty deduction</span> from your subscription. All inputs are AI-moderated in real time.</span>
                    </div>
                    <button type="submit" id="socialGenerateBtn" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem; height: 46px;">
                        <span id="socialBtnText">⚡ Generate Multi-Channel Posts</span>
                        <div id="socialBtnSpinner" class="spinner" style="display: none; margin: 0 auto;"></div>
                    </button>
                </form>
            </div>

            <!-- GROWTH FORM -->
            <div id="form-growth" class="card tab-form-content" style="display: none;">
                <div class="card-header-flex">
                    <h2 style="font-size: 1.1rem; font-weight: 700;">Growth Content Pack Config</h2>
                    <span class="badge badge-new" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: rgba(16, 185, 129, 0.2);">Growth Engine</span>
                </div>
                <form id="growthGeneratorForm" onsubmit="generateGrowthSuite(event)">
                    <div class="form-group">
                        <label for="growth_business_description">Client Business / Work Description <span style="color: #f43f5e;">*</span></label>
                        <textarea id="growth_business_description" class="form-control" placeholder="Describe the business, its products, or services. What makes it special?" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="growth_target_audience">Target Audience <span style="color: #f43f5e;">*</span></label>
                        <input type="text" id="growth_target_audience" class="form-control" placeholder="e.g. Small business owners, B2B sales leads" required>
                    </div>
                    <div class="form-group">
                        <label for="growth_campaign_goal">Growth / Campaign Goal <span style="color: #f43f5e;">*</span></label>
                        <input type="text" id="growth_campaign_goal" class="form-control" placeholder="e.g. Increase email newsletter signups, book demo calls" required>
                    </div>
                    <div class="nsfw-disclaimer">
                        <span class="nsfw-icon">⚠️</span>
                        <span><strong>Content Policy:</strong> Submitting NSFW, vulgar, sexually explicit, violent, or prohibited content will result in an automatic <span class="penalty-highlight">5-credit penalty deduction</span> from your subscription. All inputs are AI-moderated in real time.</span>
                    </div>
                    <button type="submit" id="growthGenerateBtn" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem; height: 46px; background: var(--secondary-gradient);">
                        <span id="growthBtnText">⚡ Generate Weekly Growth Pack</span>
                        <div id="growthBtnSpinner" class="spinner" style="display: none; margin: 0 auto;"></div>
                    </button>
                </form>
            </div>

            <!-- AD CAMPAIGN FORM -->
            <div id="form-campaign" class="card tab-form-content" style="display: none;">
                <div class="card-header-flex">
                    <h2 style="font-size: 1.1rem; font-weight: 700;">AI Campaign Planner</h2>
                    <span class="badge badge-new" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2);">Campaign Builder</span>
                </div>
                <form id="campaignGeneratorForm" onsubmit="generateAdCampaign(event)">
                    <div class="form-group">
                        <label for="campaign_product">Product / Service Name <span style="color: #f43f5e;">*</span></label>
                        <input type="text" id="campaign_product" class="form-control" placeholder="e.g. WhatsApp CRM, Lakshya AI Lead Finder" required>
                    </div>
                    <div class="form-group">
                        <label for="campaign_target_audience">Target Audience <span style="color: #f43f5e;">*</span></label>
                        <input type="text" id="campaign_target_audience" class="form-control" placeholder="e.g. Small business owners, Shopify store merchants" required>
                    </div>
                    <div class="form-group">
                        <label for="campaign_budget">Advertising Budget / Limits <span style="color: #f43f5e;">*</span></label>
                        <input type="text" id="campaign_budget" class="form-control" placeholder="e.g. $20/day, $500 total, minimal bootstrapping budget" required>
                    </div>
                    <div class="form-group">
                        <label for="campaign_goal">Campaign Objective / Goal <span style="color: #f43f5e;">*</span></label>
                        <input type="text" id="campaign_goal" class="form-control" placeholder="e.g. App downloads, lead generation form fills, newsletter signup" required>
                    </div>
                    <div class="nsfw-disclaimer">
                        <span class="nsfw-icon">⚠️</span>
                        <span><strong>Content Policy:</strong> Submitting NSFW, vulgar, sexually explicit, violent, or prohibited content will result in an automatic <span class="penalty-highlight">5-credit penalty deduction</span> from your subscription. All inputs are AI-moderated in real time.</span>
                    </div>
                    <button type="submit" id="campaignGenerateBtn" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem; height: 46px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none;">
                        <span id="campaignBtnText">⚡ Plan Advertising Campaign</span>
                        <div id="campaignBtnSpinner" class="spinner" style="display: none; margin: 0 auto;"></div>
                    </button>
                </form>
            </div>
        </div>

        <!-- Preview Column -->
        <div class="preview-section">
            <!-- SOCIAL PREVIEW -->
            <div id="preview-social" class="card tab-preview-content" style="height: 100%; display: flex; flex-direction: column;">
                <div class="card-header-flex" style="margin-bottom: 1rem;">
                    <div>
                        <h2 style="font-size: 1.1rem; font-weight: 700;">Multi-Channel Social Outbox</h2>
                        <p class="card-subtitle">Select a platform below to view, edit, and digital market the post.</p>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="socialEmptyState" style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 3rem 1rem; border: 2px dashed var(--border-color); border-radius: 12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 48px; height: 48px; color: var(--text-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; max-width: 250px;">
                        Enter details on the left and click generate to build multi-channel social posts.
                    </p>
                </div>

                <!-- Active Preview -->
                <div id="socialActiveState" style="display: none; flex-grow: 1; flex-direction: column; gap: 1.25rem;">
                    <!-- Sub-tabs/Pills to toggle between platforms -->
                    <div class="sub-tabs-container">
                        <button id="sub-social-linkedin" class="sub-tab-pill active" onclick="switchSocialPlatform('linkedin')">LinkedIn</button>
                        <button id="sub-social-twitter" class="sub-tab-pill" onclick="switchSocialPlatform('twitter')">X / Twitter</button>
                        <button id="sub-social-telegram" class="sub-tab-pill" onclick="switchSocialPlatform('telegram')">Telegram</button>
                        <button id="sub-social-facebook" class="sub-tab-pill" onclick="switchSocialPlatform('facebook')">Facebook</button>
                        <button id="sub-social-reddit" class="sub-tab-pill" onclick="switchSocialPlatform('reddit')">Reddit</button>
                    </div>

                    <!-- Post Card Preview -->
                    <div class="mock-post-card">
                        <div class="mock-post-header">
                            <div class="mock-avatar">{{ $avatarLetter }}</div>
                            <div class="mock-user-info">
                                <span class="mock-username">{{ $avatarName }}</span>
                                <span class="mock-timestamp">Just now • <span id="socialPreviewPlatformTag" class="platform-tag linkedin">LinkedIn</span></span>
                            </div>
                        </div>

                        <!-- Editable Title -->
                        <div class="form-group" style="margin-bottom: 0.5rem;">
                            <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--text-muted);">Post Headline / Catchy Hook</label>
                            <input type="text" id="socialPreviewTitle" class="form-control" style="font-weight: 800; font-size: 1.05rem; background: rgba(0,0,0,0.15);">
                        </div>

                        <!-- Editable Description -->
                        <div class="form-group" style="margin-bottom: 0.5rem;">
                            <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--text-muted);">Post Body Copy</label>
                            <textarea id="socialPreviewDescription" class="form-control" style="min-height: 140px; font-size: 0.85rem; line-height: 1.45; background: rgba(0,0,0,0.15);"></textarea>
                        </div>

                        <!-- Image Prompt Display & Dynamic Image -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--text-muted);">AI Generated Creative Graphic</label>
                            <div class="image-generator-container">
                                <div id="socialImageLoader" class="image-loader">
                                    <div class="spinner"></div>
                                    <span>Rendering Creative via Pollinations AI...</span>
                                </div>
                                <img id="socialPreviewImage" src="" alt="Campaign Graphic" style="width: 100%; height: auto; min-height: 200px; max-height: 280px; object-fit: cover; display: block;" onload="hideSocialImageLoader()" 
                                     onerror="handleSocialImageError(this)">
                            </div>
                        </div>

                        <!-- Editable Image Prompt -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--text-muted);">Image prompt (Used to render graphic)</label>
                            <textarea id="socialPreviewImagePrompt" class="form-control" style="min-height: 60px; font-size: 0.75rem; line-height: 1.4; color: var(--text-muted); background: rgba(0,0,0,0.15);" onchange="regenerateSocialPreviewImage()"></textarea>
                        </div>
                    </div>

                    <!-- Launch Buttons -->
                    <div style="display: flex; gap: 0.75rem; margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                        <button class="btn btn-secondary" onclick="resetSocialGenerator()" style="flex: 1;">✕ Reset</button>
                        <button class="btn btn-primary" onclick="triggerDigitalMarketingLaunch()" style="flex: 2; background: var(--secondary-gradient);">
                            🚀 Digital Market It!
                        </button>
                    </div>
                </div>
            </div>

            <!-- GROWTH PREVIEW -->
            <div id="preview-growth" class="card tab-preview-content" style="height: 100%; display: flex; flex-direction: column; display: none;">
                <div class="card-header-flex" style="margin-bottom: 1rem;">
                    <div>
                        <h2 style="font-size: 1.1rem; font-weight: 700;">Weekly Growth Pack</h2>
                        <p class="card-subtitle">Toggle below to review newsletter campaigns, SEO blog structures, and content schedule.</p>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="growthEmptyState" style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 3rem 1rem; border: 2px dashed var(--border-color); border-radius: 12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 48px; height: 48px; color: var(--text-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18V6.125c0-.621.504-1.125 1.125-1.125H9.75M9 5.25h6.75m-6.75 0a.75.75 0 100-1.5h6.75a.75.75 0 100 1.5M9 5.25v.375c0 .621.504 1.125 1.125 1.125h3.75A1.125 1.125 0 0015 5.625V5.25" />
                    </svg>
                    <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; max-width: 250px;">
                        Enter details on the left and click generate to build weekly growth assets.
                    </p>
                </div>

                <!-- Active Preview -->
                <div id="growthActiveState" style="display: none; flex-grow: 1; flex-direction: column; gap: 1.25rem;">
                    <!-- Sub-tabs/Pills to toggle between components -->
                    <div class="sub-tabs-container">
                        <button id="sub-growth-newsletter" class="sub-tab-pill active" onclick="switchGrowthSubTab('newsletter')">Newsletter Draft</button>
                        <button id="sub-growth-blog" class="sub-tab-pill" onclick="switchGrowthSubTab('blog')">Blog SEO Outline</button>
                        <button id="sub-growth-calendar" class="sub-tab-pill" onclick="switchGrowthSubTab('calendar')">7-Day Content Schedule</button>
                    </div>

                    <!-- Newsletter Component -->
                    <div id="growth-newsletter-content" class="sub-growth-view">
                        <div class="growth-doc-card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                                <div class="ad-badge">Newsletter Template</div>
                                <button class="copy-btn" onclick="copyText(document.getElementById('growthNewsletterBody').innerText, this)">
                                    <span>📋</span> Copy Text
                                </button>
                            </div>
                            <div class="growth-content" id="growthNewsletterBody" style="background: rgba(0,0,0,0.1); padding: 1rem; border-radius: 8px; font-family: monospace;">
                                <!-- AI Content goes here -->
                            </div>
                        </div>
                    </div>

                    <!-- Blog Component -->
                    <div id="growth-blog-content" class="sub-growth-view" style="display: none;">
                        <div class="growth-doc-card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                                <div class="ad-badge" style="background: rgba(99,102,241,0.1); color: #818cf8; border-color: rgba(99,102,241,0.2);">SEO Blog Article Outline</div>
                                <button class="copy-btn" onclick="copyText(document.getElementById('growthBlogBody').innerText, this)">
                                    <span>📋</span> Copy Text
                                </button>
                            </div>
                            <div class="growth-content" id="growthBlogBody" style="background: rgba(0,0,0,0.1); padding: 1rem; border-radius: 8px;">
                                <!-- AI Content goes here -->
                            </div>
                        </div>
                    </div>

                    <!-- Content Calendar Component -->
                    <div id="growth-calendar-content" class="sub-growth-view" style="display: none;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                            <div class="ad-badge" style="background: rgba(236,72,153,0.1); color: #f472b6; border-color: rgba(236,72,153,0.2);">7-Day Calendar</div>
                            <button class="copy-btn" onclick="copyCalendarText(this)">
                                <span>📋</span> Copy Calendar
                            </button>
                        </div>
                        <div class="calendar-grid" id="growthCalendarGrid">
                            <!-- JS will inject calendar cards -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- AD CAMPAIGN PREVIEW -->
            <div id="preview-campaign" class="card tab-preview-content" style="height: 100%; display: flex; flex-direction: column; display: none;">
                <div class="card-header-flex" style="margin-bottom: 1rem;">
                    <div>
                        <h2 style="font-size: 1.1rem; font-weight: 700;">AI Ad Campaign Hub</h2>
                        <p class="card-subtitle">Preview targeted audiences, ad copy variations, and high-converting landing page layouts.</p>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="campaignEmptyState" style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 3rem 1rem; border: 2px dashed var(--border-color); border-radius: 12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 48px; height: 48px; color: var(--text-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3" />
                    </svg>
                    <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; max-width: 250px;">
                        Enter details on the left and click generate to build your paid ads framework.
                    </p>
                </div>

                <!-- Active Preview -->
                <div id="campaignActiveState" style="display: none; flex-grow: 1; flex-direction: column; gap: 1.25rem;">
                    <!-- Sub-tabs/Pills to toggle between components -->
                    <div class="sub-tabs-container">
                        <button id="sub-campaign-targeting" class="sub-tab-pill active" onclick="switchCampaignSubTab('targeting')">Targeting & Keywords</button>
                        <button id="sub-campaign-creative" class="sub-tab-pill" onclick="switchCampaignSubTab('creative')">Ad Creative & Copy</button>
                        <button id="sub-campaign-landing" class="sub-tab-pill" onclick="switchCampaignSubTab('landing')">Landing Page Suggestion</button>
                    </div>

                    <!-- Targeting Component -->
                    <div id="campaign-targeting-content" class="sub-campaign-view">
                        <div class="growth-doc-card">
                            <div class="ad-badge" style="margin-bottom: 0.75rem;">Meta/Google Target Personas</div>
                            <div class="growth-content" id="campaignTargetingText" style="background: rgba(0,0,0,0.1); padding: 1rem; border-radius: 8px;">
                                <!-- AI Targeting goes here -->
                            </div>
                        </div>

                        <div class="growth-doc-card" style="margin-bottom: 0;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                                <div class="ad-badge" style="background: rgba(99,102,241,0.1); color: #818cf8; border-color: rgba(99,102,241,0.2);">Keywords (Match-Types)</div>
                                <button class="copy-btn" onclick="copyKeywordsText(this)">
                                    <span>📋</span> Copy Keywords
                                </button>
                            </div>
                            <div class="growth-content" id="campaignKeywordsText" style="background: rgba(0,0,0,0.1); padding: 1rem; border-radius: 8px;">
                                <!-- AI Keywords goes here -->
                            </div>
                        </div>
                    </div>

                    <!-- Creative & Copy Component -->
                    <div id="campaign-creative-content" class="sub-campaign-view" style="display: none;">
                        <div class="growth-doc-card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                                <div class="ad-badge">Search Headlines</div>
                                <button class="copy-btn" onclick="copyText(document.getElementById('campaignHeadlinesText').innerText, this)">
                                    <span>📋</span> Copy Headlines
                                </button>
                            </div>
                            <div class="growth-content" id="campaignHeadlinesText" style="background: rgba(0,0,0,0.1); padding: 1rem; border-radius: 8px; font-family: monospace;">
                                <!-- AI Headlines goes here -->
                            </div>
                        </div>

                        <div class="growth-doc-card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                                <div class="ad-badge">Ad Copy Variations</div>
                                <button class="copy-btn" onclick="copyText(document.getElementById('campaignAdCopyText').innerText, this)">
                                    <span>📋</span> Copy Copy
                                </button>
                            </div>
                            <div class="growth-content" id="campaignAdCopyText" style="background: rgba(0,0,0,0.1); padding: 1rem; border-radius: 8px;">
                                <!-- AI Ad Copy goes here -->
                            </div>
                        </div>

                        <div class="growth-doc-card" style="margin-bottom: 0;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                                <div class="ad-badge">Visual/Storyboard Concept Prompts</div>
                                <button class="copy-btn" onclick="copyText(document.getElementById('campaignStoryboardText').innerText, this)">
                                    <span>📋</span> Copy Prompts
                                </button>
                            </div>
                            <div class="growth-content" id="campaignStoryboardText" style="background: rgba(0,0,0,0.1); padding: 1rem; border-radius: 8px;">
                                <!-- AI Storyboard goes here -->
                            </div>
                        </div>
                    </div>

                    <!-- Landing Page Component -->
                    <div id="campaign-landing-content" class="sub-campaign-view" style="display: none;">
                        <div class="growth-doc-card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.75rem;">
                                <div class="ad-badge" style="background: rgba(16,185,129,0.1); color: #10b981; border-color: rgba(16,185,129,0.2);">Landing Page Suggested Strategy</div>
                                <button class="copy-btn" onclick="copyText(document.getElementById('campaignLandingText').innerText, this)">
                                    <span>📋</span> Copy Strategy
                                </button>
                            </div>
                            <div class="growth-content" id="campaignLandingText" style="background: rgba(0,0,0,0.1); padding: 1rem; border-radius: 8px;">
                                <!-- AI Landing Page goes here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Launch Progress Modal Overlay -->
<div class="modal-overlay" id="launchModal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.25rem;">🚀</span>
                <h3 style="font-size: 1.1rem; font-weight: 700;">Digital Outbox Publishing Pipeline</h3>
            </div>
        </div>
        <div class="modal-body" style="padding-top: 0.5rem;">
            <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.45;">
                Deploying marketing campaign creative package to client targets and social media API outboxes.
            </p>

            <div class="step-list">
                <div class="step-item" id="step1">
                    <div class="step-icon">
                        <div class="spinner" id="step1-spinner" style="display: none;"></div>
                        <span id="step1-num">1</span>
                    </div>
                    <span class="step-text" id="step1-text">Configuring target channel outbox metadata...</span>
                </div>
                <div class="step-item" id="step2">
                    <div class="step-icon">
                        <div class="spinner" id="step2-spinner" style="display: none;"></div>
                        <span id="step2-num">2</span>
                    </div>
                    <span class="step-text" id="step2-text">Buffering API request signatures...</span>
                </div>
                <div class="step-item" id="step3">
                    <div class="step-icon">
                        <div class="spinner" id="step3-spinner" style="display: none;"></div>
                        <span id="step3-num">3</span>
                    </div>
                    <span class="step-text" id="step3-text">Distributing graphics assets via Content Pipeline...</span>
                </div>
                <div class="step-item" id="step4">
                    <div class="step-icon">
                        <div class="spinner" id="step4-spinner" style="display: none;"></div>
                        <span id="step4-num">4</span>
                    </div>
                    <span class="step-text" id="step4-text">Registering metrics and publishing launch signatures...</span>
                </div>
            </div>

            <!-- Success State -->
            <div id="launchSuccessMessage" style="display: none; margin-top: 1rem; padding: 1rem; border-radius: 12px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); text-align: center; flex-direction: column; gap: 0.5rem; align-items: center;">
                <span style="font-size: 1.75rem;">🎉</span>
                <h4 style="font-weight: 700; color: #34d399; font-size: 0.95rem;">Campaign Successfully Launched!</h4>
                <p style="color: var(--text-muted); font-size: 0.8rem; line-height: 1.4;">
                    Your post has been distributed, logged in target outbox audit files, and alerts registered on dashboard logs.
                </p>
            </div>
        </div>
        <div class="modal-footer" style="padding: 1rem;">
            <button id="modalCloseBtn" class="btn btn-secondary" onclick="closeLaunchModal()" style="display: none; padding: 0.5rem 1rem;">Close Outbox Console</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function handleFetchResponse(res) {
        const isJson = res.headers.get('content-type')?.includes('application/json');
        return (isJson ? res.json() : Promise.resolve(null)).then(data => {
            if (!res.ok) {
                let errMsg = 'Request failed.';
                if (data) {
                    if (data.errors) {
                        errMsg = Object.values(data.errors).flat().join(' ');
                    } else {
                        errMsg = data.error || data.message || errMsg;
                    }
                } else {
                    errMsg = `Server error: ${res.status}`;
                }
                throw new Error(errMsg);
            }
            return data;
        });
    }

    // Tab State Manager
    let currentTab = 'social';
    
    function switchTab(tabName) {
        currentTab = tabName;
        
        // Update tab button classes
        document.querySelectorAll('.tab-nav-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.getElementById('tab-nav-' + tabName).classList.add('active');
        
        // Show/hide forms
        document.querySelectorAll('.tab-form-content').forEach(form => {
            form.style.display = 'none';
        });
        document.getElementById('form-' + tabName).style.display = 'block';
        
        // Show/hide previews
        document.querySelectorAll('.tab-preview-content').forEach(preview => {
            preview.style.display = 'none';
        });
        document.getElementById('preview-' + tabName).style.display = 'flex';
    }

    // --- Tab 1: Social Post Engine ---
    let currentSocialPosts = {
        linkedin: '',
        twitter: '',
        telegram: '',
        facebook: '',
        reddit: '',
        image_prompt: ''
    };
    let selectedPlatform = 'linkedin';

    function switchSocialPlatform(platform) {
        selectedPlatform = platform;
        
        document.querySelectorAll('#preview-social .sub-tab-pill').forEach(pill => {
            pill.classList.remove('active');
        });
        document.getElementById('sub-social-' + platform).classList.add('active');
        
        // Badge text
        const badge = document.getElementById('socialPreviewPlatformTag');
        badge.className = `platform-tag ${platform}`;
        badge.innerText = platform === 'twitter' ? 'X / Twitter' : platform.charAt(0).toUpperCase() + platform.slice(1);
        
        // Fill out editable preview fields
        document.getElementById('socialPreviewTitle').value = `Lakshya Campaign: ${platform.charAt(0).toUpperCase() + platform.slice(1)}`;
        document.getElementById('socialPreviewDescription').value = currentSocialPosts[platform] || '';
        document.getElementById('socialPreviewImagePrompt').value = currentSocialPosts.image_prompt || '';

        // Generate image graphic using Pollinations AI
        const previewImg = document.getElementById('socialPreviewImage');
        const imageLoader = document.getElementById('socialImageLoader');
        imageLoader.style.display = 'flex';
        previewImg.style.display = 'none';
        
        const encodedPrompt = encodeURIComponent(currentSocialPosts.image_prompt || 'digital marketing graphic');
        const randomSeed = Math.floor(Math.random() * 1000000);
        previewImg.src = `https://image.pollinations.ai/prompt/${encodedPrompt}?width=600&height=400&nologo=true&model=turbo&seed=${randomSeed}`;
        previewImg.style.display = 'block';
    }

    function generateSocialSuite(event) {
        event.preventDefault();

        const desc = document.getElementById('social_business_description').value;
        const tone = document.getElementById('social_tone').value;
        const audience = document.getElementById('social_target_audience').value;
        const cta = document.getElementById('social_cta').value;

        const btn = document.getElementById('socialGenerateBtn');
        const text = document.getElementById('socialBtnText');
        const spinner = document.getElementById('socialBtnSpinner');

        btn.disabled = true;
        text.style.display = 'none';
        spinner.style.display = 'block';

        fetch('{{ $generateSocialRoute }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                business_description: desc,
                tone: tone,
                target_audience: audience,
                cta: cta
            })
        })
        .then(handleFetchResponse)
        .then(data => {
            btn.disabled = false;
            text.style.display = 'inline-block';
            spinner.style.display = 'none';

            if (data.success) {
                currentSocialPosts = data.posts;

                document.getElementById('socialEmptyState').style.display = 'none';
                document.getElementById('socialActiveState').style.display = 'flex';
                
                switchSocialPlatform('linkedin');
            } else {
                window.showToast('Generation Error: ' + (data.error || 'Unknown error occurred.'), 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            text.style.display = 'inline-block';
            spinner.style.display = 'none';
            console.error('Error during generation:', err);
            window.showToast('Generation Error: ' + err.message, 'error');
        });
    }

    function hideSocialImageLoader() {
        document.getElementById('socialImageLoader').style.display = 'none';
    }

    function regenerateSocialPreviewImage() {
        const customPrompt = document.getElementById('socialPreviewImagePrompt').value;
        if (!customPrompt.trim()) return;

        const imageLoader = document.getElementById('socialImageLoader');
        const previewImg = document.getElementById('socialPreviewImage');
        
        imageLoader.style.display = 'flex';
        previewImg.style.display = 'none';

        const encodedPrompt = encodeURIComponent(customPrompt);
        const randomSeed = Math.floor(Math.random() * 1000000);
        previewImg.src = `https://image.pollinations.ai/prompt/${encodedPrompt}?width=600&height=400&nologo=true&model=turbo&seed=${randomSeed}`;
        previewImg.style.display = 'block';
        
        currentSocialPosts.image_prompt = customPrompt;
    }

    function resetSocialGenerator() {
        document.getElementById('socialActiveState').style.display = 'none';
        document.getElementById('socialEmptyState').style.display = 'flex';
        document.getElementById('socialGeneratorForm').reset();
    }


    // --- Tab 2: Weekly Growth Pack ---
    let currentGrowthPack = {
        blog_outline: '',
        newsletter_copy: '',
        content_calendar: []
    };

    function switchGrowthSubTab(subTab) {
        document.querySelectorAll('#preview-growth .sub-tab-pill').forEach(pill => {
            pill.classList.remove('active');
        });
        document.getElementById('sub-growth-' + subTab).classList.add('active');

        document.querySelectorAll('.sub-growth-view').forEach(view => {
            view.style.display = 'none';
        });
        document.getElementById('growth-' + subTab + '-content').style.display = 'block';
    }

    function generateGrowthSuite(event) {
        event.preventDefault();

        const desc = document.getElementById('growth_business_description').value;
        const audience = document.getElementById('growth_target_audience').value;
        const goal = document.getElementById('growth_campaign_goal').value;

        const btn = document.getElementById('growthGenerateBtn');
        const text = document.getElementById('growthBtnText');
        const spinner = document.getElementById('growthBtnSpinner');

        btn.disabled = true;
        text.style.display = 'none';
        spinner.style.display = 'block';

        fetch('{{ $generateGrowthRoute }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                business_description: desc,
                target_audience: audience,
                campaign_goal: goal
            })
        })
        .then(handleFetchResponse)
        .then(data => {
            btn.disabled = false;
            text.style.display = 'inline-block';
            spinner.style.display = 'none';

            if (data.success) {
                currentGrowthPack = data.growth_pack;

                document.getElementById('growthNewsletterBody').innerText = currentGrowthPack.newsletter_copy;
                document.getElementById('growthBlogBody').innerText = currentGrowthPack.blog_outline;

                // Load Calendar Grid
                const calendarGrid = document.getElementById('growthCalendarGrid');
                calendarGrid.innerHTML = '';
                const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                
                const schedule = currentGrowthPack.content_calendar;
                for (let i = 0; i < 7; i++) {
                    const dayName = days[i];
                    const dayText = schedule[i] || 'Relax & monitor campaign metrics.';
                    
                    const card = document.createElement('div');
                    card.className = 'calendar-day-card';
                    card.innerHTML = `
                        <div class="calendar-day-badge">${dayName}</div>
                        <div class="calendar-day-text">${dayText}</div>
                    `;
                    calendarGrid.appendChild(card);
                }

                document.getElementById('growthEmptyState').style.display = 'none';
                document.getElementById('growthActiveState').style.display = 'flex';
                
                switchGrowthSubTab('newsletter');
            } else {
                window.showToast('Generation Error: ' + (data.error || 'Unknown error occurred.'), 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            text.style.display = 'inline-block';
            spinner.style.display = 'none';
            console.error('Error during generation:', err);
            window.showToast('Generation Error: ' + err.message, 'error');
        });
    }

    function copyCalendarText(btn) {
        if (!currentGrowthPack.content_calendar || currentGrowthPack.content_calendar.length === 0) return;
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        let text = "7-Day Content Schedule:\n\n";
        for (let i = 0; i < 7; i++) {
            text += `${days[i]}: ${currentGrowthPack.content_calendar[i] || 'N/A'}\n`;
        }
        copyText(text, btn);
    }


    // --- Tab 3: Ad Campaign Suite ---
    let currentCampaignPack = {
        target_audience: '',
        keywords: [],
        headlines: [],
        ad_copy: [],
        creative_ideas: [],
        landing_page: ''
    };

    function switchCampaignSubTab(subTab) {
        document.querySelectorAll('#preview-campaign .sub-tab-pill').forEach(pill => {
            pill.classList.remove('active');
        });
        document.getElementById('sub-campaign-' + subTab).classList.add('active');

        document.querySelectorAll('.sub-campaign-view').forEach(view => {
            view.style.display = 'none';
        });
        document.getElementById('campaign-' + subTab + '-content').style.display = 'block';
    }

    function generateAdCampaign(event) {
        event.preventDefault();

        const product = document.getElementById('campaign_product').value;
        const audience = document.getElementById('campaign_target_audience').value;
        const budget = document.getElementById('campaign_budget').value;
        const goal = document.getElementById('campaign_goal').value;

        const btn = document.getElementById('campaignGenerateBtn');
        const text = document.getElementById('campaignBtnText');
        const spinner = document.getElementById('campaignBtnSpinner');

        btn.disabled = true;
        text.style.display = 'none';
        spinner.style.display = 'block';

        fetch('{{ $generateCampaignRoute }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product: product,
                target_audience: audience,
                budget: budget,
                campaign_goal: goal
            })
        })
        .then(handleFetchResponse)
        .then(data => {
            btn.disabled = false;
            text.style.display = 'inline-block';
            spinner.style.display = 'none';

            if (data.success) {
                currentCampaignPack = data.campaign;

                document.getElementById('campaignTargetingText').innerText = currentCampaignPack.target_audience;
                document.getElementById('campaignKeywordsText').innerHTML = currentCampaignPack.keywords.map(kw => `<div>• ${kw}</div>`).join('');
                document.getElementById('campaignHeadlinesText').innerText = currentCampaignPack.headlines.map((hl, index) => `${index + 1}. ${hl}`).join('\n');
                document.getElementById('campaignAdCopyText').innerText = currentCampaignPack.ad_copy.map((copy, index) => `Variation ${index + 1}:\n${copy}\n`).join('\n');
                document.getElementById('campaignStoryboardText').innerText = currentCampaignPack.creative_ideas.map((idea, index) => `Concept ${index + 1}:\n${idea}\n`).join('\n');
                document.getElementById('campaignLandingText').innerText = currentCampaignPack.landing_page;

                document.getElementById('campaignEmptyState').style.display = 'none';
                document.getElementById('campaignActiveState').style.display = 'flex';
                
                switchCampaignSubTab('targeting');
            } else {
                window.showToast('Generation Error: ' + (data.error || 'Unknown error occurred.'), 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            text.style.display = 'inline-block';
            spinner.style.display = 'none';
            console.error('Error during generation:', err);
            window.showToast('Generation Error: ' + err.message, 'error');
        });
    }

    function copyKeywordsText(btn) {
        if (!currentCampaignPack.keywords || currentCampaignPack.keywords.length === 0) return;
        const text = currentCampaignPack.keywords.map(kw => kw).join('\n');
        copyText(text, btn);
    }


    // --- Global Helpers ---
    function copyText(text, buttonEl) {
        navigator.clipboard.writeText(text).then(() => {
            const originalHTML = buttonEl.innerHTML;
            buttonEl.innerHTML = '✓ Copied';
            buttonEl.style.color = '#34d399';
            setTimeout(() => {
                buttonEl.innerHTML = originalHTML;
                buttonEl.style.color = '';
            }, 2000);
            window.showToast('Copied to clipboard!', 'success');
        }).catch(err => {
            window.showToast('Failed to copy to clipboard', 'error');
        });
    }

    // --- Digital Marketing Publishing Modal Stepper ---
    function triggerDigitalMarketingLaunch() {
        const finalTitle = document.getElementById('socialPreviewTitle').value;
        const finalDesc = document.getElementById('socialPreviewDescription').value;
        const finalImagePrompt = document.getElementById('socialPreviewImagePrompt').value;

        // Open launch overlay
        const launchModal = document.getElementById('launchModal');
        launchModal.classList.add('active');

        // Reset step animations
        const steps = ['step1', 'step2', 'step3', 'step4'];
        steps.forEach(id => {
            const el = document.getElementById(id);
            el.className = 'step-item';
            document.getElementById(`${id}-spinner`).style.display = 'none';
            document.getElementById(`${id}-num`).style.display = 'inline';
            
            const iconWrapper = el.querySelector('.step-icon');
            iconWrapper.innerHTML = `<div class="spinner" id="${id}-spinner" style="display: none;"></div><span id="${id}-num">${id.replace('step', '')}</span>`;
        });
        document.getElementById('launchSuccessMessage').style.display = 'none';
        document.getElementById('modalCloseBtn').style.display = 'none';

        // Animate Pipeline step 1
        runStep('step1', 800, () => {
            runStep('step2', 800, () => {
                runStep('step3', 900, () => {
                    runStep('step4', 1000, () => {
                        // Submit backend call
                        fetch('{{ $launchRoute }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                title: finalTitle,
                                description: finalDesc,
                                platform: selectedPlatform,
                                image_prompt: finalImagePrompt
                            })
                        })
                        .then(handleFetchResponse)
                        .then(data => {
                            if (data.success) {
                                document.getElementById('launchSuccessMessage').style.display = 'flex';
                                document.getElementById('modalCloseBtn').style.display = 'inline-block';
                            } else {
                                window.showToast('Error launching campaign: ' + (data.error || 'Unknown error.'), 'error');
                                closeLaunchModal();
                            }
                        })
                        .catch(err => {
                            console.error('Launch request failed:', err);
                            window.showToast('Launch Error: ' + err.message, 'error');
                            closeLaunchModal();
                        });
                    });
                });
            });
        });
    }

    function runStep(stepId, duration, next) {
        const item = document.getElementById(stepId);
        item.classList.add('active');
        
        const spinner = document.getElementById(`${stepId}-spinner`);
        const num = document.getElementById(`${stepId}-num`);
        
        spinner.style.display = 'block';
        num.style.display = 'none';

        setTimeout(() => {
            spinner.style.display = 'none';
            num.style.display = 'none';
            item.classList.remove('active');
            item.classList.add('completed');
            
            const iconWrapper = item.querySelector('.step-icon');
            iconWrapper.innerHTML = '✓';

            if (next) next();
        }, duration);
    }

    function closeLaunchModal() {
        document.getElementById('launchModal').classList.remove('active');
        resetSocialGenerator();
    }

    function handleSocialImageError(imgEl) {
        const currentSrc = imgEl.src;
        
        const unsplashCurated = [
            'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1432888622747-4eb9a8efeb07?w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600&auto=format&fit=crop'
        ];

        console.warn("Image load failed for source: " + currentSrc);
        
        if (currentSrc.includes('pollinations.ai')) {
            const promptText = document.getElementById('socialPreviewImagePrompt').value || 'marketing';
            const keyword = encodeURIComponent(promptText.split(' ').slice(0, 2).join(','));
            imgEl.src = `https://loremflickr.com/600/400/${keyword}?random=${Math.floor(Math.random() * 1000)}`;
            console.log("Fallback 1: Switching to LoremFlickr");
        } else if (currentSrc.includes('loremflickr.com')) {
            const promptText = document.getElementById('socialPreviewImagePrompt').value || 'Campaign Creative';
            const cleanText = encodeURIComponent(promptText.slice(0, 40) + (promptText.length > 40 ? '...' : ''));
            imgEl.src = `https://placehold.co/600x400/0f172a/ffffff/png?text=${cleanText}`;
            console.log("Fallback 2: Switching to Placehold.co");
        } else if (currentSrc.includes('placehold.co')) {
            const randomIndex = Math.floor(Math.random() * unsplashCurated.length);
            imgEl.src = unsplashCurated[randomIndex];
            console.log("Fallback 3: Switching to Unsplash Curated");
        } else {
            hideSocialImageLoader();
            imgEl.onerror = null;
            imgEl.src = 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600';
            console.log("Ultimate Fallback: Static Unsplash");
        }
    }
</script>
@endsection
