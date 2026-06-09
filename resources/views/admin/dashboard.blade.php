@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-wrapper">
    
    <!-- Welcome section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">Welcome Back, Admin</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Here is the active lead generation state for your campaign.</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <span class="badge badge-new" style="padding: 0.5rem 1rem;">VM Runner Status: Active</span>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Total Leads Found</span>
                <span class="stat-value">{{ $totalLeads }}</span>
                <span class="stat-trend {{ $trendDirection }}">
                    @if($trendDirection === 'up')
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:12px; height:12px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                    </svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:12px; height:12px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                    </svg>
                    @endif
                    {{ abs($trendPercent) }}% {{ $trendDirection === 'up' ? 'increase' : 'decrease' }}
                </span>
            </div>
            <div class="stat-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px; height:24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.978 11.978 0 0 1 12 21.084a11.98 11.98 0 0 1-3-1.847v-.109m0-1.848a9.38 9.38 0 0 0-2.625.372 9.337 9.337 0 0 0-4.121-.952 4.125 4.125 0 0 0 7.533-2.493M9 19.128v-.003c0-1.113.285-2.16.786-3.07M9 19.128c-.499-.91-.786-1.957-.786-3.07H4.5A2.25 2.25 0 0 0 2.25 15v.108m13.5-8.243a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM18.997 13.024a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0ZM4.5 10.75a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z" />
                </svg>
            </div>
        </div>

        <div class="card stat-card" style="justify-content: space-around;">
            <div class="gauge-container">
                <svg class="gauge-svg" viewBox="0 0 100 100" width="100%" height="100%">
                    <defs>
                        <linearGradient id="gaugeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#3b82f6" />
                            <stop offset="100%" stop-color="#34d399" />
                        </linearGradient>
                    </defs>
                    <circle class="gauge-circle-bg" cx="50" cy="50" r="40" />
                    @php
                        $dashoffset = 251.2 - (251.2 * $conversionRate) / 100;
                    @endphp
                    <circle class="gauge-circle-fill" cx="50" cy="50" r="40" 
                            stroke-dasharray="251.2" 
                            stroke-dashoffset="{{ $dashoffset }}" />
                </svg>
                <div class="gauge-center-text">
                    <span class="gauge-value">{{ $conversionRate }}%</span>
                    <span class="gauge-label">Convert</span>
                </div>
            </div>
            <div class="stat-info">
                <span class="stat-label">Qualified Leads</span>
                <span class="stat-value" style="font-size: 1.5rem;">{{ $qualifiedLeads }}</span>
                <p style="font-size: 0.75rem; color: var(--text-muted);">From {{ $totalLeads }} total records</p>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Active Keywords</span>
                <span class="stat-value">{{ $activeKeywords }}</span>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Tracking search queries</p>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(168, 85, 247, 0.1); color: #a855f7;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px; height:24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 21m0 0-.813-5.096L5.504 14.5M9 21h7.5M16.5 13.5v-3a6 6 0 1 0-12 0v3m12 0A9 9 0 1 1 3 13.5h13.5Z" />
                </svg>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Active Bots</span>
                <span class="stat-value">{{ $activeAccounts }}</span>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Scrapers configured</p>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px; height:24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Analytics Charts & Panels Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        
        <!-- Line Chart Card -->
        <div class="card">
            <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem;">Discovery Volume (Last 7 Days)</h2>
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="leadsTrendChart" style="width: 100%; height: 100%;"></canvas>
            </div>
        </div>

        <!-- Donut distribution representation -->
        <div class="card">
            <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem;">Platform Distribution</h2>
            <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-top: 1rem;">
                @foreach($platformDistribution as $platform => $count)
                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                            <span style="text-transform: capitalize; font-weight: 600;">
                                @if($platform == 'reddit') 👽 Reddit
                                @elseif($platform == 'twitter') 🐦 Twitter/X
                                @else 💼 LinkedIn @endif
                            </span>
                            <span style="font-weight: 700; color: var(--text-main);">{{ $count }} leads</span>
                        </div>
                        @php
                            $percentage = $totalLeads > 0 ? round(($count / $totalLeads) * 100) : 0;
                            $barColor = $platform == 'reddit' ? '#ff5722' : ($platform == 'twitter' ? '#1da1f2' : '#0077b5');
                        @endphp
                        <div style="height: 8px; width: 100%; background-color: rgba(255, 255, 255, 0.05); border-radius: 4px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $percentage }}%; background-color: {{ $barColor }}; border-radius: 4px; box-shadow: 0 0 8px {{ $barColor }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Notifications & Logs Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        
        <!-- Notifications Card -->
        <div class="card">
            <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
                <span>System Notifications</span>
                <span class="badge badge-new" style="font-size: 0.7rem;">Recent</span>
            </h2>
            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($recentNotifications as $notif)
                    <div style="padding: 0.85rem; background-color: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: 10px; display: flex; gap: 0.75rem; align-items: flex-start; opacity: {{ $notif->is_read ? '0.7' : '1' }}">
                        <div style="margin-top: 0.2rem;">
                            @if(!$notif->is_read)
                                <span style="display: inline-block; width: 8px; height: 8px; background-color: var(--primary-color); border-radius: 50%; box-shadow: 0 0 8px var(--primary-color);"></span>
                            @else
                                <span style="display: inline-block; width: 8px; height: 8px; background-color: var(--text-dark); border-radius: 50%;"></span>
                            @endif
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.2rem; flex-grow: 1;">
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-main);">{{ $notif->title }}</span>
                            <p style="font-size: 0.8rem; color: var(--text-muted);">{{ $notif->message }}</p>
                        </div>
                        <span style="font-size: 0.7rem; color: var(--text-dark); font-weight: 500;">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Audit Logs Card -->
        <div class="card">
            <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem;">Recent Audit Logs</h2>
            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                @foreach($recentAuditLogs as $log)
                    <div style="padding: 0.85rem; background-color: rgba(255,255,255,0.01); border-left: 3px solid var(--primary-color); border-radius: 0 10px 10px 0; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-main);">{{ $log->action }}</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">IP: {{ $log->ip_address }} | Table: {{ $log->target_table ?? 'N/A' }}</span>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--text-dark); font-weight: 600;">{{ $log->created_at->format('H:i:s') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    // Canvas Line Chart Rendering with clean styling
    const canvas = document.getElementById('leadsTrendChart');
    if(canvas) {
        const ctx = canvas.getContext('2d');
        const days = @json($days);
        const values = @json($leadCounts);
        
        // Handle High DPI displays
        const dpr = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        ctx.scale(dpr, dpr);
        
        const width = rect.width;
        const height = rect.height;
        const padding = { top: 20, right: 20, bottom: 30, left: 35 };
        
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;
        
        const maxValue = Math.max(...values, 5); // default min y axis max as 5
        
        // Draw grid lines
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.04)';
        ctx.lineWidth = 1;
        ctx.font = '500 10px "Plus Jakarta Sans"';
        ctx.fillStyle = '#4b5563';
        
        // Y Axis grid lines (4 divisions)
        for(let i = 0; i <= 4; i++) {
            const y = padding.top + (chartHeight / 4) * i;
            const val = Math.round(maxValue - (maxValue / 4) * i);
            
            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(width - padding.right, y);
            ctx.stroke();
            
            ctx.fillText(val, padding.left - 20, y + 3);
        }
        
        // Calculate points
        const points = [];
        const xStep = chartWidth / (days.length - 1);
        
        for(let i = 0; i < days.length; i++) {
            const x = padding.left + xStep * i;
            const y = padding.top + chartHeight - (values[i] / maxValue) * chartHeight;
            points.push({ x, y });
        }
        
        // Fill area under curve with nice gradient
        const areaGradient = ctx.createLinearGradient(0, padding.top, 0, padding.top + chartHeight);
        areaGradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        areaGradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');
        
        ctx.beginPath();
        ctx.moveTo(points[0].x, padding.top + chartHeight);
        for(let i = 0; i < points.length; i++) {
            ctx.lineTo(points[i].x, points[i].y);
        }
        ctx.lineTo(points[points.length - 1].x, padding.top + chartHeight);
        ctx.closePath();
        ctx.fillStyle = areaGradient;
        ctx.fill();
        
        // Draw path line
        ctx.beginPath();
        ctx.moveTo(points[0].x, points[0].y);
        for(let i = 1; i < points.length; i++) {
            // Cubic bezier smoothing
            const cpX1 = points[i-1].x + xStep / 2;
            const cpY1 = points[i-1].y;
            const cpX2 = points[i].x - xStep / 2;
            const cpY2 = points[i].y;
            ctx.bezierCurveTo(cpX1, cpY1, cpX2, cpY2, points[i].x, points[i].y);
        }
        ctx.strokeStyle = '#6366f1';
        ctx.lineWidth = 3;
        ctx.shadowColor = 'rgba(99, 102, 241, 0.4)';
        ctx.shadowBlur = 10;
        ctx.stroke();
        ctx.shadowBlur = 0; // reset shadow
        
        // Draw dots and labels on X axis
        for(let i = 0; i < points.length; i++) {
            // Circle dot
            ctx.beginPath();
            ctx.arc(points[i].x, points[i].y, 4, 0, 2 * Math.PI);
            ctx.fillStyle = '#a855f7';
            ctx.fill();
            ctx.lineWidth = 2;
            ctx.strokeStyle = '#fff';
            ctx.stroke();
            
            // X label text
            ctx.fillStyle = '#9ca3af';
            ctx.textAlign = 'center';
            ctx.fillText(days[i], points[i].x, padding.top + chartHeight + 18);
        }
    }
</script>
@endsection
