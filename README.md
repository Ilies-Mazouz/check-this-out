# Check This Out

**Check This Out** is a dynamic, database-driven entertainment community platform for movies, series, anime and games — a combination of Letterboxd, MyAnimeList and Backloggd. Users can browse a catalogue pulled from live external APIs, submit their own titles, track what they're watching/playing, leave threaded reviews with an optional star rating, and follow the latest entertainment news.

Built as the exam project ("herexamen") for the Laravel course at Erasmushogeschool Brussel.

---

## Setup

**Requirements:** PHP ^8.3, Composer, Node.js/npm, and the PHP SQLite extension (`pdo_sqlite`).

```bash
# 1. Clone and enter the project
git clone <repo-url>
cd check-this-out

# 2. Install dependencies
composer install
npm install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Database (SQLite file must exist before migrating)
touch database/database.sqlite
php artisan migrate:fresh --seed

# 5. Public storage symlink (needed for uploaded avatars/cover images to be servable)
php artisan storage:link

# 6. Run it
php artisan serve
# in a second terminal:
npm run dev
```

Then open `http://localhost:8000`.

`php artisan migrate:fresh --seed` populates the database with everything needed to use and grade the site out of the box: the default admin, genres, platforms, an FAQ, a handful of news articles, and a 55-title catalogue (movies, series, anime, games) with real posters and synopses. **No API keys are required to seed or run the site** — the catalogue seed data was snapshotted from TMDB/AniList once during development and is bundled in `database/seeders/assets/`, precisely so a fresh clone with an empty `.env` still has a fully working, populated catalogue.

### Default admin account

| Field | Value |
|---|---|
| Username | `admin` |
| Email | `admin@ehb.be` |
| Password | `Password!321` |

This account is the **master admin** — its admin role can never be revoked (by itself or by any other admin), so the app can never be left without an admin. See [Notable implementation details](#notable-implementation-details).

### Optional: live external API keys

The site works fully without these — they only enable *live* search/import of new titles and news (used by the admin catalogue importer and the community title-submission search). Add them to `.env` if you want to test that:

```env
# TMDB (movies & series) — free key: https://www.themoviedb.org/settings/api
TMDB_API_KEY=

# IGDB (games) — free, but authenticates through Twitch, not IGDB itself.
# Create an app at https://dev.twitch.tv/console/apps to get these.
TWITCH_CLIENT_ID=
TWITCH_CLIENT_SECRET=

# AniList (anime) is a public GraphQL API and needs no key.
```

Mail is set to `MAIL_MAILER=log` by default, so contact-form emails to the admin are written to `storage/logs/laravel.log` instead of actually being sent — no mail server needed to test or grade the contact form.

---

## Features

### Minimum requirements (per the assignment)

- **Login system** — register, log in/out, "remember me", forgot/reset password. Every account is a regular user or an admin (`is_admin` boolean); only admins can promote/demote other users or manually create new accounts.
- **Profile pages** — every user has a public profile at `/u/{username}`, visible to guests. Username, birthday, avatar and bio are all optional and editable by the account owner.
- **News** — admins manage articles (title, cover image, content, publish date) from the admin panel; every visitor can browse the list and read a detail page.
- **FAQ** — grouped by category, managed by admins, visible to every visitor.
- **Contact form** — any visitor can send a message; the admin receives it by email.

### Extra features

- **Live catalogue** sourced from TMDB (movies/series), AniList (anime) and IGDB (games), with an admin-triggered importer (trending/newest, optional genre filter, balanced round-robin across types, chunked with a live progress bar and a real cancel button).
- **Community title submissions** — users can search TMDB/AniList/IGDB and submit a title for admin approval, or enter one manually; the submitter gets a notification either way.
- **Watchlist & Gaming list** — per-user status tracking (Want to Watch/Watching/Completed/Dropped for movies/series/anime; Backlog/Playing/Completed/Dropped/100% for games).
- **Reviews** — one root review per user per title with an optional 1–5 star rating, threaded replies up to 3 levels deep, likes, and reply notifications (toggleable per user). Blocking another user hides their reviews from you.
- **Favourites** — a separate "liked this title" list, distinct from ratings.
- **Automated news import** from public RSS feeds (IGN for games, Anime News Network, Variety film/TV), same chunked/cancellable admin UI as the catalogue importer, deduplicated by source URL.
- **Notifications** — in-app, with an unread-count bell, for submission approval/rejection and review replies.
- **Admin panel** — dashboard with live stats, and full management of news, FAQ, titles/submissions, users, and contact messages.
- **Two visual themes** (Neon Arcade, Ember), each with a dark and light mode, saved per account.

---

## Tech stack

- **Laravel 13** (PHP 8.3+), Blade templates
- **Laravel Breeze** for authentication scaffolding
- **Tailwind CSS + Vite**, Alpine.js for small interactive bits (star rating widget, dropdowns, live import progress)
- **SQLite** for the database — no separate DB server needed
- **TMDB**, **AniList**, **IGDB** (via Twitch OAuth) for catalogue data; public RSS feeds for automated news

---

## Notable implementation details

A few decisions worth knowing about when reading the code or grading the site:

- **Reviews and comments are one system.** A "review" is a self-referencing row (`parent_id`, `depth` 1–3): the root (depth 1, `parent_id` null) is the one-per-user-per-title entry with the optional star score; replies (depth 2–3) are ordinary threaded comments on it. There is no separate `comments` table.
- **Master admin.** The seeded `admin@ehb.be` account has an additional `is_master_admin` flag (not mass-assignable, set only by the seeder). `Admin\UserController::toggleAdmin()` refuses to revoke admin status from that account — no route or form can demote it, including from another admin — so the app can never end up without an admin. There's no feature to transfer that status to someone else; the assignment doesn't ask for it, so it wasn't built.
- **Seeding never calls a live API.** The catalogue seeder reads from a bundled JSON snapshot + local images (`database/seeders/assets/`) rather than hitting TMDB/AniList at seed time, specifically so `migrate:fresh --seed` succeeds with an empty `.env` — which is exactly how this project gets graded.
- **Scheduled imports** (`titles:import-scheduled`, `news:import-rss`) are registered in `routes/console.php` but only fire if something is actually running the scheduler (`php artisan schedule:work` or a real cron entry) — they're not required for grading, just a "keep it fresh while the site happens to be running" convenience.
- **Images are resized on the way in** (`App\Support\ImageResizer`, GD-based, capped ~800px/78% JPEG, avatars at 400px) for every upload or downloaded source image, to keep page weight reasonable.

---

## Sources & attributions

- Catalogue data: [TMDB](https://www.themoviedb.org/) (movies & series) and [AniList](https://anilist.co/) (anime). This product uses the TMDB API but is not endorsed, certified, or otherwise approved by TMDB — their logo and required notice are in the site footer per their Terms of Use. Every TMDB/AniList-sourced title page links back to the original entry.
- Game data: [IGDB](https://www.igdb.com/) (authenticated via Twitch's OAuth client-credentials flow, since IGDB access is issued through a Twitch developer app rather than IGDB directly).
- News is aggregated from public RSS feeds (IGN, Anime News Network, Variety) — each imported article links back to its original source and publisher.
- Laravel, Breeze, Tailwind CSS and Alpine.js are used per their standard public documentation; no tutorial code was copy-pasted — application logic (catalogue import/dedup, the reviews/threading model, the theme system, the chunked-import UI, etc.) was written for this project.

---

## Project structure notes

- Two Blade layouts: `resources/views/layouts/main.blade.php` (public site, top navbar) and `resources/views/layouts/admin.blade.php` (admin panel, left sidebar).
- Reusable Blade components live in `resources/views/components/` — notably `x-icon` (theme-colored inline SVG icon set, no emoji anywhere in the UI) and `x-cover-image` (renders an uploaded image or a themed placeholder icon + type badge when none exists).
- `App\Services` holds the external-integration and cross-cutting logic: `TmdbService`, `AniListService`, `IgdbService`, `TitleImportService`, `RssNewsService`, `ThemeService`.
