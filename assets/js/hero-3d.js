/* Abrar IT — Hero illustration: subtle mouse-tilt on the image */
document.addEventListener('DOMContentLoaded', () => {
  const scene = document.querySelector('.hero3d');
  const img = document.querySelector('.hero3d-img');
  if (!scene || !img) return;

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  if (reduceMotion || !canHover) return;

  let rafId = null, targetX = 0, targetY = 0;

  function apply() {
    img.style.transform = `rotateY(${targetX * 6}deg) rotateX(${targetY * -6}deg)`;
    rafId = null;
  }

  scene.addEventListener('mousemove', (e) => {
    const rect = scene.getBoundingClientRect();
    const dx = (e.clientX - (rect.left + rect.width / 2)) / (rect.width / 2);
    const dy = (e.clientY - (rect.top + rect.height / 2)) / (rect.height / 2);
    targetX = Math.max(-1, Math.min(1, dx));
    targetY = Math.max(-1, Math.min(1, dy));
    if (!rafId) rafId = requestAnimationFrame(apply);
  }, { passive: true });

  scene.addEventListener('mouseleave', () => {
    targetX = 0; targetY = 0;
    if (!rafId) rafId = requestAnimationFrame(apply);
  });
});
