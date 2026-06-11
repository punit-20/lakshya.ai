@extends('layouts.admin')

@section('title', 'Client Dashboard')

@section('styles')
<style>
    .analytics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
    }

    .project-details {
        margin-top: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="client-dashboard-wrapper">
    <!-- Welcome section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">Welcome Back, {{ $client->name }}</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
                Here is the real-time performance of your affiliate marketing campaign for <strong>{{ $project->name ?? 'Active Campaign' }}</strong>.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <span class="badge badge-qualified" style="padding: 0.5rem 1rem;">Simulation Account: {{ $stats['plan'] }}</span>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <!-- Reach -->
        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Guaranteed Campaign Reach</span>
                <span class="stat-value">{{ number_format($stats['reach']) }}</span>
                <span style="font-size: 0.75rem; color: #34d399;">views / impressions</span>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
                🎯
            </div>
        </div>

        <!-- Clicks -->
        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Affiliate Link Clicks</span>
                <span class="stat-value">{{ number_format($stats['clicks']) }}</span>
                <span style="font-size: 0.75rem; color: #a855f7;">CTR: {{ round(($stats['clicks'] / max($stats['reach'], 1)) * 100, 2) }}%</span>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(168, 85, 247, 0.1); color: #c084fc;">
                🖱️
            </div>
        </div>

        <!-- Commissions -->
        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Est. Commissions Earned</span>
                <span class="stat-value" style="color: #34d399;">INR {{ number_format($stats['commission']) }}</span>
                <span style="font-size: 0.75rem; color: var(--text-muted);">Payout: Net-30</span>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                💰
            </div>
        </div>

        <!-- Spend -->
        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Ad Budget Consumed</span>
                <span class="stat-value">INR {{ number_format($stats['spend']) }}</span>
                <span style="font-size: 0.75rem; color: #fb7185;">Max Allocation: INR 5,000</span>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(244, 63, 94, 0.1); color: #fb7185;">
                💵
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="analytics-grid">
        <!-- Reach Chart -->
        <div class="card">
            <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem;">Daily Impressions Trend</h2>
            <div style="position: relative; height: 220px; width: 100%;">
                <canvas id="reachTrendChart" style="width: 100%; height: 100%;"></canvas>
            </div>
        </div>

        <!-- Clicks Chart -->
        <div class="card">
            <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem;">Affiliate Clicks Trend</h2>
            <div style="position: relative; height: 220px; width: 100%;">
                <canvas id="clickTrendChart" style="width: 100%; height: 100%;"></canvas>
            </div>
        </div>
    </div>

    <!-- Project details -->
    <div class="card project-details">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: white;">Affiliate Campaign Details</h2>
            <a href="{{ route('client.marketing') }}" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.8rem;">
                ⚡ Create New Creative
            </a>
        </div>
        <div style="padding: 1rem; background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 1rem;">
            <h3 style="font-size: 0.95rem; font-weight: 600; color: white; margin-bottom: 0.25rem;">Project Pitch Profile</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem; line-height: 1.45;">
                {{ $project->description ?? 'No campaign description registered. Go to AI Creative Builder to initialize campaign configurations.' }}
            </p>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.85rem; color: var(--text-muted);">
            <div>
                <strong>Active Platforms:</strong> <span class="platform-tag twitter" style="margin-left: 0.5rem;">Twitter / X</span> <span class="platform-tag reddit">Reddit</span>
            </div>
            <div style="text-align: right;">
                <strong>Simulation State:</strong> <span style="color: #34d399; font-weight: 700;">ACTIVE RUNNER</span>
            </div>
        </div>
    </div>

    <!-- Launched AI Campaigns Section -->
    <div style="margin-top: 2.5rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; color: white; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>🎨 My Launched AI Campaigns & Creatives</span>
            <span class="badge badge-qualified" style="font-size: 0.75rem; background: rgba(99, 102, 241, 0.15); color: #818cf8;">{{ count($campaigns) }} Published</span>
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
            @forelse($campaigns as $campaign)
                <div class="card" style="display: flex; flex-direction: column; gap: 1rem; border-color: rgba(99, 102, 241, 0.15); background: rgba(18, 24, 38, 0.4); padding: 1.25rem; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <span class="platform-tag {{ strtolower($campaign->platform) }}">
                            {{ ucfirst($campaign->platform) }}
                        </span>
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">{{ $campaign->created_at->diffForHumans() }}</span>
                    </div>

                    <div>
                        <h3 style="font-size: 1rem; font-weight: 800; color: white; margin-bottom: 0.5rem; line-height: 1.35; letter-spacing: -0.2px;">
                            {{ $campaign->title }}
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-main); line-height: 1.5; white-space: pre-wrap;">{{ $campaign->content }}</p>
                    </div>

                    @if($campaign->image_url)
                        <div style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); aspect-ratio: 3/2; background: rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center; position: relative;">
                            <img src="{{ $campaign->image_url }}" alt="Campaign Creative" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.onerror=null; this.src='{{ (str_contains(strtolower($campaign->title), 'coffee') || str_contains(strtolower($campaign->content), 'coffee')) ? 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600' : 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600' }}';">
                        </div>
                    @endif

                    <!-- Creative Performance Stats -->
                    @php
                        $campaignSeed = crc32($campaign->external_id);
                        $itemReach = abs($campaignSeed % 15000) + 2500;
                        $itemClicks = round($itemReach * (0.015 + ($campaignSeed % 100) / 2500));
                        $itemSpend = round(($itemReach / 1000) * 115);
                        $itemConversions = round($itemClicks * 0.05);
                        $itemCommissions = $itemConversions * 500;
                    @endphp
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.8rem; background: rgba(0, 0, 0, 0.2); border-radius: 10px; padding: 0.75rem; border: 1px solid var(--border-color); margin-top: auto;">
                        <div>
                            <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; font-weight: 600;">Reach</span>
                            <strong style="color: white; font-weight: 700;">{{ number_format($itemReach) }} views</strong>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; font-weight: 600;">Clicks / CTR</span>
                            <strong style="color: #c084fc; font-weight: 700;">{{ $itemClicks }} ({{ round(($itemClicks / $itemReach) * 100, 2) }}%)</strong>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; font-weight: 600;">Spend</span>
                            <strong style="color: #fb7185; font-weight: 700;">INR {{ number_format($itemSpend) }}</strong>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); display: block; font-size: 0.7rem; text-transform: uppercase; font-weight: 600;">Commissions</span>
                            <strong style="color: #34d399; font-weight: 700;">INR {{ number_format($itemCommissions) }}</strong>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.75rem; padding: 4rem 1rem; border: 2px dashed var(--border-color); border-radius: 12px; text-align: center; background: rgba(255,255,255,0.01);">
                    <span style="font-size: 2rem;">🎨</span>
                    <h3 style="font-weight: 700; color: white; margin: 0;">No Launched Campaigns Yet</h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem; max-width: 300px; margin: 0; line-height: 1.4;">
                        Generate your first affiliate campaign creative and click 'Digital Market It!' to see it displayed here.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const days = @json($days);
        const reachValues = @json($reachTrend);
        const clickValues = @json($clickTrend);
        
        drawGradientChart('reachTrendChart', days, reachValues, '#6366f1', 'rgba(99, 102, 241, 0.2)');
        drawGradientChart('clickTrendChart', days, clickValues, '#3b82f6', 'rgba(59, 130, 246, 0.2)');
    });

    function drawGradientChart(canvasId, days, values, lineColor, fillGradColor) {
        const canvas = document.getElementById(canvasId);
        if(!canvas) return;
        const ctx = canvas.getContext('2d');
        
        const dpr = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        ctx.scale(dpr, dpr);
        
        const width = rect.width;
        const height = rect.height;
        const padding = { top: 15, right: 15, bottom: 25, left: 35 };
        
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;
        
        const maxValue = Math.max(...values, 5);
        
        // Grid lines
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.04)';
        ctx.lineWidth = 1;
        ctx.font = '500 10px "Plus Jakarta Sans"';
        ctx.fillStyle = '#4b5563';
        
        for(let i = 0; i <= 3; i++) {
            const y = padding.top + (chartHeight / 3) * i;
            const val = Math.round(maxValue - (maxValue / 3) * i);
            
            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(width - padding.right, y);
            ctx.stroke();
            
            ctx.fillText(val, padding.left - 22, y + 3);
        }
        
        const points = [];
        const xStep = chartWidth / (days.length - 1);
        
        for(let i = 0; i < days.length; i++) {
            const x = padding.left + xStep * i;
            const y = padding.top + chartHeight - (values[i] / maxValue) * chartHeight;
            points.push({ x, y });
        }
        
        // Gradient fill
        const areaGradient = ctx.createLinearGradient(0, padding.top, 0, padding.top + chartHeight);
        areaGradient.addColorStop(0, fillGradColor);
        areaGradient.addColorStop(1, 'rgba(0, 0, 0, 0.0)');
        
        ctx.beginPath();
        ctx.moveTo(points[0].x, padding.top + chartHeight);
        for(let i = 0; i < points.length; i++) {
            ctx.lineTo(points[i].x, points[i].y);
        }
        ctx.lineTo(points[points.length - 1].x, padding.top + chartHeight);
        ctx.closePath();
        ctx.fillStyle = areaGradient;
        ctx.fill();
        
        // Line
        ctx.beginPath();
        ctx.moveTo(points[0].x, points[0].y);
        for(let i = 1; i < points.length; i++) {
            const cpX1 = points[i-1].x + xStep / 2;
            const cpY1 = points[i-1].y;
            const cpX2 = points[i].x - xStep / 2;
            const cpY2 = points[i].y;
            ctx.bezierCurveTo(cpX1, cpY1, cpX2, cpY2, points[i].x, points[i].y);
        }
        ctx.strokeStyle = lineColor;
        ctx.lineWidth = 2.5;
        ctx.shadowColor = lineColor + '55';
        ctx.shadowBlur = 6;
        ctx.stroke();
        ctx.shadowBlur = 0;
        
        // Dots
        for(let i = 0; i < points.length; i++) {
            ctx.beginPath();
            ctx.arc(points[i].x, points[i].y, 3, 0, 2 * Math.PI);
            ctx.fillStyle = lineColor;
            ctx.fill();
            ctx.lineWidth = 1.5;
            ctx.strokeStyle = '#fff';
            ctx.stroke();
            
            ctx.fillStyle = '#9ca3af';
            ctx.textAlign = 'center';
            ctx.fillText(days[i], points[i].x, padding.top + chartHeight + 15);
        }
    }
</script>
@endsection
