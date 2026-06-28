<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lakshya Dashboard') - AI Lead Generation</title>
    
    <!-- Core Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    
    <!-- 3D Holographic Preloader Styles -->
    <style>
        .preloader-overlay {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: radial-gradient(circle at center, #0b0f1d 0%, #030712 100%);
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s ease, visibility 0.4s ease;
            backdrop-filter: blur(25px);
        }
        .preloader-overlay.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        .preloader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            perspective: 800px;
        }
        
        /* 3D Rotating Cube */
        .cube-3d {
            width: 70px;
            height: 70px;
            position: relative;
            transform-style: preserve-3d;
            animation: rotate3dCube 3s infinite linear;
            margin-bottom: 2.25rem;
        }
        .cube-face {
            position: absolute;
            width: 70px;
            height: 70px;
            background: rgba(99, 102, 241, 0.05);
            border: 2px solid #6366f1;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.5), inset 0 0 10px rgba(99, 102, 241, 0.3);
            border-radius: 8px;
        }
        .cube-front  { transform: rotateY(  0deg) translateZ(35px); }
        .cube-back   { transform: rotateY(180deg) translateZ(35px); }
        .cube-left   { transform: rotateY(-90deg) translateZ(35px); }
        .cube-right  { transform: rotateY( 90deg) translateZ(35px); }
        .cube-top    { transform: rotateX( 90deg) translateZ(35px); }
        .cube-bottom { transform: rotateX(-90deg) translateZ(35px); }
        
        @keyframes rotate3dCube {
            0% { transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
            100% { transform: rotateX(360deg) rotateY(360deg) rotateZ(360deg); }
        }
        
        .preloader-title {
            font-family: 'Outfit', 'Inter', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: 7px;
            background: linear-gradient(135deg, #a855f7 0%, #6366f1 50%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            animation: pulseText 2s infinite ease-in-out;
            text-shadow: 0 0 30px rgba(99,102,241,0.25);
        }
        .preloader-subtitle {
            font-size: 0.75rem;
            color: #64748b;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .preloader-progress-bar {
            width: 180px;
            height: 3px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        .preloader-progress-fill {
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, #6366f1, #a855f7);
            border-radius: 10px;
            position: absolute;
            transform: translateX(-100%);
            animation: progressLoad 2s infinite ease-in-out;
        }
        
        @keyframes progressLoad {
            0% { transform: translateX(-100%); }
            50% { transform: translateX(0%); }
            100% { transform: translateX(100%); }
        }
        @keyframes pulseText {
            0%, 100% { opacity: 0.75; transform: scale(0.97); }
            50% { opacity: 1; transform: scale(1.03); }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- 3D Holographic Preloader Cover -->
    <div id="hologram-preloader" class="preloader-overlay">
        <div class="preloader-container">
            <!-- 3D Rotating Cube -->
            <div class="cube-3d">
                <div class="cube-face cube-front"></div>
                <div class="cube-face cube-back"></div>
                <div class="cube-face cube-left"></div>
                <div class="cube-face cube-right"></div>
                <div class="cube-face cube-top"></div>
                <div class="cube-face cube-bottom"></div>
            </div>
            <!-- Pulsing Title -->
            <h2 class="preloader-title">LAKSHYA.AI</h2>
            <p class="preloader-subtitle">Loading lead intelligence...</p>
            <div class="preloader-progress-bar">
                <div class="preloader-progress-fill"></div>
            </div>
        </div>
    </div>

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
                        <li class="menu-item {{ Request::is('admin/agents*') ? 'active' : '' }}">
                            <a href="{{ route('admin.agents') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                                </svg>
                                <span>AI Agents</span>
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
        
        // Global showToast utility
        window.showToast = function(title, message, type = 'info') {
            // Handle cases where only two arguments are passed: showToast(title, type)
            if (arguments.length === 2 && ['info', 'success', 'error', 'warning'].includes(message)) {
                type = message;
                message = '';
            }

            const existingToast = document.querySelector('.toast-notification');
            if (existingToast) {
                existingToast.remove();
            }

            // Ensure toast animation styles are injected
            if (!document.getElementById('toast-notification-styles')) {
                const style = document.createElement('style');
                style.id = 'toast-notification-styles';
                style.innerHTML = `
                    .toast-notification {
                        position: fixed;
                        bottom: 2rem;
                        right: 2rem;
                        background: rgba(18, 24, 38, 0.95);
                        border: 1px solid var(--border-color);
                        border-radius: 12px;
                        padding: 1rem 1.5rem;
                        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.5);
                        backdrop-filter: blur(12px);
                        z-index: 9999;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        transform: translateY(100px);
                        opacity: 0;
                        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s;
                    }
                    .toast-notification.show {
                        transform: translateY(0);
                        opacity: 1;
                    }
                `;
                document.head.appendChild(style);
            }

            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            
            let borderStyle = 'border-color: var(--border-color);';
            let titleColor = 'color: white;';
            if (type === 'success') {
                borderStyle = 'border-color: rgba(52, 211, 153, 0.4); box-shadow: 0 10px 35px rgba(52, 211, 153, 0.15);';
                titleColor = 'color: #34d399;';
            } else if (type === 'error') {
                borderStyle = 'border-color: rgba(239, 68, 68, 0.4); box-shadow: 0 10px 35px rgba(239, 68, 68, 0.15);';
                titleColor = 'color: #f87171;';
            } else if (type === 'warning') {
                borderStyle = 'border-color: rgba(245, 158, 11, 0.4); box-shadow: 0 10px 35px rgba(245, 158, 11, 0.15);';
                titleColor = 'color: #fbbf24;';
            }

            toast.style = borderStyle;
            toast.innerHTML = `
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <span style="font-size: 0.9rem; font-weight: 700; ${titleColor}">${title}</span>
                    ${message ? `<span style="font-size: 0.8rem; color: var(--text-muted);">${message}</span>` : ''}
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
            }, 4500);
        };

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

        // Manage preloader hide on load
        window.addEventListener('load', () => {
            const preloader = document.getElementById('hologram-preloader');
            if (preloader) {
                preloader.classList.add('fade-out');
            }
        });

        // Display preloader on navigate/link clicks to mask RDP network latency
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('a').forEach(link => {
                if (link.href && 
                    !link.href.includes('#') && 
                    !link.href.startsWith('javascript:') && 
                    link.target !== '_blank' &&
                    link.hostname === window.location.hostname) {
                    link.addEventListener('click', () => {
                        const preloader = document.getElementById('hologram-preloader');
                        if (preloader) {
                            preloader.classList.remove('fade-out');
                        }
                    });
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
