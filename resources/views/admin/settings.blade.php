@extends('layouts.admin')

@section('title', 'Platform Settings')

@section('content')
<div class="settings-container">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">Crawler & Settings</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Configure platform credentials, HTTP proxies, and view background agent logs.</p>
        </div>
    </div>

    <!-- Scraper Profiles Configuration -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        
        <!-- Platform Profiles Card -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; color: white;">Configure Crawl Accounts</h3>
            <form action="{{ route('admin.settings.account') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="platformSelect">Target Platform</label>
                    <select id="platformSelect" name="platform" class="project-selector" style="width: 100%;">
                        <option value="reddit">Reddit</option>
                        <option value="twitter">Twitter/X</option>
                        <option value="linkedin">LinkedIn</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="usernameInput">Username / ID</label>
                    <input type="text" id="usernameInput" name="username" class="form-control" placeholder="e.g. crawler_bot" required>
                </div>
                <div class="form-group">
                    <label for="cookiesInput">Session Cookies / Access Token (JSON)</label>
                    <textarea id="cookiesInput" name="session_cookies" class="form-control" placeholder='{"session_id": "value"}'></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">Save Configuration</button>
            </form>
        </div>

        <!-- Active Accounts List Card -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: white;">Active Crawler Accounts</h3>
            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @forelse($accounts as $acc)
                    <div style="padding: 1rem; background-color: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="platform-tag {{ $acc->platform }}">{{ $acc->platform }}</span>
                                <strong style="color: white; font-size: 0.9rem;">{{ $acc->username }}</strong>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Last used: {{ $acc->last_used_at ? $acc->last_used_at->diffForHumans() : 'Never' }}</span>
                        </div>
                        <span class="badge {{ $acc->status == 'Active' ? 'badge-qualified' : 'badge-closed' }}">{{ $acc->status }}</span>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-muted); padding: 2rem 0;">No crawlers configured. The background agent will run in simulation mode.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Proxies & Logs Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        
        <!-- Proxies Card -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: white;">Outreach HTTP Proxies</h3>
            <div class="table-container">
                <table class="custom-table" style="font-size: 0.8rem;">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Port</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proxies as $proxy)
                            <tr>
                                <td>{{ $proxy['ip'] }}</td>
                                <td>{{ $proxy['port'] }}</td>
                                <td>{{ $proxy['location'] }}</td>
                                <td>
                                    <span class="badge {{ $proxy['status'] == 'Active' ? 'badge-qualified' : 'badge-paused' }}" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                        {{ $proxy['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- VM Runner Log Card -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: white;">Virtual Machine Agent Logs</h3>
            <div style="background-color: #030712; border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; font-family: monospace; font-size: 0.75rem; color: #34d399; height: 180px; overflow-y: auto;">
                @foreach($vmLogs as $log)
                    <div style="margin-bottom: 0.5rem; line-height: 1.4;">
                        <span style="color: var(--text-muted);">[{{ $log['timestamp'] }}]</span>
                        <span style="color: {{ $log['level'] == 'SUCCESS' ? '#34d399' : ($log['level'] == 'WARNING' ? '#fbbf24' : '#60a5fa') }}; font-weight: bold;">[{{ $log['level'] }}]</span>
                        <span style="color: #f3f4f6;">{{ $log['message'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection
