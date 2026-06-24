<header class="site-header" style="background: var(--header-bg); padding: 1rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color);">
    <a href="{{ url('/') }}" class="logo" style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color); text-decoration: none;">Lakshya AI</a>
    <nav class="nav-links" style="display: flex; gap: 1.5rem;">
        <a href="{{ route('login') }}" style="color: var(--text-primary); text-decoration: none;">Login</a>
        <a href="{{ route('register') }}" style="color: var(--text-primary); text-decoration: none;">Sign Up</a>
        <a href="{{ url('/careers') }}" style="color: var(--text-primary); text-decoration: none;">Careers</a>
    </nav>
</header>
