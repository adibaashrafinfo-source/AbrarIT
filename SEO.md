# Abrar IT — SEO Audit & Technical SEO Implementation

Date: 2026-11-27

## Audit findings (before this change)

| Check | Status |
|---|---|
| Unique `<title>` + meta description per page | ✅ Already good on all 7 pages |
| Single `<h1>` per page | ✅ Already correct |
| `lang="en"` set | ✅ Already correct |
| Image `alt` text | ✅ Already correct (only real `<img>` is the logo; rest are inline SVG icons) |
| `robots.txt` | ❌ Missing |
| `sitemap.xml` | ❌ Missing |
| Canonical URLs | ❌ Missing |
| Open Graph / Twitter Card tags | ❌ Missing (link previews on WhatsApp/Facebook/X showed nothing) |
| Favicon sizes / `apple-touch-icon` / manifest | ❌ Missing (only one generic icon link) |
| Structured data (JSON-LD) | ❌ None |
| Custom 404 page | ❌ Missing (host default error page) |
| CMS admin pages indexable | ⚠️ No protection — `/cms/*` could have been crawled and indexed |

## What was implemented

**1. `robots.txt`** — explicitly allows Googlebot, Bingbot, and the major AI-answer crawlers (GPTBot, ChatGPT-User, PerplexityBot, ClaudeBot, anthropic-ai, Google-Extended) so Abrar IT can be crawled *and cited* in AI search results, not just traditional search. Blocks `/cms/` (the admin panel) and `CCBot` (training-only scraper, blocking it doesn't affect citation in any assistant). References `sitemap.xml`.

**2. `sitemap.xml`** — all 7 public pages with priority and change frequency.

**3. Canonical tags** — every page now declares its own canonical URL, preventing duplicate-content ambiguity.

**4. Open Graph + Twitter Card tags** — every page now has `og:title`, `og:description`, `og:image`, `og:url`, `og:type`, `og:site_name`, and matching `twitter:*` tags, so links shared on WhatsApp, Facebook, LinkedIn or X show a proper branded preview card instead of nothing.

**5. New brand icon set** (`assets/img/`) generated from your logo's orange so every surface has a proper icon:
   - `favicon-16.png`, `favicon-32.png` — browser tab icon
   - `apple-touch-icon.png` (180×180) — iOS home-screen icon
   - `icon-192.png`, `icon-512.png` — Android / PWA manifest icons
   - `og-image.png` (1200×630) — the social share preview card

**6. `assets/site.webmanifest`** — makes the site installable as a lightweight app on mobile home screens, using the new icons.

**7. Structured data (JSON-LD)** on every page:
   - `Organization` + `WebSite` schema (name, logo, description, email, phone, Dhaka address) — helps AI systems and Google understand who Abrar IT is as an entity.
   - `BreadcrumbList` on every inner page, matching the visible breadcrumb UI exactly.
   - `FAQPage` schema on **Services** and **Contact** — the exact same questions/answers already visible in the FAQ accordions, so Google/AI can surface them directly as rich results.
   - **Deliberately not added**: review/rating schema (`AggregateRating`/`Review`). The testimonials on the homepage are illustrative sample content, not verified customer reviews with consent — marking them up as schema would be misleading structured data. Add this once you have real, attributable client reviews.

**8. Custom branded `404.html`** — replaces the host's generic error page with one that matches the site and links back to Home/Contact. Both Vercel and GitHub Pages serve this automatically for unmatched URLs by convention — no extra config needed.

**9. CMS admin panel hardened against indexing** — `/cms/*` is disallowed in `robots.txt`, and every admin page (including the public login/setup screens) now also sends `<meta name="robots" content="noindex, nofollow">` as defense-in-depth.

## Decisions made on your behalf (reasonable defaults, flag if you'd rather change them)

- **Canonical domain used**: `https://abrar-it.vercel.app` (your current live Vercel URL). All canonical tags, OG tags, and the sitemap point here. **When you buy a custom domain, these all need a find-and-replace** — ask me to do it and I will, in one pass.
- **URLs kept as `.html`** (e.g. `/about.html`, not `/about`). The site is deployed to both Vercel *and* GitHub Pages from the same files; GitHub Pages can't do server-side URL rewriting, so clean extensionless URLs would work on one host and break on the other. Keeping `.html` keeps both deployments identical and avoids duplicate-content risk between two different URL shapes.
- **No fabricated social profile links** (`sameAs` in the Organization schema was left out) — the footer's social icons are still placeholder `#` links. Once you have real Facebook/Instagram/LinkedIn/YouTube URLs, add them to the footer and I'll add matching `sameAs` entries to the schema.

## Round 2 — additional technical fixes (2026-11-27)

- **Title/description keyword tuning**: every page's `<title>` and meta description now mention Dhaka/Bangladesh naturally (e.g. "Digital Agency in Bangladesh", "Abrar IT Digital Agency, Dhaka Bangladesh") — this is the single highest-leverage on-page change for ranking in local searches like "digital agency Dhaka" or "web design company Bangladesh". Fixed one description (Services) that was running long enough to get truncated with "..." in Google's results, and kept every title/description inside Google's display limits.
- **Explicit `width`/`height` on every logo `<img>`** (site-wide, including the dynamically-injected footer logo) so the browser reserves the correct space before the image loads — prevents layout shift, which is a Core Web Vitals ranking factor.
- **`areaServed: "Bangladesh"` added to the Organization schema** on every page, reinforcing the local/national service-area signal for Google and AI systems.
- Re-validated all JSON-LD (still parses cleanly) and re-checked HTML tag balance across all 8 pages after these edits.

## Next steps to actually rank on Google — what I can't do for you

Everything above is "technical SEO": making the site itself correct, fast and
machine-readable. That's necessary but it is **not** what makes a brand-new
site start ranking. Ranking is driven mostly by things outside the code,
which need your Google account, your phone number, or real third parties —
I can't create these for you, but here they are in priority order:

1. **Google Search Console (do this first, today)**
   - Go to [search.google.com/search-console](https://search.google.com/search-console), add your site (the Vercel URL, or your custom domain once you have one).
   - Verify ownership (Google gives you an HTML tag or DNS record — if it's an HTML meta tag, send it to me and I'll add it in one line).
   - Submit `sitemap.xml` from the Sitemaps section. This is what gets your pages into Google's index in days instead of weeks.

2. **Google Business Profile (critical for a Dhaka-based agency)**
   - Create a free listing at [business.google.com](https://business.google.com) with your exact business name, Dhaka address, phone, and website.
   - This is what makes you show up in Google Maps and the local 3-pack for searches like "digital agency near me" or "web design Dhaka" — it often matters more than on-site SEO for local service businesses.
   - Requires phone or postcard verification only you can complete.

3. **Backlinks** — the strongest ranking factor overall. Realistic sources for a Bangladeshi agency:
   - Get listed in Bangladeshi business/startup directories and local chambers of commerce.
   - Ask your 9 portfolio clients (Ramani Collection, NextGen Properties, etc.) to add a small "Designed by Abrar IT" credit link in their site footer — this is a very natural, legitimate first batch of backlinks since the relationship is real.
   - Guest posts or interviews on Bangladeshi tech/business blogs.
   - List on Clutch, GoodFirms, DesignRush (B2B agency directories — commonly checked by AI answer engines too).

4. **Real content, published regularly** — the Blog page currently has sample/placeholder posts. Publishing genuine articles (via the CMS once it's deployed) on topics your clients actually search — "website design cost in Bangladesh", "how to choose a digital marketing agency" — is what AI Overviews and ChatGPT actually cite, and what ranks long-term. Thin or fake-looking content doesn't help either.

5. **Reviews** — ask happy clients for real Google reviews on your Business Profile. Review count/rating is a direct local-ranking factor, and (once real) I can then add genuine `AggregateRating` schema, which I deliberately skipped in round 1.

6. **Custom domain** — a `.com` or `.com.bd` you own outright signals more trust than a `vercel.app` subdomain, both to users and to search engines. Tell me once you have one and I'll repoint every canonical/OG/sitemap URL.

None of this is code I can write — items 1, 2, 3 and 5 specifically need your own Google account, phone number, and real relationships, which is exactly why they're the next step rather than something already done.
