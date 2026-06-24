/**
 * Lakshya Admin Dashboard - Chart rendering & scraper trigger
 * Extracted from admin/dashboard.blade.php
 */

/**
 * Render the leads trend chart on a canvas element
 */
export function renderLeadsChart(canvasId, days, values) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    // Handle High DPI displays
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);

    const width = rect.width;
    const height = rect.height;
    const padding = { top: 20, right: 20, bottom: 30, left: 35 };

    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;

    const maxValue = Math.max(...values, 5);
    const xStep = chartWidth / (days.length - 1);

    // Draw grid lines
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.04)';
    ctx.lineWidth = 1;
    ctx.font = '500 10px "Plus Jakarta Sans"';
    ctx.fillStyle = '#4b5563';

    for (let i = 0; i <= 4; i++) {
        const y = padding.top + (chartHeight / 4) * i;
        const val = Math.round(maxValue - (maxValue / 4) * i);

        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(width - padding.right, y);
        ctx.stroke();

        ctx.fillText(val, padding.left - 20, y + 3);
    }

    // Calculate points
    const points = [];
    for (let i = 0; i < days.length; i++) {
        const x = padding.left + xStep * i;
        const y = padding.top + chartHeight - (values[i] / maxValue) * chartHeight;
        points.push({ x, y });
    }

    // Fill area with gradient
    const gradient = ctx.createLinearGradient(0, padding.top, 0, padding.top + chartHeight);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

    ctx.beginPath();
    ctx.moveTo(points[0].x, padding.top + chartHeight);
    points.forEach(p => ctx.lineTo(p.x, p.y));
    ctx.lineTo(points[points.length - 1].x, padding.top + chartHeight);
    ctx.closePath();
    ctx.fillStyle = gradient;
    ctx.fill();

    // Draw line path
    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);
    for (let i = 1; i < points.length; i++) {
        const cpX1 = points[i - 1].x + xStep / 2;
        const cpY1 = points[i - 1].y;
        const cpX2 = points[i].x - xStep / 2;
        const cpY2 = points[i].y;
        ctx.bezierCurveTo(cpX1, cpY1, cpX2, cpY2, points[i].x, points[i].y);
    }
    ctx.strokeStyle = '#6366f1';
    ctx.lineWidth = 3;
    ctx.shadowColor = 'rgba(99, 102, 241, 0.4)';
    ctx.shadowBlur = 10;
    ctx.stroke();
    ctx.shadowBlur = 0;

    // Draw dots and X labels
    for (let i = 0; i < points.length; i++) {
        ctx.beginPath();
        ctx.arc(points[i].x, points[i].y, 4, 0, 2 * Math.PI);
        ctx.fillStyle = '#a855f7';
        ctx.fill();
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#fff';
        ctx.stroke();

        ctx.fillStyle = '#9ca3af';
        ctx.textAlign = 'center';
        ctx.fillText(days[i], points[i].x, padding.top + chartHeight + 18);
    }
}

/**
 * Trigger the VM scraper run via API
 */
export function triggerScraperRun() {
    const btn = document.getElementById('btn-trigger-scraper');
    const btnText = document.getElementById('trigger-btn-text');
    const btnIcon = document.getElementById('trigger-btn-icon');
    if (!btn || !btnText || !btnIcon) return;

    const originalText = btnText.innerText;

    btn.disabled = true;
    btnText.innerText = 'Scraper Running...';

    // Add spinning animation
    if (!document.getElementById('spin-keyframe')) {
        const style = document.createElement('style');
        style.id = 'spin-keyframe';
        style.innerHTML = `
            @keyframes spin-pulse {
                0% { transform: rotate(0deg) scale(1.2); }
                50% { transform: rotate(180deg) scale(1.4); }
                100% { transform: rotate(360deg) scale(1.2); }
            }
        `;
        document.head.appendChild(style);
    }

    btnIcon.style.animation = 'spin-pulse 1.5s linear infinite';

    showToast('⚙️ Scraper Run Started', 'Initiating crawl cycle...', 'info');

    fetch('/admin/vm/trigger', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btnText.innerText = originalText;
        btnIcon.style.animation = '';
        btnIcon.style.transform = '';

        if (data.success) {
            showToast('🚀 Scraper Completed!', `Scraped ${data.stats?.posts_processed || 0} posts & found ${data.stats?.leads_found || 0} qualified leads.`, 'success');
            setTimeout(() => window.location.reload(), 2500);
        } else {
            showToast('❌ Run Failed', data.error || 'Scraper run returned error.', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btnText.innerText = originalText;
        btnIcon.style.animation = '';
        btnIcon.style.transform = '';
        showToast('❌ Network Error', 'Failed to connect: ' + err.message, 'error');
    });
}

/**
 * Show a styled toast notification
 */
function showToast(title, message, type = 'info') {
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) existingToast.remove();

    const toast = document.createElement('div');
    toast.className = 'toast-notification';

    let borderStyle = 'border-color: var(--border-color);';
    let titleColor = 'color: white;';
    if (type === 'success') {
        borderStyle = 'border-color: rgba(52, 211, 153, 0.4); box-shadow: 0 10px 35px rgba(52, 211, 153, 0.15);';
        titleColor = 'color: #34d399;';
    } else if (type === 'error') {
        borderStyle = 'border-color: rgba(239, 68, 68, 0.4); box-shadow: 0 10px 35px rgba(239, 68, 68, 0.15);';
        titleColor = 'color: #f87171;';
    }

    toast.style = borderStyle;
    toast.innerHTML = `
        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
            <span style="font-size: 0.9rem; font-weight: 700; ${titleColor}">${title}</span>
            <span style="font-size: 0.8rem; color: var(--text-muted);">${message}</span>
        </div>
    `;
    document.body.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    });

    setTimeout(() => {
        toast.style.transform = 'translateY(-20px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 4500);
}