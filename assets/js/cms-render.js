/**
 * Progressive enhancement: if assets/js/cms-config.js has CMS_API_BASE set
 * and the backend is reachable, replace the static sample Blog/Portfolio/
 * Services content on this page with live data from the admin panel.
 * If the API isn't configured, is unreachable, or has nothing published
 * yet, the page's existing static markup is left exactly as it is.
 */
document.addEventListener('DOMContentLoaded', async () => {
  const apiBase = window.CMS_API_BASE;
  if (!apiBase) return;

  const base = apiBase.replace(/\/$/, '');

  const fetchJson = async (resource) => {
    try {
      const res = await fetch(`${base}/api.php?resource=${resource}`, { headers: { Accept: 'application/json' } });
      if (!res.ok) return null;
      const data = await res.json();
      return (data && data.ok && Array.isArray(data.data)) ? data.data : null;
    } catch (err) {
      return null;
    }
  };

  const slugify = (text) => (text || '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '') || 'general';

  const arrowIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';

  /* ---- Blog ---- */
  const blogGrid = document.getElementById('cms-blog-grid');
  if (blogGrid) {
    const posts = await fetchJson('blog');
    if (posts && posts.length) {
      // Keep the real cover photos: reuse the static card's <picture> for any
      // CMS post with the same title, so live data doesn't replace photos
      // with plain gradient blocks.
      const norm = (t) => (t || '').toLowerCase().replace(/&amp;/g, '&').replace(/[^a-z0-9]+/g, ' ').trim();
      const staticCovers = {};
      blogGrid.querySelectorAll('.blog-card').forEach((card) => {
        const h = card.querySelector('h3');
        const pic = card.querySelector('.blog-thumb picture, .blog-thumb img');
        if (h && pic) staticCovers[norm(h.textContent)] = pic.outerHTML;
      });
      blogGrid.innerHTML = posts.map((p) => {
        const cover = staticCovers[norm(p.title)] || '';
        return `
        <article class="blog-card reveal">
          <div class="blog-thumb" style="background:linear-gradient(135deg,${p.cover_gradient_start},${p.cover_gradient_end})">
            ${cover}
            <span>${escapeHtml(p.category)}</span>
          </div>
          <div class="blog-body">
            <div class="blog-meta"><span>${formatDate(p.published_at)}</span></div>
            <h3>${escapeHtml(p.title)}</h3>
            <p>${escapeHtml(p.excerpt)}</p>
            <a href="blog-post.html?slug=${encodeURIComponent(p.slug)}" class="blog-link">Read Article ${arrowIcon}</a>
          </div>
        </article>`;
      }).join('');
      window.observeReveals(blogGrid);
    }
  }

  /* ---- Portfolio ---- */
  const portfolioGrid = document.getElementById('cms-portfolio-grid');
  if (portfolioGrid) {
    const items = await fetchJson('portfolio');
    if (items && items.length) {
      portfolioGrid.innerHTML = items.map((p) => {
        const safeUrl = isSafeHttpUrl(p.project_url) ? p.project_url : '';
        const hasUrl = !!safeUrl;
        const tag = hasUrl ? 'a' : 'div';
        const linkAttrs = hasUrl ? `href="${escapeHtml(safeUrl)}" target="_blank" rel="noopener noreferrer"` : '';
        const liveBadge = hasUrl
          ? '<span class="live-badge">Live Preview <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M7 17L17 7M8 7h9v9"/></svg></span>'
          : '';
        return `
        <${tag} ${linkAttrs} class="portfolio-card reveal" data-category="${slugify(p.category)}">
          <div class="portfolio-thumb" style="background:linear-gradient(135deg,${p.cover_gradient_start},${p.cover_gradient_end})">
            <span class="tag">${escapeHtml(p.category)}</span>
            <span class="mark">${escapeHtml(p.thumb_label)}</span>
            ${liveBadge}
          </div>
          <div class="portfolio-body"><h3>${escapeHtml(p.title)}</h3><p>${escapeHtml(p.description)}</p></div>
        </${tag}>`;
      }).join('');
      window.observeReveals(portfolioGrid);

      const filterBar = document.getElementById('cms-filter-bar');
      if (filterBar) {
        const categories = [...new Set(items.map((p) => p.category))];
        filterBar.innerHTML = ['<button class="filter-btn active" data-filter="all">All Work</button>']
          .concat(categories.map((c) => `<button class="filter-btn" data-filter="${slugify(c)}">${escapeHtml(c)}</button>`))
          .join('');
        if (window.bindPortfolioFilters) window.bindPortfolioFilters();
      }
    }
  }

  /* ---- Services (overview grid only) ---- */
  const servicesGrid = document.getElementById('cms-services-grid');
  if (servicesGrid) {
    const services = await fetchJson('services');
    if (services && services.length) {
      const genericIcon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg>';
      servicesGrid.innerHTML = services.map((s) => `
        <a href="contact.html" class="service-card reveal">
          <div class="service-icon">${genericIcon}</div>
          <h3>${escapeHtml(s.name)}</h3>
          <p>${escapeHtml(s.summary)}</p>
          <div class="price">${escapeHtml(s.starting_price)}</div>
        </a>
      `).join('');
      window.observeReveals(servicesGrid);
    }
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  function isSafeHttpUrl(value) {
    if (!value) return false;
    try {
      const url = new URL(value, window.location.href);
      return url.protocol === 'http:' || url.protocol === 'https:';
    } catch (err) {
      return false;
    }
  }

  function formatDate(value) {
    if (!value) return '';
    const d = new Date(value.replace(' ', 'T'));
    if (isNaN(d.getTime())) return '';
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  }
});
