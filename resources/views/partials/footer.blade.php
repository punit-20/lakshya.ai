<footer class="site-footer" style="padding:1rem; text-align:center; background: var(--footer-bg); color: var(--text-muted);">
    <p>&copy; {{ date('Y') }} Lakshya AI – All rights reserved.</p>
    <nav class="footer-links" style="margin-top:0.5rem;">
        <a href="{{ url('/') }}" style="color: var(--text-primary); margin:0 0.5rem;">Home</a>
        <a href="{{ route('login') }}" style="color: var(--text-primary); margin:0 0.5rem;">Login</a>
        <a href="{{ route('register') }}" style="color: var(--text-primary); margin:0 0.5rem;">Sign Up</a>
        <a href="{{ url('/careers') }}" style="color: var(--text-primary); margin:0 0.5rem;">Careers</a>
    </nav>
</footer>
