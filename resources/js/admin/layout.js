/**
 * Lakshya Admin Layout - Shared UI utilities
 * Extracted from layouts/admin.blade.php
 */

// CSRF Token
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

/**
 * Switch active project via selector
 */
export function switchProject(projectId) {
    if (!window.csrfToken) return;
    
    fetch('/admin/switch-project', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({ project_id: projectId })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            window.location.reload();
        }
    });
}

/**
 * Toggle language dropdown
 */
export function toggleLangDropdown() {
    const dropdown = document.getElementById('langDropdown');
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }
}

/**
 * Mark notifications as read
 */
export function toggleNotifications() {
    if (!window.csrfToken) return;
    
    fetch('/admin/notifications/read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const badge = document.querySelector('.bell-badge');
            if(badge) badge.remove();
        }
    });
}

/**
 * Show a toast notification
 */
export function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.style.pointerEvents = 'auto';
    toast.style.background = 'rgba(22, 30, 49, 0.95)';
    toast.style.backdropFilter = 'blur(12px)';
    toast.style.border = '1px solid rgba(255, 255, 255, 0.08)';
    toast.style.color = 'white';
    toast.style.padding = '0.85rem 1.25rem';
    toast.style.borderRadius = '12px';
    toast.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.4)';
    toast.style.fontSize = '0.875rem';
    toast.style.fontWeight = '500';
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.gap = '0.6rem';
    toast.style.minWidth = '280px';
    toast.style.maxWidth = '400px';
    toast.style.transform = 'translateY(20px)';
    toast.style.opacity = '0';
    toast.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';

    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: '🎯'
    };
    const icon = icons[type] || 'ℹ️';

    toast.innerHTML = `<span style="font-size: 1.1rem;">${icon}</span><span style="flex-grow: 1;">${message}</span>`;
    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    });

    setTimeout(() => {
        toast.style.transform = 'translateY(-20px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

/**
 * Register PWA service worker
 */
export function registerServiceWorker() {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('ServiceWorker registered:', reg.scope))
                .catch(err => console.log('ServiceWorker failed:', err));
        });
    }
}

/**
 * Setup PWA install prompt
 */
export function setupPwaInstall() {
    let deferredPrompt;
    
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        
        const pwaBtn = document.createElement('div');
        pwaBtn.id = 'pwa-install-banner';
        Object.assign(pwaBtn.style, {
            position: 'fixed',
            bottom: '20px',
            left: '20px',
            zIndex: '9999',
            background: 'linear-gradient(135deg, #6366f1, #8b5cf6)',
            color: 'white',
            padding: '0.65rem 1.25rem',
            borderRadius: '12px',
            boxShadow: '0 10px 25px rgba(99, 102, 241, 0.4)',
            cursor: 'pointer',
            display: 'flex',
            alignItems: 'center',
            gap: '0.5rem',
            fontSize: '0.85rem',
            fontWeight: 'bold',
            border: '1px solid rgba(255,255,255,0.15)',
            transition: 'transform 0.2s'
        });
        pwaBtn.innerHTML = '🎯 <span>Install App</span>';
        
        pwaBtn.addEventListener('mouseenter', () => { pwaBtn.style.transform = 'scale(1.05)'; });
        pwaBtn.addEventListener('mouseleave', () => { pwaBtn.style.transform = 'scale(1)'; });
        
        pwaBtn.addEventListener('click', () => {
            pwaBtn.style.display = 'none';
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(() => { deferredPrompt = null; });
        });
        document.body.appendChild(pwaBtn);
    });
}

// Auto-initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    registerServiceWorker();
    setupPwaInstall();
    
    // Close dropdown on outside click
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.language-selector-container')) {
            const dropdown = document.getElementById('langDropdown');
            if (dropdown) dropdown.style.display = 'none';
        }
    });
});