<?php

namespace App\Http\Controllers;

use App\Http\Traits\ActiveProjectTrait;
use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    use ActiveProjectTrait;

    public function emails()
    {
        $emails = EmailLog::orderBy('created_at', 'desc')->get();
        return view('admin.email-logs', compact('emails'));
    }

    public function sendTestEmail(Request $request)
    {
        try {
            $to = $request->input('to', 'founder@targetsaas.com');
            $subject = $request->input('subject', '🎯 Partnership Proposal: Custom AI Integration');
            
            $body_html = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; color: #333333;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="color: #6366f1; margin: 0;">Lakshya.ai Outreach</h2>
                </div>
                <p>Hello,</p>
                <p>I noticed your post looking for a reliable custom software development partner to integrate custom AI assistants.</p>
                <p>We build premium, custom AI chatbot interfaces that link directly to database actions. Our clients experience an average 40% reduction in customer support load within the first 30 days.</p>
                <p>I would love to walk you through some similar workflows we\'ve built. Would you be open to a quick chat this week?</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://lakshya.ai/consult" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">Book a Free 15-Min Consult</a>
                </div>
                <p style="font-size: 0.85rem; color: #777777; border-top: 1px solid #eeeeee; padding-top: 15px; margin-top: 30px;">
                    This is an automated outreach mock simulation dispatch from your Lakshya campaign.
                </p>
            </div>';

            $email = EmailLog::create([
                'to' => $to,
                'subject' => $subject,
                'body_html' => $body_html,
                'sent_at' => now(),
                'status' => 'Sent'
            ]);

            return response()->json([
                'success' => true,
                'email' => $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
