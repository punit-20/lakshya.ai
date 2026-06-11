@extends('layouts.admin')

@section('title', 'Client Manager')

@section('styles')
<style>
    .clients-card {
        margin-top: 1.5rem;
    }

    .action-badge {
        font-weight: 700;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    .action-badge.pro {
        background: rgba(168, 85, 247, 0.15);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .action-badge.starter {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
</style>
@endsection

@section('content')
<div class="clients-wrapper">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">Client Manager</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
                Manage billing plans, campaign budgets, and launch simulated client testing environments.
            </p>
        </div>
    </div>

    <!-- Pricing Summary Card -->
    <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Active Clients</span>
                <span class="stat-value">{{ count($clients) }}</span>
                <span style="font-size: 0.75rem; color: #34d399;">Target: 8-15 for target profit</span>
            </div>
            <div class="stat-icon-wrapper" style="color: #60a5fa; background: rgba(59, 130, 246, 0.1);">
                👥
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Target Revenue Mix</span>
                <span class="stat-value">INR 38k</span>
                <span style="font-size: 0.75rem; color: var(--text-muted);">OPEX: INR 18k | Net: INR 20k</span>
            </div>
            <div class="stat-icon-wrapper" style="color: #34d399; background: rgba(16, 185, 129, 0.1);">
                📈
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-info">
                <span class="stat-label">Ad Spend Cap (Per Client)</span>
                <span class="stat-value">INR 5k</span>
                <span style="font-size: 0.75rem; color: #fb7185;">Acquisition Target: 16% CR</span>
            </div>
            <div class="stat-icon-wrapper" style="color: #fb7185; background: rgba(244, 63, 94, 0.1);">
                💵
            </div>
        </div>
    </div>

    <!-- Clients Table Card -->
    <div class="card clients-card">
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem;">Client Directory</h2>
        
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Client Detail</th>
                        <th>Subscription Tier</th>
                        <th>Total Reach</th>
                        <th>Affiliate Clicks</th>
                        <th>Conversions</th>
                        <th>Campaign Spend</th>
                        <th>Commission (INR)</th>
                        <th>Simulation Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <strong style="color: white; font-size: 0.9rem;">{{ $client->name }}</strong>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $client->email }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="action-badge {{ strtolower($client->subscription->tier ?? 'starter') }}">
                                    {{ $client->subscription->tier ?? 'Starter' }}
                                </span>
                            </td>
                            <td>{{ number_format($client->total_reach) }}</td>
                            <td>{{ number_format($client->total_clicks) }}</td>
                            <td>{{ $client->conversions ?? $client->total_clicks / 20 }}</td>
                            <td>INR {{ number_format($client->total_spend) }} / 5,000</td>
                            <td style="color: #34d399; font-weight: 700;">INR {{ number_format($client->total_commission) }}</td>
                            <td>
                                <a href="{{ route('admin.clients.impersonate', $client->id) }}" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">
                                    ⚡ Test Client Access
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No clients registered in the directory.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
