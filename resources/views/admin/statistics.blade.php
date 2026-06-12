@extends('layouts.admin')

@section('title', 'Economics & Stats')

@section('styles')
<style>
    .stats-wrapper {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .level-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-beginner {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    
    .badge-advanced {
        background: rgba(168, 85, 247, 0.15);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }
    
    .badge-pro {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .metrics-tier-section {
        border: 1px solid var(--border-color);
        background: rgba(18, 24, 38, 0.35);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .tier-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding-bottom: 0.75rem;
    }

    .tier-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stats-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
    }

    .premium-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        transition: transform 0.2s, border-color 0.2s;
    }

    .premium-card:hover {
        transform: translateY(-2px);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .card-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }

    .card-val {
        font-size: 1.6rem;
        font-weight: 800;
        color: white;
    }

    .card-subtext {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .chart-container-grid {
        display: grid;
        grid-template-columns: 1.6fr 1.4fr;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .chart-container-grid {
            grid-template-columns: 1fr;
        }
    }

    .chart-box {
        background: rgba(18, 24, 38, 0.5);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
    }

    .custom-progress-bar {
        height: 6px;
        width: 100%;
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
        overflow: hidden;
        margin-top: 0.25rem;
    }

    .progress-fill {
        height: 100%;
        border-radius: 3px;
    }
</style>
@endsection

@section('content')
<div class="stats-wrapper">
    
    <!-- Title & Navigation -->
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">📈 Company Economics & Growth Report</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
                Real-time target benchmarks, burn rates, subscriber metrics, and financial progress forecasts.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <span class="badge badge-new" style="padding: 0.5rem 1rem;">System Margin: {{ $marginEfficiency }}%</span>
        </div>
    </div>

    <!-- CHARTS SECTION -->
    <div class="chart-container-grid">
        <!-- 6 Months Growth Trend -->
        <div class="chart-box">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: white; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>📊 Inflows, Outflows & Cumulative Net Profit</span>
            </h2>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="financialsTrendChart"></canvas>
            </div>
        </div>

        <!-- Tier Distribution and Inflows/Outflows Summary -->
        <div class="chart-box" style="display: flex; flex-direction: column; justify-content: space-between; gap: 1.5rem;">
            <div>
                <h2 style="font-size: 1.1rem; font-weight: 700; color: white; margin-bottom: 1.25rem;">🍰 Subscription Inflows Distribution</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center;">
                    <div style="position: relative; height: 180px; width: 100%;">
                        <canvas id="planMixChart"></canvas>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.85rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-block; width: 10px; height: 10px; background-color: #6366f1; border-radius: 50%;"></span>
                            <span style="color: var(--text-muted);">Pro Tier (4,999):</span>
                            <strong style="color: white; margin-left: auto;">{{ $proCount }} (INR {{ number_format($proCount * 4999) }})</strong>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-block; width: 10px; height: 10px; background-color: #38bdf8; border-radius: 50%;"></span>
                            <span style="color: var(--text-muted);">Starter Tier (1,499):</span>
                            <strong style="color: white; margin-left: auto;">{{ $starterCount }} (INR {{ number_format($starterCount * 1499) }})</strong>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.5rem;">
                            <span style="display: inline-block; width: 10px; height: 10px; background-color: #a855f7; border-radius: 50%;"></span>
                            <span style="color: var(--text-muted);">Trial/Free:</span>
                            <strong style="color: white; margin-left: auto;">{{ max($activeClientsCount - ($proCount + $starterCount), 0) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OPEX Outflows breakdown -->
            <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1.25rem;">
                <h3 style="font-size: 0.9rem; font-weight: 700; color: white; margin-bottom: 0.75rem;">💸 Monthly Outflow breakdown (OPEX)</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.75rem; font-size: 0.75rem;">
                    @foreach($opexBreakdown as $name => $amount)
                        <div style="background: rgba(255,255,255,0.01); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.5rem 0.75rem;">
                            <span style="color: var(--text-muted); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $name }}</span>
                            <strong style="color: #fb7185; font-size: 0.85rem; display: block; margin-top: 0.15rem;">INR {{ number_format($amount) }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- TIER 1: BEGINNER (Core Operations) -->
    <div class="metrics-tier-section">
        <div class="tier-header">
            <div class="tier-title">
                <span>🌱 Core Operations Report</span>
            </div>
            <span class="level-badge badge-beginner">Beginner Dashboard</span>
        </div>
        
        <div class="stats-card-grid">
            <!-- Inflows -->
            <div class="premium-card">
                <span class="card-label">Monthly Gross Inflows</span>
                <span class="card-val" style="color: #60a5fa;">INR {{ number_format($mrr) }}</span>
                <div class="custom-progress-bar">
                    @php $mrrProgress = round(($mrr / $targets['mrr']) * 100); @endphp
                    <div class="progress-fill" style="width: {{ min($mrrProgress, 100) }}%; background-color: #60a5fa;"></div>
                </div>
                <span class="card-subtext">Target: INR {{ number_format($targets['mrr']) }} / mo ({{ $mrrProgress }}%)</span>
            </div>

            <!-- Outflows -->
            <div class="premium-card">
                <span class="card-label">Monthly Outflow (Spend)</span>
                <span class="card-val" style="color: #fb7185;">INR {{ number_format($totalOpex) }}</span>
                <span class="card-subtext">Fixed operational outflow cost</span>
            </div>

            <!-- Profit/Loss -->
            <div class="premium-card">
                <span class="card-label">Net Inflow (Profit/Loss)</span>
                <span class="card-val" style="color: {{ $netProfit >= 0 ? '#34d399' : '#f43f5e' }};">
                    {{ $netProfit >= 0 ? '+' : '' }}INR {{ number_format($netProfit) }}
                </span>
                <div class="custom-progress-bar">
                    @php $profitProgress = $targets['net_profit'] > 0 ? round(($netProfit / $targets['net_profit']) * 100) : 0; @endphp
                    <div class="progress-fill" style="width: {{ min(max($profitProgress, 0), 100) }}%; background-color: #34d399;"></div>
                </div>
                <span class="card-subtext">Target: INR {{ number_format($targets['net_profit']) }} / mo ({{ max($profitProgress, 0) }}%)</span>
            </div>

            <!-- Target Progress -->
            <div class="premium-card">
                <span class="card-label">Clients Target Progress</span>
                <span class="card-val">{{ $activeClientsCount }} <span style="font-size: 1rem; color: var(--text-muted);">/ {{ $targets['clients'] }}</span></span>
                <div class="custom-progress-bar">
                    @php $clientProgress = round(($activeClientsCount / $targets['clients']) * 100); @endphp
                    <div class="progress-fill" style="width: {{ min($clientProgress, 100) }}%; background-color: #a855f7;"></div>
                </div>
                <span class="card-subtext">Progress toward target size: {{ $clientProgress }}%</span>
            </div>
        </div>
    </div>

    <!-- TIER 2: ADVANCED (Financial CAC & LTV Efficiency) -->
    <div class="metrics-tier-section">
        <div class="tier-header">
            <div class="tier-title">
                <span>📈 Customer Unit Economics</span>
            </div>
            <span class="level-badge badge-advanced">Advanced Analysis</span>
        </div>
        
        <div class="stats-card-grid">
            <!-- LTV -->
            <div class="premium-card">
                <span class="card-label">Customer Lifetime Value (LTV)</span>
                <span class="card-val" style="color: #c084fc;">INR {{ number_format($ltv) }}</span>
                <span class="card-subtext">Based on average retention period</span>
            </div>

            <!-- CAC -->
            <div class="premium-card">
                <span class="card-label">Acquisition Cost (CAC)</span>
                <span class="card-val" style="color: #60a5fa;">INR {{ number_format($cac) }}</span>
                <span class="card-subtext">Client Acquisition cost via ads</span>
            </div>

            <!-- LTV:CAC Ratio -->
            <div class="premium-card">
                <span class="card-label">LTV : CAC Ratio</span>
                <span class="card-val" style="color: #34d399;">{{ $ltvToCacRatio }}</span>
                <span class="card-subtext">SaaS industry benchmark > 3.0x</span>
            </div>

            <!-- ARPU -->
            <div class="premium-card">
                <span class="card-label">Avg Revenue Per User (ARPU)</span>
                <span class="card-val">INR {{ number_format($arpu) }}</span>
                <span class="card-subtext">Average monthly billing value</span>
            </div>
        </div>
    </div>

    <!-- TIER 3: PRO (Financial Health & Runways) -->
    <div class="metrics-tier-section">
        <div class="tier-header">
            <div class="tier-title">
                <span>🛡️ Corporate Financial Health</span>
            </div>
            <span class="level-badge badge-pro">Pro Benchmarks</span>
        </div>
        
        <div class="stats-card-grid">
            <!-- ARR -->
            <div class="premium-card">
                <span class="card-label">Annualized Run Rate (ARR)</span>
                <span class="card-val" style="color: #38bdf8;">INR {{ number_format($arr) }}</span>
                <span class="card-subtext">Projected gross annual inflows</span>
            </div>

            <!-- Burn Rate -->
            <div class="premium-card">
                <span class="card-label">Monthly Corporate Burn Rate</span>
                <span class="card-val" style="color: #f43f5e;">INR {{ number_format($burnRate) }}</span>
                <span class="card-subtext">Fixed cost to run operations</span>
            </div>

            <!-- Runway -->
            <div class="premium-card">
                <span class="card-label">Cash Runway</span>
                <span class="card-val" style="color: #34d399;">{{ $runway }} <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted);">Months</span></span>
                <span class="card-subtext">Safety margin based on capital reserves</span>
            </div>

            <!-- Break-Even client count -->
            <div class="premium-card">
                <span class="card-label">Break-Even Point</span>
                <span class="card-val">{{ $breakEvenClients }} <span style="font-size: 1rem; color: var(--text-muted);">Clients</span></span>
                <span class="card-subtext">Active users needed to cover spend</span>
            </div>
        </div>
    </div>

    <!-- PROJECTIONS PANEL -->
    <div class="card" style="border-color: rgba(99, 102, 241, 0.25);">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: white; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <span>💡 How to Scale Operations and Stay Profitable</span>
            <span class="badge badge-qualified">Growth Checklist</span>
        </h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center;">
            <div style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem; padding-left: 0;">
                    <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="color: #34d399; font-weight: bold;">[✓]</span>
                        <span><strong>Step 1: Set Up VM Crawler Automations:</strong> Maintain Twitter/Reddit keywords to scan high-intent leads constantly. Keep VM hosting costs at INR 2,500/mo.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="color: #34d399; font-weight: bold;">[✓]</span>
                        <span><strong>Step 2: Optimize Ads Budget:</strong> Allocate the INR 5,000 monthly ad budget. Drive traffic to custom client campaigns with a target CPL of INR 100.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="color: #34d399; font-weight: bold;">[✓]</span>
                        <span><strong>Step 3: Convert Trial Signups:</strong> Guide trial leads to compile their live campaigns. Direct them to check their AI outbox graphics previews.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 0.5rem;">
                        <span style="color: #34d399; font-weight: bold;">[✓]</span>
                        <span><strong>Step 4: Scale Retention (LTV):</strong> Onboard users onto Starter or Pro subscription tiers. Keep customer support salary at INR 8,000/mo to guarantee a **25.6x LTV:CAC ratio**.</span>
                    </li>
                </ul>
            </div>
            
            <div style="background: rgba(255, 255, 255, 0.01); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                <h3 style="font-size: 0.95rem; font-weight: 700; color: white; margin: 0;">🏆 Target Scenarios Mix</h3>
                <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.8rem;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(255,255,255,0.05); padding-bottom: 0.4rem;">
                        <span style="color: var(--text-muted);">Starter Client Price:</span>
                        <strong style="color: white;">INR 1,499 / mo</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(255,255,255,0.05); padding-bottom: 0.4rem;">
                        <span style="color: var(--text-muted);">Pro Client Price:</span>
                        <strong style="color: white;">INR 4,999 / mo</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed rgba(255,255,255,0.05); padding-bottom: 0.4rem;">
                        <span style="color: var(--text-muted);">Break-Even Users:</span>
                        <strong style="color: #fb7185;">{{ $breakEvenClients }} clients</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 0.85rem; padding-top: 0.2rem; color: #34d399;">
                        <span>Target Profit Matrix:</span>
                        <span>10 Starter + 5 Pro = +INR 21,985 Profit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // Font configs for Chart.js matching "Plus Jakarta Sans"
        Chart.defaults.font.family = '"Plus Jakarta Sans", -apple-system, sans-serif';
        Chart.defaults.color = '#9ca3af';
        
        // 1. Inflows vs Outflows and Profits Chart
        const financialsCtx = document.getElementById('financialsTrendChart').getContext('2d');
        
        // Gradients
        const profitGrad = financialsCtx.createLinearGradient(0, 0, 0, 300);
        profitGrad.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        profitGrad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
        
        const inflowsGrad = financialsCtx.createLinearGradient(0, 0, 0, 300);
        inflowsGrad.addColorStop(0, 'rgba(59, 130, 246, 0.15)');
        inflowsGrad.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(financialsCtx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [
                    {
                        label: 'Gross Inflows (Revenue)',
                        data: @json($historicalInflows),
                        borderColor: '#3b82f6',
                        backgroundColor: inflowsGrad,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    },
                    {
                        label: 'Net Profits',
                        data: @json($historicalProfit),
                        borderColor: '#10b981',
                        backgroundColor: profitGrad,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    },
                    {
                        label: 'Outflows (Spend)',
                        data: @json($historicalSpend),
                        borderColor: '#fb7185',
                        borderDash: [5, 5],
                        borderWidth: 2,
                        fill: false,
                        tension: 0,
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#e5e7eb',
                            boxWidth: 15,
                            padding: 15
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'INR ' + new Intl.NumberFormat().format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.04)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#9ca3af'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.04)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#9ca3af',
                            callback: function(value) {
                                return 'INR ' + new Intl.NumberFormat().format(value);
                            }
                        }
                    }
                }
            }
        });

        // 2. Pricing Plan Mix Chart
        const planCtx = document.getElementById('planMixChart').getContext('2d');
        new Chart(planCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pro Tier', 'Starter Tier', 'Trial/Free'],
                datasets: [{
                    data: [
                        {{ $proCount }},
                        {{ $starterCount }},
                        {{ max($activeClientsCount - ($proCount + $starterCount), 0) }}
                    ],
                    backgroundColor: ['#6366f1', '#38bdf8', 'rgba(255,255,255,0.08)'],
                    borderColor: '#111827',
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.parsed + ' Clients';
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endsection
