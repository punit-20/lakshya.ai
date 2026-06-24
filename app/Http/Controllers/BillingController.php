<?php

namespace App\Http\Controllers;

use App\Http\Traits\ActiveProjectTrait;
use App\Models\Subscription;
use App\Models\Invoice;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    use ActiveProjectTrait;

    public function billing()
    {
        $userId = $this->getAuthUserId();
        $subscription = Subscription::where('user_id', $userId)->first();
        $invoices = $subscription ? Invoice::where('subscription_id', $subscription->id)->orderBy('invoice_date', 'desc')->get() : collect();

        // Calculate actual usage stats for quota display
        $activeProjectId = $this->getActiveProjectId();
        $usedLeads = \App\Models\Lead::where('project_id', $activeProjectId)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        $usedKeywords = \App\Models\Keyword::where('status', 'Active')->count();

        return view('admin.billing', compact('subscription', 'invoices', 'usedLeads', 'usedKeywords'));
    }
}
