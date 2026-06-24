<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use App\Models\CreditUsageLog;
use App\Models\AuditLog;
use App\Models\Notification;

class CreditService
{
    /**
     * Check if a user has sufficient credits to perform an action.
     */
    public function hasCredits(int $userId, int $required = 1): bool
    {
        $user = User::find($userId);
        if ($user && $user->role === 'admin') {
            return true;
        }

        $subscription = Subscription::where('user_id', $userId)->first();
        return $subscription && $subscription->credits >= $required;
    }

    /**
     * Deduct credits from user subscription and log the action.
     *
     * @return bool
     */
    public function deductCredits(
        int $userId,
        int $amount,
        string $action,
        ?string $details = null,
        ?int $leadId = null
    ): bool {
        $user = User::find($userId);
        if ($user && $user->role === 'admin') {
            CreditUsageLog::create([
                'user_id' => $userId,
                'lead_id' => $leadId,
                'action' => $action,
                'credits_used' => 0,
                'details' => $details ?: 'Admin action performed (unlimited tier)',
            ]);
            return true;
        }

        $subscription = Subscription::where('user_id', $userId)->first();
        if ($subscription) {
            $subscription->credits = max(0, $subscription->credits - $amount);
            $subscription->save();

            CreditUsageLog::create([
                'user_id' => $userId,
                'lead_id' => $leadId,
                'action' => $action,
                'credits_used' => $amount,
                'details' => $details,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Check credits and return error response if insufficient.
     *
     * @return array|null  ['error' => message, 'code' => http_code] or null if sufficient
     */
    public function checkClientCredits(int $userId, int $required = 1): ?array
    {
        $user = User::find($userId);
        if ($user && $user->role === 'client' && !$this->hasCredits($userId, $required)) {
            return [
                'error' => 'Insufficient credits. Your current balance is 0. Please upgrade your plan to top up.',
                'code' => 403,
            ];
        }
        return null;
    }
}