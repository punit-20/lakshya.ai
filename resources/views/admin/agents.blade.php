@extends('layouts.admin')

@section('title', 'AI Autonomous Agents')

@section('styles')
<style>
    /* Premium Page Styles */
    .agents-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        animation: fadeIn 0.4s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Tabs Styling */
    .tab-nav {
        display: flex;
        gap: 0.5rem;
        background: rgba(18, 24, 38, 0.4);
        padding: 0.35rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        margin-bottom: 0.5rem;
        overflow-x: auto;
    }
    
    .tab-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 0.6rem 1.2rem;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }
    
    .tab-btn:hover {
        color: white;
        background: rgba(255, 255, 255, 0.03);
    }
    
    .tab-btn.active {
        color: white;
        background: var(--primary-gradient);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.25);
    }

    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Grid layout for Agents */
    .agent-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    
    .agent-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 180px;
        border-left: 3px solid var(--primary-color);
    }
    
    .agent-card.idle {
        border-left-color: var(--text-dark);
    }
    
    .agent-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .agent-title {
        font-size: 1rem;
        font-weight: 700;
        color: white;
    }

    .agent-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.4;
        margin-bottom: 1rem;
    }

    /* Toggle Switches styling */
    .switch {
        position: relative;
        display: inline-block;
        width: 38px;
        height: 20px;
    }

    .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(255,255,255,0.08);
        transition: .3s;
        border-radius: 20px;
        border: 1px solid var(--border-color);
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 2px;
        bottom: 2px;
        background-color: #9ca3af;
        transition: .3s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background: var(--primary-gradient);
    }

    input:checked + .slider:before {
        transform: translateX(18px);
        background-color: white;
    }

    /* Terminal Console Panel styles */
    .console-panel {
        background: #020408;
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 0 0 12px 12px;
        font-family: 'Consolas', 'Fira Code', 'Courier New', monospace;
        color: #e2e8f0;
        padding: 1.25rem;
        font-size: 0.8rem;
        height: 380px;
        overflow-y: auto;
        box-shadow: inset 0 4px 20px rgba(0,0,0,0.9);
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.1) transparent;
    }

    .console-panel::-webkit-scrollbar {
        width: 6px;
    }
    .console-panel::-webkit-scrollbar-thumb {
        background-color: rgba(255,255,255,0.1);
        border-radius: 3px;
    }

    .console-line {
        line-height: 1.6;
        margin-bottom: 0.4rem;
        white-space: pre-wrap;
        border-left: 2px solid transparent;
        padding-left: 8px;
    }

    .console-line.info { border-left-color: #4b5563; }
    .console-line.success { border-left-color: #10b981; }
    .console-line.warning { border-left-color: #f59e0b; }
    .console-line.error { border-left-color: #ef4444; font-weight: bold; }

    /* Heartbeat pulses */
    .heartbeat-dot {
        width: 8px;
        height: 8px;
        background-color: #34d399;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 8px #34d399;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(0.9); opacity: 0.6; }
        50% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(0.9); opacity: 0.6; }
    }

    /* Code Block copy utility */
    .code-block {
        background: rgba(0,0,0,0.2);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-family: monospace;
        font-size: 0.8rem;
        color: #60a5fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1rem 0;
    }
</style>
@endsection

@section('content')
<div class="agents-container">
    
    <!-- Top Header section -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">AI Autonomous Agents Control</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Deploy, monitor, and configure task parameters for autonomous lead intelligence queues.</p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span class="badge badge-qualified" style="padding: 0.5rem 1rem; border: 1px solid var(--status-qualified-border); background: rgba(52, 211, 153, 0.05);">
                <span class="heartbeat-dot"></span> &nbsp; Agent Worker: Running
            </span>
            <button class="btn btn-secondary" onclick="refreshAllData()">
                <span>Sync Data</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Overview Grid -->
    <div class="stats-grid">
        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Active Deployments</span>
                <span class="stat-value">7 / 9</span>
                <p style="font-size: 0.75rem; color: var(--text-muted);">AI Agents currently online</p>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: var(--primary-color);">
                🤖
            </div>
        </div>

        <div class="card stat-card" style="justify-content: space-around;">
            <div class="gauge-container" style="width: 100px; height: 100px;">
                <svg class="gauge-svg" viewBox="0 0 100 100" width="100%" height="100%">
                    <circle class="gauge-circle-bg" cx="50" cy="50" r="40" />
                    @php
                        $dashoffset = 251.2 - (251.2 * $successRate) / 100;
                    @endphp
                    <circle class="gauge-circle-fill" cx="50" cy="50" r="40" 
                            stroke-dasharray="251.2" 
                            stroke-dashoffset="{{ $dashoffset }}" />
                </svg>
                <div class="gauge-center-text">
                    <span class="gauge-value" style="font-size: 1.25rem;">{{ $successRate }}%</span>
                    <span class="gauge-label" style="font-size: 0.55rem;">Success</span>
                </div>
            </div>
            <div class="stat-info">
                <span class="stat-label">Queue Health</span>
                <span class="stat-value" style="font-size: 1.4rem;">Success</span>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Task run efficiency</p>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Tracked Visitors</span>
                <span class="stat-value" id="stats-visitor-count">{{ count($visitors) }}</span>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Company profiles resolved</p>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                🕵️
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Outreach Transmitted</span>
                <span class="stat-value">{{ count($emailLogs) + count($whatsappLogs) + count($linkedinLogs) }}</span>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Emails, WhatsApp & DMs</p>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(168, 85, 247, 0.1); color: #a855f7;">
                ✉️
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tab-nav">
        <button id="tab-btn-center" class="tab-btn active" onclick="switchTab('center')">🤖 Agent Control Grid</button>
        <button id="tab-btn-visitor" class="tab-btn" onclick="switchTab('visitor')">🕵️ Visitor Intelligence</button>
        <button id="tab-btn-email" class="tab-btn" onclick="switchTab('email')">📧 Cold Email Warmup & Logs</button>
        <button id="tab-btn-whatsapp" class="tab-btn" onclick="switchTab('whatsapp')">💬 WhatsApp Outreach</button>
        <button id="tab-btn-linkedin" class="tab-btn" onclick="switchTab('linkedin')">💼 LinkedIn Logs</button>
        <button id="tab-btn-queue" class="tab-btn" onclick="switchTab('queue')">⚙️ Queue Console</button>
    </div>

    <!-- TAB 1: AGENT GRID -->
    <div id="tab-content-center" class="tab-content active">
        <div class="agent-grid">
            @foreach($agents as $type => $data)
                <div class="card agent-card {{ $data['status'] == 'Idle' ? 'idle' : '' }}" id="agent-card-{{ $type }}">
                    <div>
                        <div class="agent-header">
                            <div>
                                <span class="agent-title">{{ $data['name'] }}</span>
                                <p style="font-size: 0.7rem; color: var(--primary-color); font-weight: 700; margin-top: 0.1rem;">{{ $type }}</p>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="toggle-{{ $type }}" {{ $data['status'] == 'Active' ? 'checked' : '' }} onchange="toggleAgentStatus('{{ $type }}')">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <p class="agent-desc">{{ $data['desc'] }}</p>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: 0.5rem;">
                        <span class="badge {{ $data['status'] == 'Active' ? 'badge-qualified' : 'badge-paused' }}" style="font-size: 0.65rem;" id="badge-{{ $type }}">
                            {{ $data['status'] }}
                        </span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: white;" id="metric-{{ $type }}">{{ $data['metric'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Agent Launcher Card -->
        <div class="card" style="margin-top: 1.5rem; border-color: rgba(99, 102, 241, 0.2);">
            <h2 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Manual Agent Task Dispatcher</h2>
            <form id="dispatch-task-form" onsubmit="dispatchAgentTask(event)" style="display: grid; grid-template-columns: repeat(3, 1fr) auto; gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="dispatch-agent">Target Agent</label>
                    <select class="form-control" id="dispatch-agent" required>
                        <option value="LeadHunterAgent">LeadHunterAgent (Platform Search)</option>
                        <option value="EmailAgent">EmailAgent (Outreach email)</option>
                        <option value="WhatsAppAgent">WhatsAppAgent (WhatsApp template)</option>
                        <option value="LinkedInAgent">LinkedInAgent (LinkedIn Outreach)</option>
                        <option value="SEOAgent">SEOAgent (Generate Blog post)</option>
                        <option value="CompetitorAgent">CompetitorAgent (Competitor Monitoring)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="dispatch-title">Task Operation Description</label>
                    <input type="text" class="form-control" id="dispatch-title" placeholder="e.g. Scan Reddit for custom chatbots" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="dispatch-lead">Associate with Lead (Optional)</label>
                    <select class="form-control" id="dispatch-lead">
                        <option value="">None - Bulk Campaign</option>
                        @foreach($leads as $l)
                            <option value="{{ $l->id }}">{{ $l->contact_name }} (Intent: {{ $l->score }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Dispatch to Queue</button>
            </form>
        </div>
    </div>

    <!-- TAB 2: VISITOR TRACKER -->
    <div id="tab-content-visitor" class="tab-content">
        <div class="card" style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: white;">🎯 Install Visitor Intel Script</h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">Insert this tracking snippet directly into the header block of your client's commercial website. Our script parses visitor IPs, crawls buying pathways, scores target conversion likelihoods, and logs them in real time.</p>
            <div class="code-block">
                <span id="tracker-script-copy">&lt;script src="https://lakshya.ai/js/tracker.js" data-project-id="{{ $project->id }}"&gt;&lt;/script&gt;</span>
                <button class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;" onclick="copyScriptText()">Copy Script</button>
            </div>
        </div>

        <div class="card">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: white; margin-bottom: 1rem;">Real-time Visitor Signal Stream</h2>
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Company / Source</th>
                            <th>IP Address</th>
                            <th>Target Pageviews Crawled</th>
                            <th>Buying Intent Score</th>
                            <th>Recorded At</th>
                            <th>Action status</th>
                        </tr>
                    </thead>
                    <tbody id="visitor-hits-tbody">
                        @forelse($visitors as $v)
                            <tr>
                                <td>
                                    <strong style="color: white; display: block;">🏢 {{ $v->company_name ?? 'Resolving Identity...' }}</strong>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">B2B Lead intelligence</span>
                                </td>
                                <td><code>{{ $v->ip_address }}</code></td>
                                <td style="max-width: 250px;">
                                    @foreach($v->pages_visited as $page)
                                        <span class="badge badge-paused" style="font-size: 0.65rem; border: none; padding: 0.1rem 0.35rem; margin: 0.05rem 0;">{{ $page }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($v->intent_score >= 80)
                                        <span class="badge badge-closed" style="background: rgba(244, 63, 94, 0.15); color: #fb7185; border: 1px solid rgba(244,63,94,0.3); box-shadow: 0 0 8px rgba(244,63,94,0.2);">
                                            🔥 {{ $v->intent_score }} / 100
                                        </span>
                                    @else
                                        <span class="badge badge-discovered" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3);">
                                            ⚡ {{ $v->intent_score }} / 100
                                        </span>
                                    @endif
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">{{ $v->created_at->diffForHumans() }}</td>
                                <td>
                                    <span class="badge badge-qualified">Lead Created</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No visitor tracking logs recorded. Copy and install the tracking script.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: COLD EMAIL -->
    <div id="tab-content-email" class="tab-content">
        <!-- Warmup widget -->
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                <div class="gauge-container" style="width: 120px; height: 120px; margin-bottom: 1rem;">
                    <svg class="gauge-svg" viewBox="0 0 100 100" width="100%" height="100%">
                        <circle class="gauge-circle-bg" cx="50" cy="50" r="40" />
                        <circle class="gauge-circle-fill" cx="50" cy="50" r="40" 
                                stroke-dasharray="251.2" 
                                stroke-dashoffset="38" style="stroke: #a855f7; filter: drop-shadow(0 0 4px #a855f7);" />
                    </svg>
                    <div class="gauge-center-text">
                        <span class="gauge-value" style="color: #c084fc;">85%</span>
                        <span class="gauge-label">Warmup</span>
                    </div>
                </div>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: white;">Sender Reputation: Excellent</h3>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Gmail & Outlook IP warmup sequence active. (Warmup Level: Pro)</p>
            </div>
            
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h2 style="font-size: 1.1rem; font-weight: 700; color: white;">Outreach Mail SMTP Configuration</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">Autonomously rotate through active sender accounts to distribute SMTP volume and dodge spam filters. Current integration status: Connected.</p>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1rem;">
                    <div style="padding: 0.75rem; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 8px; text-align: center;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Gmail OAuth</span>
                        <strong style="display: block; font-size: 1rem; color: #34d399; margin-top: 0.15rem;">Active</strong>
                    </div>
                    <div style="padding: 0.75rem; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 8px; text-align: center;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Outlook SMTP</span>
                        <strong style="display: block; font-size: 1rem; color: #34d399; margin-top: 0.15rem;">Active</strong>
                    </div>
                    <div style="padding: 0.75rem; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 8px; text-align: center;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Daily Limit</span>
                        <strong style="display: block; font-size: 1rem; color: white; margin-top: 0.15rem;">500 / account</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: white; margin-bottom: 1rem;">Cold Email Transmission Log</h2>
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Recipient</th>
                            <th>Subject</th>
                            <th>Sent Date</th>
                            <th>Status Badge</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emailLogs as $email)
                            <tr>
                                <td><strong style="color: white;">{{ $email->to }}</strong></td>
                                <td>{{ $email->subject }}</td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">{{ $email->sent_at ? $email->sent_at->diffForHumans() : 'Just now' }}</td>
                                <td>
                                    <span class="badge badge-qualified" style="background: rgba(16, 185, 129, 0.1); color: #34d399; border-color: rgba(16, 185, 129, 0.2);">
                                        {{ $email->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No outreach emails logged. Dispatch an EmailAgent task from the Control Grid.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: WHATSAPP OUTREACH -->
    <div id="tab-content-whatsapp" class="tab-content">
        <div class="card" style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: white; margin-bottom: 1rem;">AI WhatsApp Template Composer</h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -0.5rem; margin-bottom: 1.5rem;">WhatsApp remains the highest-conversion channel in South Asia. Build templates and let WhatsAppAgent dynamically tailor and send copy on lead updates.</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <form onsubmit="dispatchWhatsAppMessage(event)" style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="form-group">
                        <label for="wa-phone">Phone Number</label>
                        <input type="text" class="form-control" id="wa-phone" placeholder="e.g. +919876543210" required>
                    </div>
                    <div class="form-group">
                        <label for="wa-message">Outreach Message Body</label>
                        <textarea class="form-control" id="wa-message" style="min-height: 100px;" placeholder="Compose message here. Support variable injection e.g. {lead_name}." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="align-self: flex-start;">Send via WhatsApp API</button>
                </form>
                
                <div style="padding: 1.25rem; background: rgba(0,0,0,0.15); border: 1px solid var(--border-color); border-radius: 12px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <span style="font-size: 0.75rem; color: #34d399; font-weight: 700; text-transform: uppercase;">Meta Business API Integration</span>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: white; margin-top: 0.25rem;">WhatsApp Webhook Status: Active</h4>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem; line-height: 1.4;">Incoming customer replies are instantly pushed to the CRM. The Lead status will automatically shift to "Qualified" or "Contacted" based on sentiment classifications.</p>
                    </div>
                    <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: var(--text-muted);">API Credit Balance:</span>
                        <strong style="color: white;">8,450 message units</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: white; margin-bottom: 1rem;">WhatsApp Outreach Logs</h2>
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Recipient</th>
                            <th>Message Content</th>
                            <th>Status</th>
                            <th>Reply Content</th>
                            <th>Sentiment</th>
                            <th>Dispatched At</th>
                        </tr>
                    </thead>
                    <tbody id="whatsapp-logs-tbody">
                        @forelse($whatsappLogs as $w)
                            <tr>
                                <td>
                                    <strong style="color: white;">{{ $w->lead ? $w->lead->contact_name : 'Manual Outreach' }}</strong>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">{{ $w->phone_number }}</span>
                                </td>
                                <td style="max-width: 250px; font-size: 0.8rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $w->message }}
                                </td>
                                <td>
                                    <span class="badge {{ $w->status == 'Replied' ? 'badge-qualified' : 'badge-discovered' }}">
                                        {{ $w->status }}
                                    </span>
                                </td>
                                <td style="max-width: 200px; font-size: 0.8rem; font-style: italic;">
                                    {{ $w->reply_message ?? '--' }}
                                </td>
                                <td>
                                    @if($w->sentiment == 'Positive')
                                        <span class="badge badge-qualified" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Positive</span>
                                    @elseif($w->sentiment == 'Negative')
                                        <span class="badge badge-closed" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Negative</span>
                                    @elseif($w->sentiment == 'Neutral')
                                        <span class="badge badge-paused" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Neutral</span>
                                    @else
                                        <span>--</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">{{ $w->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No WhatsApp logs recorded. Send a template to test.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 5: LINKEDIN LOGS -->
    <div id="tab-content-linkedin" class="tab-content">
        <div class="card">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: white; margin-bottom: 1rem;">LinkedIn Agent Automation Activity Logs</h2>
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Lead Profile</th>
                            <th>Action Type Executed</th>
                            <th>Message Content Dispatched</th>
                            <th>Transmission Status</th>
                            <th>Recorded At</th>
                        </tr>
                    </thead>
                    <tbody id="linkedin-logs-tbody">
                        @forelse($linkedinLogs as $l)
                            <tr>
                                <td>
                                    <strong style="color: white;">{{ $l->lead ? $l->lead->contact_name : 'LinkedIn Profile' }}</strong>
                                    <a href="{{ $l->profile_url }}" target="_blank" style="font-size: 0.75rem; color: #60a5fa; text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 250px;">{{ $l->profile_url }}</a>
                                </td>
                                <td>
                                    <span class="badge badge-paused" style="border-color: rgba(255,255,255,0.15); color: white;">
                                        {{ $l->action_type }}
                                    </span>
                                </td>
                                <td style="max-width: 300px; font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $l->message ?? '--' }}
                                </td>
                                <td>
                                    <span class="badge {{ $l->status == 'Completed' ? 'badge-qualified' : 'badge-closed' }}">
                                        {{ $l->status }}
                                    </span>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);">{{ $l->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No LinkedIn logs recorded. Enqueue a LinkedInAgent task to begin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 6: QUEUE MONITOR -->
    <div id="tab-content-queue" class="tab-content">
        <div style="display: grid; grid-template-columns: 350px 1fr; gap: 1.5rem; min-height: 400px; height: calc(100vh - 280px);">
            
            <!-- Left Side: Task Run List -->
            <div class="card" style="padding: 1rem; display: flex; flex-direction: column; gap: 1rem; overflow-y: auto;">
                <h3 style="font-size: 1rem; font-weight: 700; color: white; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">Queue Tasks</h3>
                <div id="queue-task-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @forelse($tasks as $index => $t)
                        @php
                            $run = $t->runs->first();
                            $statusClass = $t->status == 'Completed' ? 'badge-qualified' : ($t->status == 'Running' ? 'badge-discovered' : 'badge-paused');
                        @endphp
                        <div class="queue-task-item" onclick="selectQueueTask({{ $t->id }})" id="queue-task-{{ $t->id }}" style="padding: 1rem; background-color: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; cursor: pointer; transition: all 0.2s;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                                <span style="font-size: 0.75rem; color: var(--primary-color); font-weight: 700;">{{ $t->agent_type }}</span>
                                <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $t->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 style="font-size: 0.85rem; font-weight: 700; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.4rem;">{{ $t->task_name }}</h4>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="badge {{ $statusClass }}" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">{{ $t->status }}</span>
                                @if($run)
                                    <span style="font-size: 0.7rem; color: var(--text-muted);">Run Result: {{ $run->status }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-muted); padding: 3rem 0; font-size: 0.85rem;">No background agent tasks enqueued.</div>
                    @endforelse
                </div>
            </div>

            <!-- Right Side: Live Log Terminal Console -->
            <div class="card" style="padding: 0; display: flex; flex-direction: column; overflow: hidden; background: #04070c;">
                <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); background-color: rgba(22, 30, 49, 0.4); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 id="console-title" style="font-size: 1.1rem; font-weight: 700; color: white; margin-bottom: 0.25rem;">Select an agent task to view live execution logs</h2>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">
                            <span id="console-agent-type">Agent: --</span> &nbsp;|&nbsp; <span id="console-task-status">Status: --</span>
                        </div>
                    </div>
                    <button class="btn btn-secondary" id="btn-stream-logs" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;" onclick="toggleLiveStream()">
                        🟢 Stream Live RDP Console
                    </button>
                </div>
                
                <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                    <!-- Terminal Window Header Bar (macOS Style) -->
                    <div style="display: flex; gap: 6px; padding: 0.6rem 1rem; background: #0f172a; border-radius: 12px 12px 0 0; border: 1px solid rgba(255,255,255,0.05); border-bottom: none; align-items: center;">
                        <div style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></div>
                        <div style="width: 10px; height: 10px; border-radius: 50%; background: #eab308;"></div>
                        <div style="width: 10px; height: 10px; border-radius: 50%; background: #22c55e;"></div>
                        <span style="margin-left: 0.75rem; font-size: 0.7rem; font-family: monospace; color: #64748b;">bash - lakshya-rdp-daemon</span>
                    </div>
                    <!-- Console output lines target -->
                    <div id="console-output-target" class="console-panel">
                        <div class="console-line info">[SYSTEM] Ready. Click on a task in the queue list to inspect live execution output...</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Global data structures for JavaScript rendering
    let tasksList = @json($tasks);

    function colorizeLogMessage(message) {
        // Escape HTML to prevent injection
        let escaped = message
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");

        // Color code brackets and labels
        escaped = escaped.replace(/\[SCRAPER\]/g, '<span style="color: #c084fc; font-weight: 700;">[SCRAPER]</span>');
        escaped = escaped.replace(/\[AI-ADVISOR\]/g, '<span style="color: #2dd4bf; font-weight: 700;">[AI-ADVISOR]</span>');
        escaped = escaped.replace(/\[RUNNER\]/g, '<span style="color: #818cf8; font-weight: 700;">[RUNNER]</span>');
        escaped = escaped.replace(/\[WORKER\]/g, '<span style="color: #fb923c; font-weight: 700;">[WORKER]</span>');
        escaped = escaped.replace(/\[SYSTEM\]/g, '<span style="color: #38bdf8; font-weight: 700;">[SYSTEM]</span>');
        escaped = escaped.replace(/\[DB\]/g, '<span style="color: #fb7185; font-weight: 700;">[DB]</span>');
        escaped = escaped.replace(/\[SUCCESS\]/g, '<span style="color: #34d399; font-weight: 700;">[SUCCESS]</span>');
        escaped = escaped.replace(/\[ERROR\]/g, '<span style="color: #f87171; font-weight: 700;">[ERROR]</span>');
        escaped = escaped.replace(/\[WARNING\]/g, '<span style="color: #fbbf24; font-weight: 700;">[WARNING]</span>');
        escaped = escaped.replace(/\[QUEUE\]/g, '<span style="color: #f472b6; font-weight: 700;">[QUEUE]</span>');

        // Style status tags
        escaped = escaped.replace(/(✔|SUCCESS|Lead Registered)/gi, '<span style="color: #34d399; font-weight: 700;">$1</span>');
        escaped = escaped.replace(/(❌|FAILED|ERROR)/gi, '<span style="color: #f87171; font-weight: 700;">$1</span>');
        escaped = escaped.replace(/(⚠|WARNING|skipped)/gi, '<span style="color: #fbbf24; font-weight: 700;">$1</span>');

        // Style qualified intent highlights
        escaped = escaped.replace(/(Lead Qualified!)/g, '<span style="color: #34d399; font-weight: bold; text-shadow: 0 0 4px rgba(52,211,153,0.35);">Lead Qualified!</span>');
        
        return escaped;
    }

    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        document.getElementById('tab-btn-' + tabId).classList.add('active');
        document.getElementById('tab-content-' + tabId).classList.add('active');
    }

    function toggleAgentStatus(agentType) {
        const toggle = document.getElementById('toggle-' + agentType);
        const newStatus = toggle.checked ? 'Active' : 'Idle';
        
        fetch('{{ route("admin.agents.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                agent_type: agentType,
                status: newStatus
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Update local badges
                const card = document.getElementById('agent-card-' + agentType);
                const badge = document.getElementById('badge-' + agentType);
                if(newStatus === 'Active') {
                    card.classList.remove('idle');
                    badge.innerText = 'Active';
                    badge.className = 'badge badge-qualified';
                } else {
                    card.classList.add('idle');
                    badge.innerText = 'Idle';
                    badge.className = 'badge badge-paused';
                }
                showAgentToast('🤖 Configuration Updated', `${agentType} has been set to ${newStatus}.`, 'success');
            }
        });
    }

    function selectQueueTask(id) {
        if (typeof isStreamingLogs !== 'undefined' && isStreamingLogs) {
            toggleLiveStream();
        }
        // Toggle selected styling in list
        document.querySelectorAll('.queue-task-item').forEach(item => {
            item.style.backgroundColor = 'rgba(255,255,255,0.02)';
            item.style.borderColor = 'var(--border-color)';
        });
        const activeItem = document.getElementById('queue-task-' + id);
        if(activeItem) {
            activeItem.style.backgroundColor = 'rgba(99, 102, 241, 0.08)';
            activeItem.style.borderColor = 'var(--primary-color)';
        }

        const task = tasksList.find(t => t.id == id);
        if(!task) return;

        // Populate console header
        document.getElementById('console-title').innerText = task.task_name;
        document.getElementById('console-agent-type').innerText = 'Agent: ' + task.agent_type;
        document.getElementById('console-task-status').innerText = 'Status: ' + task.status;

        // Clear console output and fill with task logs
        const target = document.getElementById('console-output-target');
        target.innerHTML = '';

        // Add header trace line
        const headerDiv = document.createElement('div');
        headerDiv.className = 'console-line success';
        headerDiv.innerText = `>>> Trace Log Initiated for task ID ${task.id} (${task.agent_type})`;
        target.appendChild(headerDiv);

        const run = task.runs && task.runs[0];
        if (run && run.logs && run.logs.length > 0) {
            run.logs.forEach(log => {
                const logDiv = document.createElement('div');
                const lvlClass = log.level.toLowerCase(); // info, warning, error
                logDiv.className = `console-line ${lvlClass}`;
                const timeStr = log.created_at ? new Date(log.created_at).toLocaleTimeString() : 'LOG';
                logDiv.innerHTML = `<span style="color: #64748b; font-size: 0.75rem; margin-right: 6px;">[${timeStr}]</span> ${colorizeLogMessage(log.message)}`;
                target.appendChild(logDiv);
            });
        } else {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'console-line info';
            emptyDiv.innerText = task.status === 'Pending' 
                ? '[QUEUE] Task is currently waiting in database line. Background daemon worker will claim it next.'
                : '[LOG] Executed successfully. No trace logs recorded.';
            target.appendChild(emptyDiv);
        }

        // Scroll to bottom of console
        setTimeout(() => {
            target.scrollTop = target.scrollHeight;
        }, 50);
    }

    function dispatchAgentTask(e) {
        e.preventDefault();
        const agent = document.getElementById('dispatch-agent').value;
        const title = document.getElementById('dispatch-title').value;
        const leadId = document.getElementById('dispatch-lead').value;

        fetch('{{ route("admin.agents.enqueue") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                agent_type: agent,
                task_name: title,
                payload: { lead_id: leadId }
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showAgentToast('🚀 Task Enqueued', data.message, 'success');
                document.getElementById('dispatch-title').value = '';
                refreshQueueData();
            }
        });
    }

    function dispatchWhatsAppMessage(e) {
        e.preventDefault();
        const phone = document.getElementById('wa-phone').value;
        const text = document.getElementById('wa-message').value;

        fetch('{{ route("admin.agents.enqueue") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                agent_type: 'WhatsAppAgent',
                task_name: 'Manual WhatsApp outreach to ' + phone,
                payload: { phone: phone, message: text }
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showAgentToast('💬 Message Sent', 'WhatsApp outbound message task added to API pipeline.', 'success');
                document.getElementById('wa-phone').value = '';
                document.getElementById('wa-message').value = '';
                refreshWhatsAppTab();
            }
        });
    }

    function copyScriptText() {
        const scriptText = document.getElementById('tracker-script-copy').innerText;
        navigator.clipboard.writeText(scriptText);
        showAgentToast('📋 Copied!', 'Script copied to clipboard.', 'success');
    }

    function refreshAllData() {
        refreshQueueData();
        refreshVisitorTab();
        refreshWhatsAppTab();
        refreshLinkedInTab();
        showAgentToast('🔄 Sync Complete', 'Data feeds refreshed successfully.', 'success');
    }

    function refreshQueueData() {
        fetch('{{ url("admin/agents/queue-logs") }}')
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                tasksList = data.tasks;
                renderQueueList();
            }
        });
    }

    function renderQueueList() {
        const listContainer = document.getElementById('queue-task-list');
        listContainer.innerHTML = '';
        
        if (tasksList.length === 0) {
            listContainer.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 3rem 0; font-size: 0.85rem;">No background agent tasks enqueued.</div>';
            return;
        }

        tasksList.forEach(t => {
            const run = t.runs && t.runs[0];
            const statusClass = t.status === 'Completed' ? 'badge-qualified' : (t.status === 'Running' ? 'badge-discovered' : 'badge-paused');
            const runInfo = run ? `<span style="font-size: 0.7rem; color: var(--text-muted);">Run Result: ${run.status}</span>` : '';
            
            const item = document.createElement('div');
            item.className = 'queue-task-item';
            item.id = 'queue-task-' + t.id;
            item.onclick = () => selectQueueTask(t.id);
            item.style = 'padding: 1rem; background-color: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; cursor: pointer; transition: all 0.2s;';
            
            item.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <span style="font-size: 0.75rem; color: var(--primary-color); font-weight: 700;">${t.agent_type}</span>
                    <span style="font-size: 0.7rem; color: var(--text-muted);">Just now</span>
                </div>
                <h4 style="font-size: 0.85rem; font-weight: 700; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.4rem;">${t.task_name}</h4>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="badge ${statusClass}" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">${t.status}</span>
                    ${runInfo}
                </div>
            `;
            listContainer.appendChild(item);
        });
    }

    function refreshVisitorTab() {
        fetch('{{ url("admin/agents/visitor-stream") }}')
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const tbody = document.getElementById('visitor-hits-tbody');
                document.getElementById('stats-visitor-count').innerText = data.visitors.length;
                tbody.innerHTML = '';

                if (data.visitors.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No visitor tracking logs recorded. Copy and install the tracking script.</td></tr>';
                    return;
                }

                data.visitors.forEach(v => {
                    let badgeColor = v.intent_score >= 80 
                        ? 'background: rgba(244, 63, 94, 0.15); color: #fb7185; border: 1px solid rgba(244,63,94,0.3);'
                        : 'background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3);';

                    let pagesHTML = '';
                    v.pages_visited.forEach(p => {
                        pagesHTML += `<span class="badge badge-paused" style="font-size: 0.65rem; border: none; padding: 0.1rem 0.35rem; margin: 0.05rem 0;">${p}</span> `;
                    });

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <strong style="color: white; display: block;">🏢 ${v.company_name || 'Resolving Identity...'}</strong>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">B2B Lead intelligence</span>
                        </td>
                        <td><code>${v.ip_address}</code></td>
                        <td style="max-width: 250px;">${pagesHTML}</td>
                        <td>
                            <span class="badge" style="${badgeColor}">
                                ⚡ ${v.intent_score} / 100
                            </span>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-muted);">Just now</td>
                        <td><span class="badge badge-qualified">Lead Created</span></td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        });
    }

    function refreshWhatsAppTab() {
        fetch('{{ url("admin/agents/whatsapp-logs") }}')
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const tbody = document.getElementById('whatsapp-logs-tbody');
                tbody.innerHTML = '';

                if (data.logs.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No WhatsApp logs recorded. Send a template to test.</td></tr>';
                    return;
                }

                data.logs.forEach(w => {
                    let sentimentHTML = '';
                    if (w.sentiment === 'Positive') {
                        sentimentHTML = '<span class="badge badge-qualified" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Positive</span>';
                    } else if (w.sentiment === 'Negative') {
                        sentimentHTML = '<span class="badge badge-closed" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Negative</span>';
                    } else if (w.sentiment === 'Neutral') {
                        sentimentHTML = '<span class="badge badge-paused" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Neutral</span>';
                    } else {
                        sentimentHTML = '<span>--</span>';
                    }

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <strong style="color: white;">${w.lead ? w.lead.contact_name : 'Manual Outreach'}</strong>
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: block;">${w.phone_number}</span>
                        </td>
                        <td style="max-width: 250px; font-size: 0.8rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            ${w.message}
                        </td>
                        <td>
                            <span class="badge ${w.status === 'Replied' ? 'badge-qualified' : 'badge-discovered'}">
                                ${w.status}
                            </span>
                        </td>
                        <td style="max-width: 200px; font-size: 0.8rem; font-style: italic;">
                            ${w.reply_message || '--'}
                        </td>
                        <td>${sentimentHTML}</td>
                        <td style="font-size: 0.8rem; color: var(--text-muted);">Just now</td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        });
    }

    function refreshLinkedInTab() {
        fetch('{{ url("admin/agents/linkedin-logs") }}')
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const tbody = document.getElementById('linkedin-logs-tbody');
                tbody.innerHTML = '';

                if (data.logs.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem 0;">No LinkedIn logs recorded. Enqueue a LinkedInAgent task to begin.</td></tr>';
                    return;
                }

                data.logs.forEach(l => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>
                            <strong style="color: white;">${l.lead ? l.lead.contact_name : 'LinkedIn Profile'}</strong>
                            <a href="${l.profile_url}" target="_blank" style="font-size: 0.75rem; color: #60a5fa; text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 250px;">${l.profile_url}</a>
                        </td>
                        <td>
                            <span class="badge badge-paused" style="border-color: rgba(255,255,255,0.15); color: white;">
                                ${l.action_type}
                            </span>
                        </td>
                        <td style="max-width: 300px; font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            ${l.message || '--'}
                        </td>
                        <td>
                            <span class="badge ${l.status === 'Completed' ? 'badge-qualified' : 'badge-closed'}">
                                ${l.status}
                            </span>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-muted);">Just now</td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        });
    }

    function showAgentToast(title, message, type = 'info') {
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        
        let borderStyle = 'border-color: var(--border-color);';
        let titleColor = 'color: white;';
        if (type === 'success') {
            borderStyle = 'border-color: rgba(52, 211, 153, 0.4); box-shadow: 0 10px 35px rgba(52, 211, 153, 0.15);';
            titleColor = 'color: #34d399;';
        }

        toast.style = borderStyle;
        toast.innerHTML = `
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.9rem; font-weight: 700; ${titleColor}">${title}</span>
                <span style="font-size: 0.8rem; color: var(--text-muted);">${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 50);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 4000);
    }

    // Live RDP Log Streaming Controller using AJAX JSON polling
    let liveLogInterval = null;
    let isStreamingLogs = false;
    let lastLogId = 0;

    function toggleLiveStream() {
        const btn = document.getElementById('btn-stream-logs');
        const target = document.getElementById('console-output-target');
        
        if (isStreamingLogs) {
            // Disconnect stream / clear polling interval
            if (liveLogInterval) {
                clearInterval(liveLogInterval);
                liveLogInterval = null;
            }
            isStreamingLogs = false;
            btn.innerHTML = '🟢 Stream Live RDP Console';
            btn.className = 'btn btn-secondary';
            document.getElementById('console-title').innerText = 'Select an agent task to view live execution logs';
            document.getElementById('console-agent-type').innerText = 'Agent: --';
            document.getElementById('console-task-status').innerText = 'Status: --';
            target.innerHTML = '<div class="console-line info">[SYSTEM] Live log stream disconnected. Click any task to inspect standard logs.</div>';
        } else {
            // Establish stream connection
            isStreamingLogs = true;
            btn.innerHTML = '🔴 Disconnect Live Feed';
            btn.className = 'btn btn-danger';
            document.getElementById('console-title').innerText = 'Live RDP Daemon Console Feed';
            document.getElementById('console-agent-type').innerText = 'Source: Python daemon outputs';
            document.getElementById('console-task-status').innerText = 'Status: Streaming...';
            
            // Clear console output grid
            target.innerHTML = '<div class="console-line success">>>> Connected to Live RDP Stream! Listening for messages...</div>';
            
            const vmUrl = "{{ config('admin.vm.base_url') }}";
            lastLogId = -1; // -1 instructs the backend to return current ID without historical logs
            let isFirstFetch = true;

            // Poll every 1.2 seconds for new logs
            liveLogInterval = setInterval(() => {
                fetch(`${vmUrl}/get-logs?last_id=${lastLogId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (isFirstFetch) {
                                // Initialize lastLogId to current counter to avoid printing old logs
                                lastLogId = data.last_id;
                                isFirstFetch = false;
                                return;
                            }
                            
                            if (data.logs && data.logs.length > 0) {
                                data.logs.forEach(log => {
                                    lastLogId = Math.max(lastLogId, log.id);
                                    
                                    const logDiv = document.createElement('div');
                                    logDiv.className = 'console-line';
                                    
                                    const logLine = log.message;
                                    // Style lines dynamically
                                    if (logLine.includes('[SUCCESS]') || logLine.includes('✔') || logLine.includes('Qualified')) {
                                        logDiv.className += ' success';
                                    } else if (logLine.includes('[ERROR]') || logLine.includes('❌') || logLine.includes('FAILED')) {
                                        logDiv.className += ' error';
                                    } else if (logLine.includes('[WARNING]') || logLine.includes('⚠') || logLine.includes('skipped')) {
                                        logDiv.className += ' warning';
                                    } else {
                                        logDiv.className += ' info';
                                    }
                                    
                                    logDiv.innerHTML = `<span style="color: #64748b; font-size: 0.75rem; margin-right: 6px;">[${log.timestamp}]</span> ${colorizeLogMessage(logLine)}`;
                                    target.appendChild(logDiv);
                                });
                                
                                // Auto scroll
                                setTimeout(() => {
                                    target.scrollTop = target.scrollHeight;
                                }, 50);
                            } else if (data.last_id) {
                                lastLogId = Math.max(lastLogId, data.last_id);
                            }
                        }
                    })
                    .catch(err => {
                        console.error("Logs fetch error:", err);
                    });
            }, 1200);
        }
    }

    // Auto-select first queue task on load if exists
    if(tasksList.length > 0) {
        selectQueueTask(tasksList[0].id);
    }
</script>
@endsection
