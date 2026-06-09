@extends('layouts.admin')

@section('title', 'CRM Kanban Board')

@section('styles')
<style>
    /* Kanban Layout */
    .kanban-board {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.25rem;
        align-items: start;
        margin-top: 1.5rem;
        min-height: calc(100vh - 200px);
    }

    .kanban-column {
        background-color: rgba(18, 24, 38, 0.4);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        max-height: 80vh;
        overflow-y: auto;
    }

    .column-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .column-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-main);
    }

    .column-count {
        background-color: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
        padding: 0.15rem 0.5rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    /* Kanban Card */
    .kanban-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .kanban-card:hover {
        transform: translateY(-2px);
        border-color: rgba(99, 102, 241, 0.4);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    }

    .card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-score {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.15rem 0.4rem;
        border-radius: 4px;
    }

    .card-score.high {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
    }

    .card-score.medium {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
    }

    .card-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .card-body {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Dialogue Log Bubble */
    .dialogue-box {
        background-color: rgba(11, 15, 25, 0.4);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem;
        max-height: 200px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .bubble {
        padding: 0.6rem 0.85rem;
        border-radius: 12px;
        font-size: 0.8rem;
        max-width: 85%;
        line-height: 1.4;
    }

    .bubble.agent {
        background-color: var(--primary-color);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }

    .bubble.lead {
        background-color: rgba(255, 255, 255, 0.05);
        color: var(--text-main);
        align-self: flex-start;
        border-bottom-left-radius: 2px;
        border: 1px solid var(--border-color);
    }

    .bubble.system {
        background-color: rgba(245, 158, 11, 0.1);
        color: #fbbf24;
        align-self: center;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(245, 158, 11, 0.2);
        max-width: 100%;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="crm-wrapper">
    
    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">Leads Pipeline</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Move leads through outreach stages, draft replies with AI, and track status pipelines.</p>
        </div>
    </div>

    <!-- Kanban Board Grid -->
    <div class="kanban-board">
        @foreach($statuses as $status)
            <div class="kanban-column" id="column-{{ strtolower($status) }}">
                <div class="column-header">
                    <span class="column-title">{{ $status }}</span>
                    <span class="column-count">{{ count($leadsByStatus[$status]) }}</span>
                </div>
                
                @foreach($leadsByStatus[$status] as $lead)
                    <div class="kanban-card" onclick="openLeadDetails({{ $lead->id }})">
                        <div class="card-meta">
                            <span class="platform-tag {{ $lead->post->platform ?? 'reddit' }}">{{ $lead->post->platform ?? 'outreach' }}</span>
                            <span class="card-score {{ $lead->score >= 90 ? 'high' : 'medium' }}">⭐ {{ $lead->score }}</span>
                        </div>
                        <span class="card-name">{{ $lead->contact_name }}</span>
                        <p class="card-body">{{ $lead->post->content ?? 'No content matches' }}</p>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.25rem;">
                            <span style="font-size: 0.7rem; color: var(--text-dark); font-weight: 700;">{{ $lead->created_at->format('M d') }}</span>
                            <span style="font-size: 0.7rem; color: var(--primary-color); font-weight: 600;">{{ $lead->intent_category }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

</div>

<!-- Lead Detail Modal Overlay -->
<div class="modal-overlay" id="leadModal" onclick="closeLeadModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        
        <div class="modal-header">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span class="platform-tag" id="modalPlatform">Reddit</span>
                <h3 class="brand-name" id="modalLeadName" style="-webkit-text-fill-color: initial; color: white;">Lead Details</h3>
            </div>
            <button class="btn btn-secondary" onclick="closeLeadModal()" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">✕</button>
        </div>
        
        <div class="modal-body">
            
            <!-- Tab headers or collapsible panels -->
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                
                <!-- Post Contents Panel -->
                <div style="padding: 1rem; background-color: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px;">
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Matched Post Content</span>
                    <h4 id="modalPostTitle" style="font-size: 0.95rem; font-weight: 700; margin-top: 0.25rem; color: white;">Post Title</h4>
                    <p id="modalPostContent" style="font-size: 0.85rem; color: var(--text-main); margin-top: 0.5rem; line-height: 1.5;">Post content body...</p>
                    <div style="margin-top: 0.75rem; display: flex; gap: 1rem; font-size: 0.75rem; color: var(--text-muted);">
                        <span>Author: <strong id="modalPostAuthor" style="color: var(--text-main);">u/author</strong></span>
                        <a id="modalPostUrl" href="#" target="_blank" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">View Original Post ↗</a>
                    </div>
                </div>

                <!-- Pipeline Status dropdown & Details -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Pipeline Stage</label>
                        <select id="modalStatusSelect" class="project-selector" onchange="updateLeadStatus()">
                            <option value="New">New</option>
                            <option value="Discovered">Discovered</option>
                            <option value="Contacted">Contacted</option>
                            <option value="Qualified">Qualified</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Relevance Score</label>
                        <span id="modalLeadScore" style="font-size: 1.2rem; font-weight: 800; color: #34d399; margin-top: 0.2rem;">95/100</span>
                    </div>
                </div>

                <!-- Chat Dialogue Log (For Contacted / Qualified Leads) -->
                <div id="dialogueSection" style="display: none;">
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.5rem;">Conversation Dialogue</span>
                    <div class="dialogue-box" id="dialogueList">
                        <!-- Message bubble list -->
                    </div>
                </div>

                <!-- AI Generated Reply Drafting -->
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">AI Generated Pitch Reply</span>
                        <button class="btn btn-secondary" onclick="triggerAiGeneration()" id="generateBtn" style="padding: 0.25rem 0.5rem; font-size: 0.7rem;">⚡ Generate Pitch</button>
                    </div>
                    <div class="form-group">
                        <textarea id="aiReplyEditor" class="form-control" style="font-size: 0.85rem; line-height: 1.4; height: 120px;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: -0.5rem;">
                        <button class="btn btn-secondary" onclick="saveReplyDraft()" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Save Draft</button>
                        <button class="btn btn-primary" onclick="sendReplyOutreach()" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Send Pitch Outreach</button>
                    </div>
                </div>

                <!-- Meeting Scheduler (For Qualified / Closed Leads) -->
                <div style="padding-top: 0.5rem; border-top: 1px solid var(--border-color);">
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.5rem;">Schedule Meeting Booking</span>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: flex-end;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Date & Time</label>
                            <input type="datetime-local" id="meetingTime" class="form-control">
                        </div>
                        <button class="btn btn-primary" onclick="scheduleMeeting()" style="height: 38px;">Book Calendar Slot</button>
                    </div>
                </div>

            </div>

        </div>
        
    </div>
</div>
@endsection

@section('scripts')
<script>
    let activeLeadId = null;

    function openLeadDetails(leadId) {
        activeLeadId = leadId;
        
        fetch(`{{ url('admin/crm/details') }}/${leadId}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const lead = data.lead;
                
                // Load platform badges
                const plat = document.getElementById('modalPlatform');
                plat.innerText = lead.post ? lead.post.platform : 'Outreach';
                plat.className = `platform-tag ${lead.post ? lead.post.platform : 'reddit'}`;
                
                document.getElementById('modalLeadName').innerText = lead.contact_name;
                document.getElementById('modalPostTitle').innerText = lead.post ? (lead.post.title || 'No Title') : 'No Title';
                document.getElementById('modalPostContent').innerText = lead.post ? lead.post.content : 'No post body';
                document.getElementById('modalPostAuthor').innerText = lead.post ? lead.post.author : 'N/A';
                
                const urlLink = document.getElementById('modalPostUrl');
                if (lead.post && lead.post.url) {
                    urlLink.href = lead.post.url;
                    urlLink.style.display = 'inline-block';
                } else {
                    urlLink.style.display = 'none';
                }
                
                document.getElementById('modalStatusSelect').value = lead.status;
                document.getElementById('modalLeadScore').innerText = `${lead.score}/100`;
                document.getElementById('aiReplyEditor').value = lead.generated_reply || '';

                // Handle dialogue box rendering
                const dialogueSection = document.getElementById('dialogueSection');
                const dialogueList = document.getElementById('dialogueList');
                dialogueList.innerHTML = '';
                
                if (lead.conversation && lead.conversation.messages && lead.conversation.messages.length > 0) {
                    dialogueSection.style.display = 'block';
                    lead.conversation.messages.forEach(m => {
                        const b = document.createElement('div');
                        b.className = `bubble ${m.sender}`;
                        b.innerText = m.message;
                        dialogueList.appendChild(b);
                    });
                    // Scroll to bottom
                    dialogueList.scrollTop = dialogueList.scrollHeight;
                } else {
                    dialogueSection.style.display = 'none';
                }

                // Show modal overlay
                document.getElementById('leadModal').classList.add('active');
            }
        })
        .catch(err => {
            console.error('Failed to load lead details:', err);
            alert('Failed to load lead details. Please try again.');
        });
    }

    function closeLeadModal() {
        document.getElementById('leadModal').classList.remove('active');
        activeLeadId = null;
    }

    function updateLeadStatus() {
        if(!activeLeadId) return;
        const status = document.getElementById('modalStatusSelect').value;
        
        fetch('{{ route("admin.crm.status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ lead_id: activeLeadId, status: status })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Instantly reload to update columns
                window.location.reload();
            }
        })
        .catch(err => {
            console.error('Failed to update lead status:', err);
            alert('Failed to update status. Please try again.');
        });
    }

    function triggerAiGeneration() {
        if(!activeLeadId) return;
        const btn = document.getElementById('generateBtn');
        btn.innerText = '⚡ Thinking...';
        btn.disabled = true;

        fetch('{{ route("admin.crm.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ lead_id: activeLeadId })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerText = '⚡ Generate Pitch';
            btn.disabled = false;
            if(data.success) {
                document.getElementById('aiReplyEditor').value = data.reply;
            }
        })
        .catch(err => {
            btn.innerText = '⚡ Generate Pitch';
            btn.disabled = false;
            console.error('AI generation failed:', err);
            alert('AI generation failed. Please try again.');
        });
    }

    function saveReplyDraft() {
        if(!activeLeadId) return;
        const text = document.getElementById('aiReplyEditor').value;
        
        fetch('{{ route("admin.crm.save-reply") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ lead_id: activeLeadId, reply: text })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('Draft saved successfully!');
            }
        })
        .catch(err => {
            console.error('Save draft failed:', err);
            alert('Failed to save draft. Please try again.');
        });
    }

    function sendReplyOutreach() {
        if(!activeLeadId) return;
        const text = document.getElementById('aiReplyEditor').value;
        if(!text.trim()) {
            alert('Please generate or write a pitch first!');
            return;
        }

        fetch('{{ route("admin.crm.send-message") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ lead_id: activeLeadId, message: text })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('Outreach pitch sent successfully!');
                window.location.reload();
            }
        })
        .catch(err => {
            console.error('Send outreach failed:', err);
            alert('Failed to send outreach. Please try again.');
        });
    }

    function scheduleMeeting() {
        if(!activeLeadId) return;
        const time = document.getElementById('meetingTime').value;
        if(!time) {
            alert('Please select a meeting date and time!');
            return;
        }

        fetch('{{ route("admin.crm.meeting") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ lead_id: activeLeadId, scheduled_at: time })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('Meeting booked and registered in system calendar!');
                window.location.reload();
            }
        })
        .catch(err => {
            console.error('Meeting scheduling failed:', err);
            alert('Failed to schedule meeting. Please try again.');
        });
    }
</script>
@endsection
