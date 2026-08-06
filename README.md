# Check This Out

An entertainment community platform for movies, series, anime and games — a mix of Letterboxd, MyAnimeList and Backloggd. Browse a catalogue pulled from real APIs, track what you're watching or playing, leave reviews, and keep up with the news.

Built as the exam project for the Laravel course at Erasmushogeschool Brussel.

---

## Getting it running

You'll need PHP ^8.3, Composer, Node/npm, and SQLite support in PHP.

```bash
git clone <repo-url>
cd check-this-out

composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate:fresh --seed

php artisan storage:link
php artisan serve
```

In a second terminal run `npm run dev`, then open `http://localhost:8000`.

No API keys are needed to run or grade this — the catalogue (55 titles, real posters/synopses) was snapshotted once during development and ships with the repo, so `migrate:fresh --seed` gives you a fully working, populated site straight away, even with a completely empty `.env`.

### Default admin

| | |
|---|---|
| Username | `admin` |
| Email | `admin@ehb.be` |
| Password | `Password!321` |

This account can't be demoted, by itself or by any other admin — the app is built to never end up without one.

### Optional: live API keys

Everything works without these. They only matter if you want to test *live* searching/importing of new titles (used by the admin catalogue importer and the title-submission search):

```env
# TMDB (movies & series) — free key: https://www.themoviedb.org/settings/api
TMDB_API_KEY=

# IGDB (games) — free, but you authenticate through Twitch, not IGDB itself.
# Create an app at https://dev.twitch.tv/console/apps
TWITCH_CLIENT_ID=
TWITCH_CLIENT_SECRET=

# AniList (anime) is public and needs no key.
```

Mail defaults to `MAIL_MAILER=log`, so contact-form messages land in `storage/logs/laravel.log` instead of actually being emailed — no mail server needed to test it.

---

## What's in it

**Required by the assignment:**
- Login system — register, log in/out, remember me, forgot/reset password. Admins can promote/demote users and create accounts manually.
- Public, editable profile pages — username, birthday, avatar, bio, all optional.
- News — admin CRUD, public list + detail pages, with title/image/content/date.
- FAQ — grouped by category, admin-managed, public.
- Contact form that emails the admin.

**Extra features on top:**
- A live catalogue pulled from TMDB, AniList and IGDB, with an admin importer (trending/newest, genre filters, a real progress bar with cancel)
- Community title submissions, with admin approval and a notification either way
- Watchlist and gaming-list tracking
- Threaded reviews with an optional 1–5 star rating, likes, and reply notifications
- Favourites, blocking other users, notification preferences
- Automated news import from RSS feeds
- A full admin panel: news, FAQ, titles, users, contact messages, live dashboard stats
- Two color themes, each with a light and dark mode

## Requirements checklist

| Requirement | Status |
|---|---|
| Login (register, log in/out, remember me, password reset) | ✅ |
| Regular vs. admin accounts, admin-only promote/demote & manual creation | ✅ |
| Public + owner-editable profile page | ✅ |
| News CRUD (admin) + public list/detail | ✅ |
| FAQ grouped by category, admin CRUD | ✅ |
| Contact form + email to admin | ✅ |
| 2+ layouts, components, XSS/CSRF protection, client-side validation | ✅ |
| All routes through controllers, correct middleware, grouped | ✅ |
| Eloquent models with one-to-many and many-to-many relations | ✅ |
| `migrate:fresh --seed` runs clean with real seed data | ✅ |
| Default admin matches the assignment exactly | ✅ |
| `vendor/` and `node_modules/` gitignored | ✅ |

## Tech stack

Laravel 13, Blade + Laravel Breeze for auth, Tailwind + Vite, a bit of Alpine.js for interactive pieces (star ratings, dropdowns, import progress), and SQLite. Catalogue data comes from TMDB, AniList and IGDB; news from public RSS feeds.

## A few things worth knowing

- **Reviews and comments are the same model.** A review is a self-referencing row: the root one (one per user per title) can carry a star rating; replies to it are just threaded comments, no rating attached.
- **The admin account can't be locked out of its own role.** It carries a hidden flag that no route or form can flip off, not even from another admin. Wasn't asked for by the assignment, just a safety net.
- **Seeding never calls a live API.** The catalogue is a bundled snapshot precisely so grading works with zero API keys configured.
- **Images get resized on the way in** (uploads and downloads alike) so the site stays fast.

## Sources

- Catalogue: [TMDB](https://www.themoviedb.org/) and [AniList](https://anilist.co/). Uses the TMDB API but isn't endorsed or certified by TMDB — their logo and required notice are in the footer, and every TMDB/AniList title links back to its source.
- Games: [IGDB](https://www.igdb.com/), authenticated through Twitch's OAuth (IGDB access is issued via a Twitch developer app, not IGDB directly).
- News: public RSS feeds (IGN, Anime News Network, Variety) — each imported article links back to where it came from.
- Laravel, Breeze, Tailwind and Alpine.js are used per their standard docs. No tutorial code was copy-pasted — the catalogue import/dedup logic, the review-threading model, the theme system and the import UI were written specifically for this project.
