<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Project;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected int $trialDurationDays;
    protected int $trialCredits;

    public function __construct()
    {
        $this->trialDurationDays = config('pricing.tiers.free_trial.duration_days', 14);
        $this->trialCredits = config('pricing.tiers.free_trial.credits', 10);
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'User logged in successfully',
                'target_table' => 'users',
                'ip_address' => $request->ip(),
            ]);

            return $this->redirectUser();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'tier' => config('pricing.tiers.free_trial.name', 'Free Trial'),
            'status' => 'Active',
            'credits' => $this->trialCredits,
            'limits_json' => [
                'leads_monthly' => 10,
                'scrapes_daily' => 50,
                'keywords_limit' => 2,
            ],
            'billing_cycle_ends_at' => now()->addDays($this->trialDurationDays),
        ]);

        // Auto create first project/campaign for the client
        Project::create([
            'user_id' => $user->id,
            'name' => $request->name . ' Campaign',
            'description' => 'Default lead generation and social outreach campaign.',
            'status' => 'Active',
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'User registered and created client portal campaign',
            'target_table' => 'users',
            'ip_address' => $request->ip(),
        ]);

        Auth::login($user);

        return redirect()->route('client.dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'User logged out',
                'target_table' => 'users',
                'ip_address' => $request->ip(),
            ]);
        }

        // Exit client impersonation if any
        if (session()->has('impersonating_client_id')) {
            session()->forget('impersonating_client_id');
        }
        session()->forget('active_project_id');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectUser()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('client.dashboard');
    }
}
