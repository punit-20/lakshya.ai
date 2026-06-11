@extends('layouts.admin')

@section('title', 'AI Marketer')

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

    .mock-post-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: white;
        line-height: 1.3;
    }

    .mock-post-desc {
        font-size: 0.9rem;
        color: var(--text-main);
        line-height: 1.5;
        white-space: pre-wrap;
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

    .prompt-footer {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.4;
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
</style>
@endsection

@section('content')
<div class="marketing-wrapper">
    <div class="section-title-wrapper">
        <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">AI Marketer</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
            Generate high-converting digital marketing campaigns in seconds with Gemini 2.5 Flash, review outputs, and publish instantly.
        </p>
    </div>

    <div class="marketing-layout">
        <!-- Input Form Column -->
        <div class="form-section">
            <div class="card">
                <div class="card-header-flex">
                    <h2 style="font-size: 1.1rem; font-weight: 700;">Campaign Context Creator</h2>
                    <span class="badge badge-new">Lakshya AI</span>
                </div>

                <form id="marketingGeneratorForm" onsubmit="generateCampaign(event)">
                    <div class="form-group">
                        <label for="business_description">Client Business / Work Description <span style="color: #f43f5e;">*</span></label>
                        <textarea id="business_description" class="form-control" placeholder="Describe the business, its products, or services. What makes it special? (e.g., 'A local bakery that bakes organic, wood-fired sourdough bread daily using heritage grains.')" required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="platform">Target Platform <span style="color: #f43f5e;">*</span></label>
                            <select id="platform" class="project-selector" style="width: 100%; height: 42px;">
                                <option value="linkedin">LinkedIn</option>
                                <option value="twitter">Twitter / X</option>
                                <option value="reddit">Reddit</option>
                                <option value="facebook">Facebook</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tone">Tone of Voice <span style="color: #f43f5e;">*</span></label>
                            <select id="tone" class="project-selector" style="width: 100%; height: 42px;">
                                <option value="Creative & Friendly">Creative & Friendly</option>
                                <option value="Professional & Authoritative">Professional & Authoritative</option>
                                <option value="Bold & Disruptive">Bold & Disruptive</option>
                                <option value="Helpful & Informative">Helpful & Informative</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="target_audience">Target Audience <span style="color: #f43f5e;">*</span></label>
                        <input type="text" id="target_audience" class="form-control" placeholder="e.g. Local foodies, tech startup founders, remote engineers" required>
                    </div>

                    <div class="form-group">
                        <label for="cta">Call to Action / Special Offers (Optional)</label>
                        <input type="text" id="cta" class="form-control" placeholder="e.g. Buy 1 get 1 free this weekend! / Sign up for free trial at website.com">
                    </div>

                    <button type="submit" id="generateBtn" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem; height: 46px;">
                        <span id="btnText">⚡ Generate Campaign Creative</span>
                        <div id="btnSpinner" class="spinner" style="display: none;"></div>
                    </button>
                </form>
            </div>
        </div>

        <!-- Preview Column -->
        <div class="preview-section">
            <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                <div class="card-header-flex" style="margin-bottom: 1rem;">
                    <div>
                        <h2 style="font-size: 1.1rem; font-weight: 700;">Live Campaign Outbox Preview</h2>
                        <p class="card-subtitle">Review, edit, and digital market the final outputs.</p>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="previewEmptyState" style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 3rem 1rem; border: 2px dashed var(--border-color); border-radius: 12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 48px; height: 48px; color: var(--text-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; max-width: 250px;">
                        Enter campaign details on the left and click generate to build mock-up creatives.
                    </p>
                </div>

                <!-- Active Preview Container -->
                <div id="previewActiveState" style="display: none; flex-grow: 1; flex-direction: column; gap: 1.25rem;">
                    
                    <!-- Post Preview -->
                    <div class="mock-post-card">
                        <div class="mock-post-header">
                            <div class="mock-avatar">L</div>
                            <div class="mock-user-info">
                                <span class="mock-username">Lakshya Marketer</span>
                                <span class="mock-timestamp">Just now • <span id="previewPlatformTag" class="platform-tag linkedin">LinkedIn</span></span>
                            </div>
                        </div>

                        <!-- Editable Title -->
                        <div class="form-group" style="margin-bottom: 0.5rem;">
                            <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--text-muted);">Campaign Headline</label>
                            <input type="text" id="previewTitle" class="form-control" style="font-weight: 800; font-size: 1.05rem; background: rgba(0,0,0,0.15);">
                        </div>

                        <!-- Editable Description -->
                        <div class="form-group" style="margin-bottom: 0.5rem;">
                            <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--text-muted);">Campaign Body Description</label>
                            <textarea id="previewDescription" class="form-control" style="min-height: 140px; font-size: 0.85rem; line-height: 1.45; background: rgba(0,0,0,0.15);"></textarea>
                        </div>

                        <!-- Image Prompt Display & Dynamic Image -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--text-muted);">AI Generated Creative Graphic</label>
                            <div class="image-generator-container">
                                <div id="imageLoader" class="image-loader">
                                    <div class="spinner"></div>
                                    <span>Rendering Creative via Pollinations AI...</span>
                                </div>
                                <img id="previewImage" src="" alt="Campaign Graphic" style="width: 100%; height: auto; min-height: 200px; max-height: 280px; object-fit: cover; display: block;" onload="hideImageLoader()" onerror="hideImageLoader(); this.onerror=null; this.src='https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600';">
                            </div>
                        </div>

                        <!-- Editable Image Prompt -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--text-muted);">Image prompt (Used to render graphic)</label>
                            <textarea id="previewImagePrompt" class="form-control" style="min-height: 60px; font-size: 0.75rem; line-height: 1.4; color: var(--text-muted); background: rgba(0,0,0,0.15);" onchange="regeneratePreviewImage()"></textarea>
                        </div>
                    </div>

                    <!-- Launch Buttons -->
                    <div style="display: flex; gap: 0.75rem; margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                        <button class="btn btn-secondary" onclick="resetCampaignGenerator()" style="flex: 1;">✕ Reset</button>
                        <button class="btn btn-primary" onclick="triggerDigitalMarketingLaunch()" style="flex: 2; background: var(--secondary-gradient);">
                            🚀 Digital Market It!
                        </button>
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
    let currentCampaign = {
        title: '',
        description: '',
        platform: '',
        image_prompt: ''
    };

    function generateCampaign(event) {
        event.preventDefault();
        
        const desc = document.getElementById('business_description').value;
        const platform = document.getElementById('platform').value;
        const tone = document.getElementById('tone').value;
        const audience = document.getElementById('target_audience').value;
        const cta = document.getElementById('cta').value;

        // UI Loading States
        const generateBtn = document.getElementById('generateBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        generateBtn.disabled = true;
        btnText.style.display = 'none';
        btnSpinner.style.display = 'block';

        fetch('{{ route("admin.marketing.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                business_description: desc,
                platform: platform,
                tone: tone,
                target_audience: audience,
                cta: cta
            })
        })
        .then(res => res.json())
        .then(data => {
            generateBtn.disabled = false;
            btnText.style.display = 'inline-block';
            btnSpinner.style.display = 'none';

            if (data.success) {
                currentCampaign.title = data.title;
                currentCampaign.description = data.description;
                currentCampaign.platform = platform;
                currentCampaign.image_prompt = data.image_prompt;

                // Load preview
                document.getElementById('previewTitle').value = data.title;
                document.getElementById('previewDescription').value = data.description;
                document.getElementById('previewImagePrompt').value = data.image_prompt;

                // Platform Badge
                const platBadge = document.getElementById('previewPlatformTag');
                platBadge.className = `platform-tag ${platform}`;
                platBadge.innerText = platform.charAt(0).toUpperCase() + platform.slice(1);

                // Set image src using Pollinations AI (random seed to prevent caching)
                const imageLoader = document.getElementById('imageLoader');
                const previewImg = document.getElementById('previewImage');
                imageLoader.style.display = 'flex';
                previewImg.style.display = 'none';

                const encodedPrompt = encodeURIComponent(data.image_prompt);
                const randomSeed = Math.floor(Math.random() * 1000000);
                previewImg.src = `https://image.pollinations.ai/prompt/${encodedPrompt}?width=600&height=400&nologo=true&seed=${randomSeed}`;
                previewImg.style.display = 'block';

                // Toggle visibility states
                document.getElementById('previewEmptyState').style.display = 'none';
                document.getElementById('previewActiveState').style.display = 'flex';
            } else {
                alert('Generation Error: ' + (data.error || 'Unknown error occurred.'));
            }
        })
        .catch(err => {
            generateBtn.disabled = false;
            btnText.style.display = 'inline-block';
            btnSpinner.style.display = 'none';
            console.error('Error during generation:', err);
            alert('Failed to generate marketing post. Check server configurations and try again.');
        });
    }

    function hideImageLoader() {
        document.getElementById('imageLoader').style.display = 'none';
    }

    function regeneratePreviewImage() {
        const customPrompt = document.getElementById('previewImagePrompt').value;
        if (!customPrompt.trim()) return;

        const imageLoader = document.getElementById('imageLoader');
        const previewImg = document.getElementById('previewImage');
        
        imageLoader.style.display = 'flex';
        previewImg.style.display = 'none';

        const encodedPrompt = encodeURIComponent(customPrompt);
        const randomSeed = Math.floor(Math.random() * 1000000);
        previewImg.src = `https://image.pollinations.ai/prompt/${encodedPrompt}?width=600&height=400&nologo=true&seed=${randomSeed}`;
        previewImg.style.display = 'block';
        currentCampaign.image_prompt = customPrompt;
    }

    function resetCampaignGenerator() {
        document.getElementById('previewActiveState').style.display = 'none';
        document.getElementById('previewEmptyState').style.display = 'flex';
        document.getElementById('marketingGeneratorForm').reset();
    }

    function triggerDigitalMarketingLaunch() {
        // Retrieve latest values from edit form
        const finalTitle = document.getElementById('previewTitle').value;
        const finalDesc = document.getElementById('previewDescription').value;
        const finalImagePrompt = document.getElementById('previewImagePrompt').value;

        // Open launch console overlay
        const launchModal = document.getElementById('launchModal');
        launchModal.classList.add('active');

        // Reset step animations
        const steps = ['step1', 'step2', 'step3', 'step4'];
        steps.forEach(id => {
            const el = document.getElementById(id);
            el.className = 'step-item';
            document.getElementById(`${id}-spinner`).style.display = 'none';
            document.getElementById(`${id}-num`).style.display = 'inline';
            
            // Restore initial number or state
            const iconWrapper = el.querySelector('.step-icon');
            iconWrapper.innerHTML = `<div class="spinner" id="${id}-spinner" style="display: none;"></div><span id="${id}-num">${id.replace('step', '')}</span>`;
        });
        document.getElementById('launchSuccessMessage').style.display = 'none';
        document.getElementById('modalCloseBtn').style.display = 'none';

        // Animate Step 1
        runStep('step1', 800, () => {
            // Animate Step 2
            runStep('step2', 800, () => {
                // Animate Step 3
                runStep('step3', 900, () => {
                    // Animate Step 4
                    runStep('step4', 1000, () => {
                        // Submit backend call
                        fetch('{{ route("admin.marketing.launch") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                title: finalTitle,
                                description: finalDesc,
                                platform: currentCampaign.platform,
                                image_prompt: finalImagePrompt
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('launchSuccessMessage').style.display = 'flex';
                                document.getElementById('modalCloseBtn').style.display = 'inline-block';
                            } else {
                                alert('Error launching campaign: ' + (data.error || 'Unknown error.'));
                                closeLaunchModal();
                            }
                        })
                        .catch(err => {
                            console.error('Launch request failed:', err);
                            alert('Outbox connection failed. Campaign wasn\'t fully published.');
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
            
            // Checkmark icon display
            const iconWrapper = item.querySelector('.step-icon');
            iconWrapper.innerHTML = '✓';

            if (next) next();
        }, duration);
    }

    function closeLaunchModal() {
        document.getElementById('launchModal').classList.remove('active');
        // Reset state after successful launch
        resetCampaignGenerator();
    }
</script>
@endsection
