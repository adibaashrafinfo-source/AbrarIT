/* Abrar IT — Hero 3D illustration: subtle mouse parallax on floating elements */
document.addEventListener('DOMContentLoaded', () => {
  const scene = document.querySelector('.hero3d');
  if (!scene) return;

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  if (reduceMotion || !canHover) return;

  const layers = Array.from(scene.querySelectorAll('[data-depth]'));
  if (!layers.length) return;

  let targetX = 0, targetY = 0, rafId = null;

  function apply() {
    layers.forEach((el) => {
      const depth = parseFloat(el.dataset.depth) || 0.3;
      const x = targetX * depth * 14;
      const y = targetY * depth * 14;
      el.style.transform = `translate3d(${x}px, ${y}px, 0)`;
    });
    rafId = null;
  }

  window.addEventListener('mousemove', (e) => {
    const rect = scene.getBoundingClientRect();
    const cx = rect.left + rect.width / 2;
    const cy = rect.top + rect.height / 2;
    const dx = (e.clientX - cx) / (rect.width / 2);
    const dy = (e.clientY - cy) / (rect.height / 2);
    targetX = Math.max(-1, Math.min(1, dx));
    targetY = Math.max(-1, Math.min(1, dy));
    if (!rafId) rafId = requestAnimationFrame(apply);
  }, { passive: true });

  window.addEventListener('mouseleave', () => {
    targetX = 0; targetY = 0;
    if (!rafId) rafId = requestAnimationFrame(apply);
  });
});
