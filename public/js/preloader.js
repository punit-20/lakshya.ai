// Simple preloader hide script
window.addEventListener('load', () => {
  const preloader = document.getElementById('preloader');
  if (preloader) {
    preloader.style.transition = 'opacity 0.5s ease-out';
    preloader.style.opacity = '0';
    setTimeout(() => {
      preloader.remove();
    }, 500);
  }
});
