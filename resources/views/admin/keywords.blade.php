@extends('layouts.admin')

@section('title', 'Keywords Manager')

@section('content')
<div class="keywords-container">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">Search Keywords</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Active project context: <strong style="color: var(--primary-color);">{{ $project->name }}</strong></p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
        
        <!-- Add Keyword Card -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; color: white;">Add Search Query</h3>
            <form action="{{ route('admin.keywords.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="keywordInput">Keyword Phrase</label>
                    <input type="text" id="keywordInput" name="keyword" class="form-control" placeholder="e.g. landing page feedback" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">＋ Add Keyword</button>
            </form>
        </div>

        <!-- Keyword List Card -->
        <div class="card" style="padding: 1rem;">
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Search Phrase</th>
                            <th>Status</th>
                            <th>Last Scraped</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keywords as $k)
                            <tr>
                                <td style="font-weight: 600;">{{ $k->keyword }}</td>
                                <td>
                                    <!-- Toggle status checkbox slider -->
                                    <label class="badge {{ $k->status == 'Active' ? 'badge-qualified' : 'badge-paused' }}" 
                                           style="cursor: pointer;" onclick="toggleKeywordStatus({{ $k->id }}, this)">
                                        {{ $k->status }}
                                    </label>
                                </td>
                                <td style="color: var(--text-muted); font-size: 0.85rem;">
                                    {{ $k->last_scraped_at ? $k->last_scraped_at->diffForHumans() : 'Never' }}
                                </td>
                                <td style="text-align: right;">
                                    <form action="{{ route('admin.keywords.delete', $k->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; color: #fb7185; border-color: rgba(244, 63, 94, 0.1);" onclick="return confirm('Delete this keyword?')">
                                            ✕ Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem 0;">No keywords configured for this project.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    function toggleKeywordStatus(id, element) {
        fetch(`{{ url('admin/keywords/toggle') }}/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                if(element.classList.contains('badge-qualified')) {
                    element.className = 'badge badge-paused';
                    element.innerText = 'Paused';
                } else {
                    element.className = 'badge badge-qualified';
                    element.innerText = 'Active';
                }
            }
        });
    }
</script>
@endsection
