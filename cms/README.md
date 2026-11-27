# Abrar IT — Admin Panel (CMS)

A small PHP + MySQL admin panel for managing the Abrar IT website's Blog
posts, Portfolio projects, Services & pricing, and Contact form
submissions. It is separate from the main website (which stays on
GitHub/Vercel) because those are static hosts that can't run PHP — this
folder is meant to be uploaded to your own cPanel-style web hosting
(Hostinger, Namecheap, etc.) that supports PHP and MySQL.

## 1. Upload

Upload the entire `cms/` folder to your hosting, e.g. to
`public_html/cms/` so it's reachable at `https://yourdomain.com/cms`.

## 2. Create the database

In cPanel → **MySQL Databases**: create a database and a database user,
add that user to the database with all privileges. Note the database
name, username, password and host (usually `localhost`).

Then in **phpMyAdmin**, select your new database and use the **Import**
tab to import `cms/schema.sql`. This creates all the tables and seeds
the 8 starting services so the Services page isn't empty on day one.

## 3. Configure

Copy `config.sample.php` to `config.php` (same folder) and fill in:

- `DB_DSN`, `DB_USER`, `DB_PASS` — your database details from step 2.
- `ALLOWED_ORIGINS` — the URL(s) of your live website (e.g. your Vercel
  URL and/or your GitHub Pages URL), comma-separated. This lets the
  website's JavaScript call the API from the browser.
- `APP_SECRET` — any long random string, used to help protect sessions.

**Never commit `config.php` to git** — it holds your real database
password. It's already listed in `.gitignore`.

## 4. Create your admin account

Visit `https://yourdomain.com/cms/setup.php` once. Fill in your name,
email and a password (8+ characters) to create the first admin account.

**Then delete `setup.php` from the server.** It refuses to run again
once an account exists, but removing the file is the safest option.

## 5. Log in

Visit `https://yourdomain.com/cms/login.php` and sign in. From the
dashboard you can manage:

- **Blog Posts** — title, category, excerpt, content, cover colors, draft/published.
- **Portfolio** — title, category, description, thumbnail label, cover colors, order, draft/published.
- **Services** — name, summary, starting price, order, draft/published.
- **Messages** — everything submitted through the website's Contact page.

Only items marked **Published** appear on the live website.

## 6. Connect the live website to this backend

Open `assets/js/cms-config.js` in the main website repository and set:

```js
window.CMS_API_BASE = "https://yourdomain.com/cms";
```

Commit and push/redeploy the website. Once this is set:

- The **Contact page** form submits directly into the Messages inbox here.
- The **Blog**, **Portfolio**, and **Services** pages automatically show
  whatever you've published here instead of their built-in sample
  content. If this isn't set, or the API can't be reached, those pages
  simply keep showing their sample content — nothing breaks.

## Notes & current limits

- The Services page's detailed sections (the big per-service write-ups
  with feature lists and anchors like `#graphic-design`) and the
  pricing packages further down stay static HTML — only the services
  overview grid at the top is admin-managed for now. Editing the full
  write-ups still means editing `services.html` directly.
- Blog posts don't yet have individual article pages — "Read Article"
  is a placeholder, same as in the original static site.
- The homepage's featured portfolio preview and service cards stay
  static; only the dedicated Blog/Portfolio/Services pages are wired
  to the admin content.
- Passwords are hashed (bcrypt via PHP's `password_hash`), all database
  queries use prepared statements, and every form is CSRF-protected.
  Still, use HTTPS on your hosting and keep your admin password private.
