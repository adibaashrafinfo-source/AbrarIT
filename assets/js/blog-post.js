(async function () {
  const params = new URLSearchParams(window.location.search);
  const slug = params.get('slug');
  const loadingEl = document.getElementById('post-loading');
  const headerEl = document.getElementById('post-header');
  const notFoundEl = document.getElementById('post-not-found');
  const contentEl = document.getElementById('post-content');

  function showNotFound() {
    loadingEl.style.display = 'none';
    notFoundEl.style.display = '';
    contentEl.innerHTML = '<p>We couldn\'t find that article. It may have been unpublished or the link may be out of date.</p><p><a href="blog.html">Browse all articles →</a></p>';
  }

  const apiBase = window.CMS_API_BASE;
  if (!slug || !apiBase) {
    showNotFound();
    return;
  }

  try {
    const res = await fetch(`${apiBase.replace(/\/$/, '')}/api.php?resource=blog-post&slug=${encodeURIComponent(slug)}`, {
      headers: { Accept: 'application/json' },
    });
    const data = await res.json();
    if (!res.ok || !data.ok || !data.data) {
      showNotFound();
      return;
    }

    const post = data.data;
    document.getElementById('page-title').textContent = `${post.title} — Abrar IT Blog`;
    document.getElementById('meta-description').setAttribute('content', post.excerpt || post.title);
    document.getElementById('post-category').textContent = post.category || 'Blog';
    document.getElementById('post-title-h1').textContent = post.title;
    document.getElementById('post-breadcrumb').textContent = post.title;
    const d = post.published_at ? new Date(post.published_at.replace(' ', 'T')) : null;
    document.getElementById('post-date').textContent = d && !isNaN(d.getTime())
      ? d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
      : '';
    contentEl.innerHTML = post.content || `<p>${post.excerpt || ''}</p>`;

    loadingEl.style.display = 'none';
    headerEl.style.display = '';

    const ld = document.createElement('script');
    ld.type = 'application/ld+json';
    ld.textContent = JSON.stringify({
      '@context': 'https://schema.org',
      '@type': 'BlogPosting',
      headline: post.title,
      description: post.excerpt || '',
      datePublished: post.published_at || '',
      author: { '@type': 'Organization', name: 'Abrar IT' },
      publisher: { '@type': 'Organization', name: 'Abrar IT' },
    });
    document.head.appendChild(ld);
  } catch (err) {
    showNotFound();
  }
})();
