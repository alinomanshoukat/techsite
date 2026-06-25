# TechWire — Tech Blog & Link-Building Service Template

A complete, self-hosted blog platform built for tech-niche publishers who monetize
through guest posts and link insertion services. Built with plain PHP, MySQL,
HTML, CSS and JavaScript — no framework, no build step, no dependencies to install.

---

## What's Included

**Public Site**
- Home page (featured post + latest articles + sidebar)
- Blog (paginated article archive)
- Single post page (with related articles, author bio, view counter)
- Category archive pages
- Live search (MySQL full-text)
- About Us (auto-pulls your team from the Users table)
- Contact page (saves messages to your database)
- Guest Post page (pricing table + submission form)
- Link Insertion page (pricing table + submission form)

**Admin Panel** (`/admin`)
- Role-based accounts: **Admin** (full control) and **Author** (write-only)
- Dashboard with stats
- Post editor with image upload, categories, and a simple rich-text toolbar
- Author posts require Admin approval before going live (editorial workflow)
- Category manager
- Leads inbox — every Guest Post pitch, Link Insertion request, and Contact
  message lands here automatically
- Author/user manager — create logins for writers, disable or delete accounts
- Site settings — change site name, tagline, prices, and turnaround time without
  touching code
- Personal profile page for any logged-in user (avatar, bio, password)

---

## Requirements

- PHP 7.4 or higher (PHP 8.x recommended)
- MySQL 5.7+ or MariaDB 10+
- Any standard host (shared hosting, VPS, or local via XAMPP/MAMP/Laragon)

---

## Installation (5 minutes)

1. **Upload the files** to your server (or your local `htdocs`/`www` folder).
2. **Create a database** in phpMyAdmin or your hosting control panel.
3. **Import the schema**: open `sql/install.sql` and import it into the database
   you just created. This also adds 6 demo articles, 5 categories, and 2 demo
   logins so the site looks complete on first load.
4. **Edit `includes/config.php`** with your database name, username, and password:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_db_username');
   define('DB_PASS', 'your_db_password');
   ```
5. **Make `uploads/` writable** (chmod 755 or 775 depending on your host).
6. Visit your site. Done.

If you installed into a subfolder (e.g. `yoursite.com/blog/`), set `SITE_URL` in
`includes/config.php` to `/blog` instead of leaving it blank.

---

## Demo Logins

| Role   | Email                   | Password    |
|--------|-------------------------|-------------|
| Admin  | admin@techwire.test     | Admin@123   |
| Author | author@techwire.test    | Author@123  |

**Change these immediately after installing** — go to **My Profile** in the
admin sidebar to update the password, or delete the demo accounts from
**Authors & Access** and create your own.

---

## How the Editorial Workflow Works

1. An **Author** logs in and writes a post. They can save it as a Draft or
   submit it for review — they cannot publish directly.
2. The **Admin** sees every pending post in **All Posts**, reviews it, and
   switches its status to Published when ready.
3. Published posts appear instantly on the homepage and blog — there's nothing
   to "refresh" or rebuild; the site always queries the latest data.
4. The **Admin** can also create more Author accounts at any time from
   **Authors & Access**, or promote someone to a second Admin.

## Featured Images

Upload any image when creating or editing a post — any size or aspect ratio is
fine. The site automatically crops every thumbnail to a consistent shape (using
CSS `object-fit: cover`), so your homepage and blog grid always look uniform
even if your photos come from different sources.

## Guest Posts & Link Insertion Leads

Every submission from the **Guest Post** and **Link Insertion** pages — and
every message from **Contact** — is saved straight to your database and shows
up in **Leads & Messages** in the admin panel. Update each lead's status
(New → Reviewing → Accepted/Rejected) to keep track of your sales pipeline.

---

## Customizing

- **Colors, fonts, spacing**: all in `assets/css/style.css`, defined as CSS
  variables at the top of the file (`--ink`, `--paper`, `--blue`, `--brick`, etc).
- **Site name, tagline, prices**: edit from **Site Settings** in the admin
  panel — no code editing needed.
- **Logo**: the site uses a text logo by default (`TechWire`). To use an image
  logo instead, edit the `.logo` block in `includes/header.php`.
- **Categories**: add, rename, or delete from **Categories** in the admin panel.

---

## Support Notes for Buyers

This template is plain PHP/MySQL with no external dependencies, so it will run
on virtually any standard PHP hosting. If you see a "Database connection
failed" message, double-check the four values in `includes/config.php` match
your actual hosting database credentials.

Enjoy, and good luck with your tech blog!
