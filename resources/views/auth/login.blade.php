<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lakshya.ai</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.15) 0%, transparent 40%),
                        #0b0f19;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 1rem;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(22, 30, 49, 0.75);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .brand-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            text-align: center;
        }
        .logo {
            width: 50px;
            height: 50px;
            background: var(--primary-gradient);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: white;
            font-size: 1.75rem;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
            margin-bottom: 0.5rem;
        }
        .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .demo-credentials {
            background: rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 1rem;
            font-size: 0.8rem;
            color: #a5b4fc;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .demo-credential-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .demo-credential-row:hover {
            background: rgba(99, 102, 241, 0.15);
        }
        .btn-submit {
            margin-top: 0.5rem;
            font-size: 0.95rem;
            padding: 0.75rem;
        }
        .link-text {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }
        .link-text a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        .link-text a:hover {
            text-decoration: underline;
        }
        .error-alert {
            background: rgba(244, 63, 94, 0.1);
            color: #fb7185;
            border: 1px solid rgba(244, 63, 94, 0.2);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-header">
            <div class="logo">L</div>
            <h2 class="title">Welcome back</h2>
            <p class="subtitle">Scrape, Qualify, and Convert Leads</p>
        </div>

        @if($errors->any())
            <div class="error-alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form autocomplete="off" action="{{ route('login') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="name@company.com" required value="{{ old('email') }}" autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary btn-submit">Sign In</button>
        </form>

        <div class="demo-credentials" style="margin-top: 1rem;">
            <div style="font-weight: 700; margin-bottom: 0.25rem;">💡 Demo Accounts (Click to Prefill):</div>
            <div class="demo-credential-row" onclick="prefill('admin@lakshya.ai', 'admin123')">
                <span>🔑 Admin: admin@lakshya.ai</span>
                <span style="font-size: 0.7rem; background: var(--primary-color); color: white; padding: 2px 6px; border-radius: 4px;">Select</span>
            </div>
            <div class="demo-credential-row" onclick="prefill('sarah@jewelrybloom.com', 'client123')">
                <span>🔑 Client: sarah@jewelrybloom.com</span>
                <span style="font-size: 0.7rem; background: var(--primary-color); color: white; padding: 2px 6px; border-radius: 4px;">Select</span>
            </div>
        </div>

        <p class="link-text">Don't have an account? <a href="{{ route('register') }}">Create an account</a></p>
    </div>

    <script>
        function prefill(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
