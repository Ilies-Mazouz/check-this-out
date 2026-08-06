# Check This Out

Een community-platform rond entertainment: films, series, anime en games — een mix van Letterboxd, MyAnimeList en Backloggd. Blader door een catalogus die uit echte API's komt, hou bij wat je aan het kijken of spelen bent, schrijf reviews, en volg het nieuws.

Gemaakt als examenproject voor het vak Laravel aan de Erasmushogeschool Brussel.

---

## Aan de slag

Je hebt PHP ^8.3, Composer, Node/npm en SQLite-ondersteuning in PHP nodig.

```bash
git clone https://github.com/Ilies-Mazouz/check-this-out.git
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

Start in een tweede terminal `npm run dev`, en ga naar `http://localhost:8000`.

Er zijn geen API-keys nodig om het project te draaien of te beoordelen — de catalogus (55 titels, met echte posters en synopsissen) is één keer vastgelegd tijdens de ontwikkeling en zit gewoon in de repo. `migrate:fresh --seed` geeft je dus meteen een volledig werkende, gevulde site, zelfs met een lege `.env`.

### Standaard admin

| | |
|---|---|
| Username | `admin` |
| Email | `admin@ehb.be` |
| Wachtwoord | `Password!321` |

Dit account kan niet gedegradeerd worden, ook niet door zichzelf of een andere admin — de site is zo gebouwd dat er altijd minstens één admin overblijft.

### Optioneel: live API-keys

Alles werkt ook zonder deze. Ze zijn enkel nodig als je *live* zoeken/importeren van nieuwe titels wil testen (gebruikt door de admin catalogus-importer en de zoekfunctie bij titel-inzendingen):

```env
# TMDB (films & series) — gratis key: https://www.themoviedb.org/settings/api
TMDB_API_KEY=

# IGDB (games) — gratis, maar je authenticeert via Twitch, niet via IGDB zelf.
# Maak een app aan op https://dev.twitch.tv/console/apps
TWITCH_CLIENT_ID=
TWITCH_CLIENT_SECRET=

# AniList (anime) is publiek en heeft geen key nodig.
```

Mail staat standaard op `MAIL_MAILER=log`, dus berichten van het contactformulier komen in `storage/logs/laravel.log` terecht in plaats van echt verstuurd te worden — geen mailserver nodig om het te testen.

---

## Wat erin zit

**Verplicht door de opdracht:**
- Login systeem — registreren, in-/uitloggen, "onthoud mij", wachtwoord vergeten/resetten. Admins kunnen gebruikers promoveren/degraderen en manueel accounts aanmaken.
- Publieke, door de eigenaar bewerkbare profielpagina's — username, verjaardag, avatar, bio, allemaal optioneel.
- Nieuws — CRUD voor admins, publieke lijst + detailpagina's, met titel/afbeelding/inhoud/datum.
- FAQ — gegroepeerd per categorie, beheerd door admins, publiek zichtbaar.
- Contactformulier dat een mail naar de admin stuurt.

**Extra features bovenop:**
- Een live catalogus uit TMDB, AniList en IGDB, met een admin-importer (trending/nieuw, genre-filters, een echte voortgangsbalk met cancel-knop)
- Community title-inzendingen, met goedkeuring door admin en een notificatie in beide gevallen
- Watchlist en gaming-lijst bijhouden
- Threaded reviews met een optionele score van 1 tot 5 sterren, likes, en notificaties bij reacties
- Favorieten, gebruikers blokkeren, notificatie-voorkeuren
- Automatische nieuws-import via RSS-feeds
- Een volledig admin-paneel: nieuws, FAQ, titels, gebruikers, contactberichten, live dashboard-statistieken
- Twee kleurthema's, elk met een lichte en donkere modus

## Checklist requirements

| Requirement | Status |
|---|---|
| Login (registreren, in-/uitloggen, onthoud mij, wachtwoord reset) | ✅ |
| Gewone vs. admin-accounts, enkel admin kan promoveren/degraderen & manueel aanmaken | ✅ |
| Publieke + door eigenaar bewerkbare profielpagina | ✅ |
| Nieuws CRUD (admin) + publieke lijst/detail | ✅ |
| FAQ gegroepeerd per categorie, admin CRUD | ✅ |
| Contactformulier + mail naar admin | ✅ |
| 2+ layouts, componenten, XSS/CSRF-bescherming, client-side validatie | ✅ |
| Alle routes via controllers, juiste middleware, gegroepeerd | ✅ |
| Eloquent models met one-to-many en many-to-many relaties | ✅ |
| `migrate:fresh --seed` draait probleemloos met echte seed-data | ✅ |
| Standaard admin komt exact overeen met de opdracht | ✅ |
| `vendor/` en `node_modules/` in .gitignore | ✅ |

## Tech stack

Laravel 13, Blade + Laravel Breeze voor authenticatie, Tailwind + Vite, een beetje Alpine.js voor interactieve stukjes (sterren-rating, dropdowns, import-voortgang), en SQLite. Catalogus-data komt van TMDB, AniList en IGDB; nieuws van publieke RSS-feeds.

## Nog even goed om te weten

- **Reviews en comments zijn hetzelfde model.** Een review is een zelfverwijzende rij: de root-review (één per gebruiker per titel) kan een sterren-score dragen; reacties daarop zijn gewoon threaded comments, zonder score.
- **Het admin-account kan zichzelf niet buitenspel zetten.** Er zit een verborgen vlag op die door geen enkele route of formulier uitgezet kan worden, ook niet door een andere admin. Niet gevraagd door de opdracht, gewoon een extra veiligheidsnet.
- **Seeden roept nooit een live API aan.** De catalogus is een vastgelegde snapshot, precies zodat beoordelen werkt zonder dat er ook maar één API-key ingesteld staat.
- **Afbeeldingen worden automatisch verkleind** bij binnenkomst (zowel uploads als downloads), zodat de site snel blijft.

## Bronnen

- Catalogus: [TMDB](https://www.themoviedb.org/) en [AniList](https://anilist.co/). Gebruikt de TMDB API maar is niet goedgekeurd of gecertificeerd door TMDB — hun logo en verplichte vermelding staan in de footer, en elke TMDB/AniList-titel linkt terug naar de bron.
- Games: [IGDB](https://www.igdb.com/), geauthenticeerd via Twitch's OAuth (toegang tot IGDB loopt via een Twitch developer-app, niet via IGDB zelf).
- Nieuws: publieke RSS-feeds (IGN, Anime News Network, Variety) — elk geïmporteerd artikel linkt terug naar de originele bron.
- Laravel, Breeze, Tailwind en Alpine.js zijn gebruikt volgens hun standaard documentatie. Er is geen tutorial-code gekopieerd — de import/dedup-logica van de catalogus, het threading-model van reviews, het themasysteem en de import-UI zijn specifiek voor dit project geschreven.

## Met dank aan

- De Laravel-documentatie, voor duidelijke uitleg bij het framework.
- Claude Code (Anthropic) — gebruikt voor delen van de ontwikkeling en uitwerking.
