@extends('layouts.admin')

@section('title', 'Billing & Subscription')

@section('content')
<div class="billing-container">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; letter-spacing: -0.5px;">Subscription & Billing</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Monitor platform quotas, manage billing models, and download invoice copies.</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
        
        <!-- Active Plan Quota Card -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div>
                <span class="badge badge-qualified" style="font-size: 0.7rem; text-transform: uppercase;">Active Plan</span>
                <h2 style="font-size: 1.8rem; font-weight: 800; margin-top: 0.5rem; color: white;">
                    {{ $subscription?->tier ?? 'Free' }} Tier
                </h2>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Renews on: {{ $subscription?->billing_cycle_ends_at ? $subscription->billing_cycle_ends_at->format('M d, Y') : 'N/A' }}</span>
            </div>

            @if($subscription && $subscription->limits_json)
                @php
                    $leadsLimit = $subscription->limits_json['leads_monthly'] ?? 100;
                    $scrapesLimit = $subscription->limits_json['scrapes_daily'] ?? 1000;
                    $keywordsLimit = $subscription->limits_json['keywords_limit'] ?? 10;
                    $leadsPercent = $leadsLimit > 0 ? round(($usedLeads / $leadsLimit) * 100) : 0;
                    $keywordsPercent = $keywordsLimit > 0 ? round(($usedKeywords / $keywordsLimit) * 100) : 0;
                @endphp
                <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; flex-direction: column; gap: 1rem;">
                    <!-- Leads Quota -->
                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600;">
                            <span>Monthly Leads Limit</span>
                            <span style="color: white;">{{ $usedLeads }} / {{ $leadsLimit }}</span>
                        </div>
                        <div style="height: 6px; background-color: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; width: {{ min($leadsPercent, 100) }}%; background-color: var(--primary-color);"></div>
                        </div>
                    </div>

                    <!-- Daily Scrapes Limit -->
                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600;">
                            <span>Daily Crawler Scrapes</span>
                            <span style="color: white;">— / {{ $scrapesLimit }}</span>
                        </div>
                        <div style="height: 6px; background-color: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; width: 0%; background-color: #3b82f6;"></div>
                        </div>
                    </div>

                    <!-- Keywords limit -->
                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600;">
                            <span>Active Keywords limit</span>
                            <span style="color: white;">{{ $usedKeywords }} / {{ $keywordsLimit }}</span>
                        </div>
                        <div style="height: 6px; background-color: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; width: {{ min($keywordsPercent, 100) }}%; background-color: #a855f7;"></div>
                        </div>
                    </div>
                </div>
            @else
                <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; color: var(--text-muted); font-size: 0.85rem;">
                    No active subscription. Upgrade to unlock higher quotas.
                </div>
            @endif
        </div>

        <!-- Invoices List Card -->
        <div class="card" style="padding: 1rem;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; padding-left: 0.5rem; color: white;">Invoice Receipts</h3>
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th style="text-align: right;">Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                            <tr>
                                <td style="font-weight: 700;">#INV-2026-00{{ $loop->iteration }}</td>
                                <td style="color: var(--text-muted); font-size: 0.85rem;">
                                    {{ $inv->invoice_date ? $inv->invoice_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td style="font-weight: bold; color: white;">${{ number_format($inv->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $inv->status == 'Paid' ? 'badge-qualified' : 'badge-closed' }}" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="#" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.75rem;">
                                        📄 PDF Invoice
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem 0;">No invoices generated.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
