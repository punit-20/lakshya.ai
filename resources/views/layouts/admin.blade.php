<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lakshya Dashboard') - AI Lead Generation</title>
    
    <!-- Core Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('styles')
</head>
<body>
    <div class="app-container">
        
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand-section">
                <div class="brand-logo">L</div>
                <span class="brand-name">Lakshya.ai</span>
            </div>
            
            <nav class="sidebar-menu-wrapper">
                <ul class="sidebar-menu">
                    @if(session()->has('impersonating_client_id'))
                        <li class="menu-item {{ Request::is('client/dashboard*') ? 'active' : '' }}">
                            <a href="{{ route('client.dashboard') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V18ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25A2.25 2.25 0 0 1 13.5 8.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                                </svg>
                                <span>Client Dashboard</span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('client/marketing*') ? 'active' : '' }}">
                            <a href="{{ route('client.marketing') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                                </svg>
                                <span>AI Creative Builder</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('admin.clients.exit') }}" style="background: rgba(244, 63, 94, 0.1); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.2);">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                </svg>
                                <span>Exit Client Mode</span>
                            </a>
                        </li>
                    @else
                        <li class="menu-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V18ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25A2.25 2.25 0 0 1 13.5 8.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('admin/projects*') ? 'active' : '' }}">
                            <a href="{{ route('admin.projects') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.625-5.495L12 3m0 0L10.375 7.255M12 3v13.5M2.25 12.75a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25m-18 0V17.25a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25V12.75" />
                                </svg>
                                <span>Projects</span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('admin/keywords*') ? 'active' : '' }}">
                            <a href="{{ route('admin.keywords') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75a8.25 8.25 0 0 1-11.65 0M21.75 18a2.25 2.25 0 0 1-2.25 2.25H4.5A2.25 2.25 0 0 1 2.25 18v-2.25A2.25 2.25 0 0 1 4.5 13.5h15a2.25 2.25 0 0 1 2.25 2.25V18ZM12 3v9m0 0L8.25 8.25M12 12l3.75-3.75" />
                                </svg>
                                <span>Keywords</span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('admin/crm*') ? 'active' : '' }}">
                            <a href="{{ route('admin.crm') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                </svg>
                                <span>Lakshya Target Board</span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('admin/marketing*') ? 'active' : '' }}">
                            <a href="{{ route('admin.marketing') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                                </svg>
                                <span>AI Marketer</span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('admin/clients*') ? 'active' : '' }}">
                            <a href="{{ route('admin.clients') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A2.25 2.25 0 0 1 12.75 21.5h-1.5a2.25 2.25 0 0 1-2.25-2.263v-.109m0 0A8.967 8.967 0 0 1 3 18.062a4.125 4.125 0 0 1 7.533-2.493M21 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.5 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                                </svg>
                                <span>Client Manager</span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('admin/statistics*') ? 'active' : '' }}">
                            <a href="{{ route('admin.statistics') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                                </svg>
                                <span>Economics & Stats</span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('admin/settings*') ? 'active' : '' }}">
                            <a href="{{ route('admin.settings') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.43l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>Agent Settings</span>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('admin/billing*') ? 'active' : '' }}">
                            <a href="{{ route('admin.billing') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                </svg>
                                <span>Billing & SaaS</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-profile">
                    @if(session()->has('impersonating_client_id'))
                        @php
                            $impersonatedClient = \App\Models\User::with('subscription')->find(session('impersonating_client_id'));
                            $initials = $impersonatedClient ? strtoupper(substr($impersonatedClient->name, 0, 2)) : 'CL';
                        @endphp
                        <div class="user-avatar" style="background: var(--primary-color);">{{ $initials }}</div>
                        <div class="user-info">
                            <span class="user-name">{{ $impersonatedClient->name ?? 'Client User' }}</span>
                            <span class="user-role">{{ $impersonatedClient->subscription->tier ?? 'Client' }} Tier</span>
                        </div>
                    @else
                        <div class="user-avatar">LA</div>
                        <div class="user-info">
                            <span class="user-name">Lakshya Admin</span>
                            <span class="user-role">Super Admin</span>
                        </div>
                    @endif
                </div>
            </div>
        </aside>
        
        <!-- Main Content Area Wrapper -->
        <div class="main-wrapper">
            
            <!-- Top Header -->
            <header class="main-header">
                <div class="header-title-section">
                    @if(session()->has('impersonating_client_id'))
                        @php
                            $clientId = session('impersonating_client_id');
                            $clientProject = \App\Models\Project::where('user_id', $clientId)->first();
                        @endphp
                        <span class="client-campaign-title" style="font-size: 0.95rem; font-weight: 700; color: white; display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.03); padding: 0.45rem 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                            <span style="color: #60a5fa;">📁 Campaign:</span> {{ $clientProject->name ?? 'Active Campaign' }}
                        </span>
                    @else
                        <select id="headerProjectSelector" class="project-selector" onchange="switchProject(this.value)">
                            @foreach($layoutProjects as $p)
                                <option value="{{ $p->id }}" {{ (session('active_project_id', 1) == $p->id) ? 'selected' : '' }}>
                                    📁 {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
                
                <div class="header-actions">
                    <!-- Notification Dropdown -->
                    <div class="bell-container" onclick="toggleNotifications()">
                        <div class="bell-icon">
                            <!-- Bell Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:20px; height:20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                        </div>
                        @if($layoutUnreadNotificationsCount > 0)
                            <span class="bell-badge"></span>
                        @endif
                    </div>
                </div>
            </header>
            
            <!-- Main Content Blade Target -->
            <main class="content-area">
                @if(session()->has('impersonating_client_id'))
                    <div style="background: linear-gradient(135deg, #fb7185 0%, #f43f5e 100%); color: white; padding: 0.85rem 1.5rem; border-radius: 12px; font-size: 0.9rem; font-weight: 700; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border: 1px solid rgba(255,255,255,0.15); box-shadow: 0 4px 15px rgba(244, 63, 94, 0.2);">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>⚠️</span>
                            <span>SIMULATING CLIENT ACCESS: Testing interface on behalf of client. (Data shown is mock client metrics)</span>
                        </div>
                        <a href="{{ route('admin.clients.exit') }}" class="btn btn-secondary" style="padding: 0.35rem 0.85rem; font-size: 0.75rem; border-color: rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); color: white;">
                            Exit Simulation Mode
                        </a>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info">
                        <strong>Info:</strong> {{ session('info') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
            
        </div>
    </div>

    <!-- CSRF Helper & Project Switcher Script -->
    <script>
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        function switchProject(projectId) {
            fetch('{{ url("admin/switch-project") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({ project_id: projectId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                }
            });
        }

        function toggleNotifications() {
            // Mark all read or show dropdown
            fetch('{{ url("admin/notifications/read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const badge = document.querySelector('.bell-badge');
                    if(badge) badge.remove();
                }
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
