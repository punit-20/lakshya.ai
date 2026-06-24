@extends('layouts.admin')

@section('title', 'Outreach Email Logs')

@section('content')
<div class="email-logs-container" style="display: flex; flex-direction: column; gap: 1.5rem; height: calc(100vh - 120px);">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">Outreach Email Simulator</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Monitor and debug automated email outreach pitches and notification workflows.</p>
        </div>
        <button onclick="sendTestEmail()" class="btn btn-primary">
            <span>Send Test Email</span>
        </button>
    </div>

    <!-- Split Pane Inbox -->
    <div style="display: grid; grid-template-columns: 380px 1fr; gap: 1.5rem; flex-grow: 1; min-height: 0;">
        
        <!-- Left Sidebar: Email List -->
        <div class="card" style="padding: 1rem; display: flex; flex-direction: column; gap: 1rem; overflow-y: auto;">
            <h3 style="font-size: 1rem; font-weight: 700; color: white; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">Inbox</h3>
            
            <div id="email-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($emails as $email)
                    <div class="email-item" onclick="selectEmail({{ $email->id }})" id="email-item-{{ $email->id }}" style="padding: 1rem; background-color: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; cursor: pointer; transition: all 0.2s;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <span style="font-size: 0.75rem; color: var(--primary-color); font-weight: 700;">To: {{ $email->to }}</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $email->sent_at ? $email->sent_at->diffForHumans() : 'Just now' }}</span>
                        </div>
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.25rem;">{{ $email->subject }}</h4>
                        <span class="badge badge-qualified" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">{{ $email->status }}</span>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-muted); padding: 3rem 0; font-size: 0.85rem;">No emails dispatched yet. Click 'Send Test Email' to simulate an outreach.</div>
                @endforelse
            </div>
        </div>

        <!-- Right Side: Email Reader -->
        <div class="card" style="padding: 0; display: flex; flex-direction: column; overflow: hidden; background: #070b13;">
            <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); background-color: rgba(22, 30, 49, 0.4);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h2 id="reader-subject" style="font-size: 1.1rem; font-weight: 700; color: white; margin-bottom: 0.5rem;">Select an email to view</h2>
                        <div style="display: flex; flex-direction: column; gap: 0.2rem; font-size: 0.8rem; color: var(--text-muted);">
                            <span id="reader-to">To: --</span>
                            <span id="reader-date">Sent: --</span>
                        </div>
                    </div>
                    <span id="reader-status-badge" class="badge badge-qualified" style="display: none;">Sent</span>
                </div>
            </div>
            
            <div style="flex-grow: 1; padding: 1.5rem; overflow-y: auto; display: flex; align-items: center; justify-content: center; min-height: 0;" id="reader-body-wrapper">
                <div id="reader-placeholder" style="text-align: center; color: var(--text-muted);">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">✉️</div>
                    <p>Select an outreach or notification email from the list to preview its rendered HTML content.</p>
                </div>
                <iframe id="reader-frame" style="width: 100%; height: 100%; border: none; display: none; background: white; border-radius: 8px;"></iframe>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    // Store emails globally for easy JS selection
    const emailsList = @json($emails);
    
    function selectEmail(id) {
        // Find email object
        const email = emailsList.find(e => e.id == id);
        if(!email) return;

        // Toggle active states in sidebar
        document.querySelectorAll('.email-item').forEach(item => {
            item.style.backgroundColor = 'rgba(255,255,255,0.02)';
            item.style.borderColor = 'var(--border-color)';
        });
        const activeItem = document.getElementById('email-item-' + id);
        if(activeItem) {
            activeItem.style.backgroundColor = 'rgba(99, 102, 241, 0.08)';
            activeItem.style.borderColor = 'var(--primary-color)';
        }

        // Populate reader
        document.getElementById('reader-subject').innerText = email.subject;
        document.getElementById('reader-to').innerText = 'To: ' + email.to;
        document.getElementById('reader-date').innerText = 'Sent: ' + new Date(email.created_at).toLocaleString();
        
        const badge = document.getElementById('reader-status-badge');
        badge.innerText = email.status;
        badge.style.display = 'inline-flex';

        // Load HTML into iframe safely
        document.getElementById('reader-placeholder').style.display = 'none';
        const frame = document.getElementById('reader-frame');
        frame.style.display = 'block';
        
        // Write content to iframe document
        const frameDoc = frame.contentDocument || frame.contentWindow.document;
        frameDoc.open();
        frameDoc.write(email.body_html);
        frameDoc.close();
    }

    function sendTestEmail() {
        fetch('{{ url("admin/emails/test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.reload();
            }
        });
    }

    // Auto-select first email if exists
    if(emailsList.length > 0) {
        selectEmail(emailsList[0].id);
    }
</script>
@endsection
