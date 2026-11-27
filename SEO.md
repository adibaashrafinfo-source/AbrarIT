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

## What you should do next

1. **Submit `sitemap.xml` to Google Search Console and Bing Webmaster Tools** once you have a custom domain (or even now, for the Vercel URL) — this is the only way to see AI/Search performance data; there's no separate "AI Overview" report, Google uses the same Search Console data for both.
2. **Get a custom domain** and tell me — I'll update every canonical/OG/sitemap URL in one pass.
3. **Add real social profile links** in the footer when you have them, so I can wire up `sameAs` schema.
4. **Keep content fresh** — update the Blog regularly once real posts exist; dated, recently-updated content is weighted more heavily by both Google and AI answer engines.
