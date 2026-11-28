# Google Search Console Setup — Step-by-Step (for Claude in Chrome)

This is written so you can **copy the block below and paste it directly
to Claude running as your Chrome extension**, while you're logged into
whichever Google account should own this property. It will drive the
browser through the exact clicks. There's one unavoidable pause in the
middle (explained below) where you'll need to come back to me (Claude
Code) with one piece of text.

## Why "URL prefix," not "Domain"

Google Search Console offers two property types. **Use "URL prefix,"
not "Domain."** The "Domain" option verifies ownership via a DNS
record — but your site is currently on `abrar-it.vercel.app`, a
subdomain you don't control the DNS for (Vercel does). "URL prefix"
verifies a specific URL instead, using a method we *can* complete:
an HTML meta tag on the page itself.

(Once you buy a custom domain, tell me — I'll update the site's URLs,
and you'd redo this setup with "Domain" property type, which is
slightly more powerful since it covers all subdomains and both
http/https automatically.)

---

## Paste this to Claude in Chrome

```
Open a new tab and go to https://search.google.com/search-console

1. If asked to sign in, use my Google account (I'll handle sign-in myself if needed).
2. Click "Add Property" (or "Start now" if this is the first property).
3. Two options will appear: "Domain" and "URL prefix". Choose "URL prefix" — the one on the RIGHT side, not Domain.
4. In the URL prefix text field, type exactly: https://abrar-it.vercel.app
   (must include https:// and have no trailing slash)
5. Click "Continue".
6. Google will show a verification page with several methods (HTML file, HTML tag, Google Analytics, Google Tag Manager, Domain name provider). Click to expand the "HTML tag" method (it's usually already open by default, labeled "Recommended" or shown first).
7. It will show a line that looks like:
   <meta name="google-site-verification" content="SOME_LONG_CODE_HERE" />
8. Copy that ENTIRE line exactly as shown — select all of it including the full content="..." value.
9. Show me (the user) that exact copied line in your response, clearly, so I can send it elsewhere. Do NOT click the "Verify" button yet — stop here and wait for me.
```

---

## ⏸ Pause here — come back to me (Claude Code)

Claude in Chrome will now show you one line that looks like:

```html
<meta name="google-site-verification" content="abc123xyz..." />
```

**Copy that exact line and paste it to me in this chat.** I'll add it
to the site's `index.html` and deploy it — takes about a minute. I'll
tell you once it's live.

---

## Resume — paste this to Claude in Chrome (after I confirm it's deployed)

```
Go back to the Google Search Console tab (or reopen https://search.google.com/search-console if closed).

1. Make sure you're on the verification page for https://abrar-it.vercel.app with the "HTML tag" method still selected.
2. Click the "Verify" button.
3. If it says "Ownership verified" — great, click "Go to property" or "Continue".
   If it fails, wait 30 seconds and click Verify again (the new page needs a moment to be live), and try up to 3 times before stopping and telling me it failed.
4. Once verified and inside the property dashboard, look at the left-hand sidebar and click "Sitemaps".
5. There will be a field labeled "Add a new sitemap". Type exactly: sitemap.xml
   (just that — not the full URL, Search Console adds the domain automatically)
6. Click "Submit".
7. Confirm it shows up in the table below with a status — it may say "Pending" or "Success". Tell me which one it shows.
8. Optional but useful: click the left sidebar "URL Inspection" tool, paste in https://abrar-it.vercel.app/ (the homepage), press Enter, and click "Request Indexing" if the option appears. This asks Google to crawl the homepage sooner rather than waiting. Repeat this for https://abrar-it.vercel.app/services.html and https://abrar-it.vercel.app/web-design-bangladesh.html if you have a few extra minutes — these are the most commercially important pages.
```

---

## What happens next

- **Sitemap status "Pending"** is normal at first — it can take anywhere
  from a few hours to a few days for Google to actually crawl and show
  "Success." No action needed, just check back.
- **"Coverage" / "Pages" report** (left sidebar) will slowly start
  showing how many of your pages are indexed — check this in about a
  week.
- **"Performance" report** is the one that matters most long-term — it
  shows the actual search queries people use to find your site, with
  real click and impression data. This usually takes 1-2 weeks to
  start populating. Once you have a few weeks of this data, send me a
  screenshot or export — I'll use the *real* query data to refine
  `SEO-KEYWORDS.md` instead of the educated guesses it's built on now.

## If something goes wrong

- **"Verification failed"** repeatedly: tell me, and I'll double check
  the meta tag is actually live on the deployed site (sometimes a
  deploy takes a minute to propagate).
- **Can't find "Add Property" button**: you may already have a
  property added from a previous attempt — check the property
  dropdown in the very top-left of the Search Console page first.
- **Signed into the wrong Google account**: click your profile picture
  top-right in Chrome/Google and switch accounts before starting.
