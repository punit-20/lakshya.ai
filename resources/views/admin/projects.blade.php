@extends('layouts.admin')

@section('title', 'Projects Manager')

@section('content')
<div class="projects-container">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">Campaign Projects</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Manage and isolate different lead discovery campaigns (multi-tenant scope).</p>
        </div>
        <button class="btn btn-primary" onclick="toggleProjectModal(true)">＋ New Project</button>
    </div>

    <!-- Grid of Projects -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
        @foreach($projects as $p)
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; border-color: {{ session('active_project_id') == $p->id ? 'rgba(99, 102, 241, 0.4)' : 'var(--border-color)' }};">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <span class="badge {{ $p->status == 'Active' ? 'badge-qualified' : 'badge-paused' }}">{{ $p->status }}</span>
                        @if(session('active_project_id') == $p->id)
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary-color);">👉 Active Context</span>
                        @endif
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; margin-top: 0.75rem; color: white;">{{ $p->name }}</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin-top: 0.5rem; min-height: 45px;">{{ $p->description ?? 'No description provided.' }}</p>
                </div>
                
                <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; gap: 1rem; font-size: 0.8rem; color: var(--text-muted);">
                        <span>🔑 <strong>{{ $p->keywords_count }}</strong> Keywords</span>
                        <span>👥 <strong>{{ $p->leads_count }}</strong> Leads</span>
                    </div>
                    
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.projects.toggle', $p->id) }}" class="btn btn-secondary" style="padding: 0.4rem 0.75rem; font-size: 0.75rem;">
                            {{ $p->status == 'Active' ? 'Pause' : 'Activate' }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Project Create Modal Overlay -->
<div class="modal-overlay" id="projectModal" onclick="toggleProjectModal(false)">
    <div class="modal-content" onclick="event.stopPropagation()">
        
        <form action="{{ route('admin.projects.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h3 class="brand-name" style="-webkit-text-fill-color: initial; color: white;">Create Campaign Project</h3>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="projectName">Project Name</label>
                    <input type="text" id="projectName" name="name" class="form-control" placeholder="e.g. WebSizzle SaaS" required>
                </div>
                <div class="form-group">
                    <label for="projectDesc">Description</label>
                    <textarea id="projectDesc" name="description" class="form-control" placeholder="Outreach campaign description..."></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleProjectModal(false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Project</button>
            </div>
        </form>
        
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleProjectModal(show) {
        const modal = document.getElementById('projectModal');
        if(show) {
            modal.classList.add('active');
        } else {
            modal.classList.remove('active');
        }
    }
</script>
@endsection
