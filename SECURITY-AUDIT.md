# Abrar IT — Cybersecurity Audit

Date: 2026-11-28

Scope: the static website (all 23 HTML pages + JS), the PHP/MySQL CMS
(`/cms`), and hosting configuration (`vercel.json`, `.htaccess`,
`robots.txt`). This builds on an earlier `/code-review` pass (which
found and fixed 3 issues: a dead CMS blog-post link, a `javascript:`
XSS vector in the portfolio URL field, and duplicated JS) — this audit
goes wider, specifically for security rather than general code quality.

## Findings & fixes

| # | Area | Finding | Severity | Fix |
|---|---|---|---|---|
| 1 | CMS hosting config | `php_flag display_errors off` in `.htaccess` is invalid on PHP-FPM hosts (common on modern cPanel/CloudLinux) — causes a full **500 error for the entire app**, not a graceful degradation | High (availability) | Moved to `ini_set()` in `includes/bootstrap.php` and `api.php`, which works under every PHP SAPI |
| 2 | Static site | No HTTP security headers at all (no CSP, no clickjacking protection, no MIME-sniffing protection) | Medium | Added `vercel.json` with a tested Content-Security-Policy, `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Permissions-Policy`, and HSTS |
| 3 | CMS sessions | No `SameSite` cookie attribute set — weaker CSRF defense-in-depth | Medium | Set `session.cookie_samesite = Lax` in `bootstrap.php` |
| 4 | CMS auth | Logout didn't fully destroy the session or clear the cookie, just emptied `$_SESSION` | Low | `logout()` now expires the session cookie and calls `session_destroy()` |
| 5 | CMS auth | No throttling on repeated failed logins beyond a fixed 300ms delay | Low-Medium | Added an escalating delay after 5 failed attempts within 5 minutes (session-based; see limitation below) |
| 6 | Public API | Contact form had a honeypot but no rate limit — a script could flood the database with submissions | Low-Medium | Added a per-email throttle (max 3 submissions per 10 minutes), tested end-to-end |
| 7 | CMS hosting config | No `Options -Indexes` — a host with directory listing on by default could expose `/cms/includes/` or `/cms/assets/` file listings | Low | Added `Options -Indexes` to `.htaccess` |
| 8 | CMS hosting config | No baseline security headers on the CMS side | Low | Added `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` via `mod_headers` in `.htaccess` (guarded with `<IfModule>`) |
| 9 | Documentation accuracy | `config.sample.php` claimed `APP_SECRET` "signs session cookies" — it was never actually read by any code, which could give false confidence in a control that doesn't exist | Low | Corrected the comment to say it's reserved/unused today, and names what actually protects sessions |
| 10 | CSP readiness | `blog-post.html` had its fetch/render logic as an inline `<script>` block, which would have forced a weaker `'unsafe-inline'` CSP | — (enabler) | Extracted to `assets/js/blog-post.js` so `script-src 'self'` (no unsafe-inline) is possible |

## What was already solid (verified, not just assumed)

- **SQL injection**: every query in the CMS uses either a hardcoded literal string or a parameterized `PDO::prepare()` call with bound values. Grepped the entire `/cms` tree for string-concatenated SQL — none found. The one place a variable is interpolated into SQL text (`unique_slug()`'s table name) is only ever called with hardcoded literals from my own code, never user input.
- **CSRF**: every state-changing POST endpoint (blog/portfolio/services create-edit-delete, messages delete, login, setup) calls `csrf_check()` against a `random_bytes(32)` token. Verified 1:1 that every POST handler has a matching check and every form has a matching hidden field.
- **XSS**: all dynamic output in the CMS admin templates goes through `e()` (HTML-escaping). The one deliberate exception — blog post `content` rendered as raw HTML — is a documented design choice (like any CMS's WYSIWYG content) and is only ever written by an authenticated admin, not a visitor.
- **No open redirects**: every `redirect()` call site passes a hardcoded path, never a user-controlled value.
- **No LFI/RCE surface**: no `eval()`, `unserialize()`, `extract()`, variable variables, or dynamic `include`/`require` anywhere in the codebase.
- **Secrets hygiene**: `cms/config.php` has never been committed (confirmed via `git log`), `.gitignore` covers it, and `config.sample.php` only ever contained placeholder values.
- **Reverse tabnabbing**: every `target="_blank"` link across all 23 pages already had `rel="noopener noreferrer"`.
- **Password storage**: `password_hash()`/`password_verify()` (bcrypt), never plaintext or a weak hash.
- **Login enumeration**: the login page returns the same generic "Incorrect email or password" regardless of which part was wrong.
- **Admin-area indexing**: `/cms/` is disallowed in `robots.txt` and every admin page separately sends `noindex, nofollow` as defense-in-depth.

## How the new CSP was validated (not just written and hoped)

A real risk with Content-Security-Policy is that a wrong policy silently breaks the live site. Before shipping it:

1. Grepped every HTML file for every external resource actually used (Google Fonts, the Google Maps embed on the Contact page, the WhatsApp link) to build an accurate allowlist instead of guessing.
2. Served the actual site locally with the exact same headers `vercel.json` now sends.
3. Loaded 10 representative pages (home, contact with its map iframe, portfolio with its gradient cards, a service page, the blog index, an individual blog article, the dynamic `blog-post.html`, about, and the 404 page) in a real headless browser, watching the console for any `Refused to...` CSP violation — **zero violations**.
4. Visually confirmed (screenshots) that the Google Maps embed and the portfolio's gradient cards still render correctly under the policy.

**One explicit tradeoff, stated plainly rather than hidden**: `style-src` includes `'unsafe-inline'`. The site uses inline `style="background:linear-gradient(...)"` attributes extensively (every portfolio/blog/service card). Removing that would mean refactoring hundreds of inline styles into CSS classes — a large, separate effort with a comparatively low security payoff, since inline-style injection is a much narrower attack surface than inline-script injection (which the policy *does* block completely — `script-src 'self'`, no exceptions).

## Known limitations (stated honestly, not glossed over)

- **Login throttling is session-based**, not IP-based — an attacker who clears cookies resets the counter. A real fix needs either a database-backed attempt log or a server-level tool (Fail2ban, Cloudflare), which depends on what the eventual hosting provider offers. Worth revisiting once the CMS is actually deployed and you know your host's capabilities.
- **CSP is only applied to the static site** (via `vercel.json`) and the CMS's basic headers only (via `.htaccess`) — the CMS doesn't get the full CSP, since its admin templates weren't audited for inline-script elimination in this pass. Lower priority since the admin panel already requires authentication and is excluded from search indexing.
- **Contact-form rate limiting is per-email, not per-IP** — a spammer varying the email address isn't caught by it. It stops the simplest flood case; a more complete fix would need IP-based tracking, which PHP alone can do but is easy to spoof without additional infrastructure.
- **`.htaccess` only works on Apache/LiteSpeed** — if the eventual PHP hosting runs pure Nginx, none of the `.htaccess` protections (including the `.sql`/`.md`/`config.sample.php` blocking) apply. Most cPanel-style Bangladeshi shared hosting runs Apache or LiteSpeed (both honor `.htaccess`), so this is a reasonable bet, but worth confirming once a host is chosen.

## Validation performed

- All PHP files re-linted (`php -l`) after every change — zero syntax errors.
- All JS files re-checked (`node --check`) — zero syntax errors.
- Full-site re-validation: JSON-LD still parses on every page, HTML tag balance checked on all 23 pages, no broken internal links.
- The new contact-form rate limit was tested end-to-end against a local server: 3 submissions from the same email succeeded, the 4th was correctly rejected, and a different email was unaffected.
- The new CSP was tested as described above — zero violations across 10 page types, including the two pages with the most complex content (Google Maps iframe, dynamic CMS-fetched content).
