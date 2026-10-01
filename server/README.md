# Hazleton Pumps — editable website (Node.js + admin panel)

A small Node/Express app that serves the bilingual (EN/RU) Hazleton Pumps
site and includes a password-protected admin panel at `/admin` for editing
everything: page text (both languages), images, case studies, press
articles, contact details, and a "quote request" leads inbox.

All content lives in `data/content.json` — a single JSON file acting as the
database. There is no external database to set up.

## Running locally

```bash
cd server
npm install
npm start
```

Then open:
- Public site: http://localhost:3000/
- Admin panel: http://localhost:3000/admin/login

## Admin login

Credentials are stored (bcrypt-hashed) in `data/admin.json`. The initial
account was created for you — **change the password immediately** from
*Настройки и контакты → Логин и пароль администратора* after your first
login, since the starting password was shared in chat and is not private
going forward.

## What the admin panel can do

- **Страницы** — edit every page's title and content blocks (hero text,
  paragraphs, bullet lists, spec panels, product/feature cards, image
  galleries, logos, awards...) in English and Russian side by side. You can
  add or remove blocks on any page.
- **Примеры проектов** (Case Studies) and **Пресса и статьи** (Press
  Releases) — full create / edit / delete for these, each one becomes its
  own page automatically at `/case-studies/<slug>/` or
  `/press-releases/<slug>/`.
- **Настройки и контакты** — company phone/email/address, footer text, the
  repeating "let us help you configure a solution" call-to-action, and the
  admin login itself.
- **Заявки** (Leads) — every submission of the "Request a quote" form on
  `/contact/` is stored here (name, email, phone, company, message). There
  is no email/SMTP integration configured, so submissions are *not*
  emailed anywhere automatically — check this inbox, or ask to have email
  notifications wired up once you have SMTP credentials (e.g. a Gmail
  app password, SendGrid, etc.) to provide.
- Any image field has a file picker — uploads are stored under
  `public/uploads/` and immediately used on the live page after saving.

## Deploying

This is a plain Node.js app — it runs anywhere Node runs: a VPS, Render,
Railway, Fly.io, etc. There is no required external service.

1. Push/copy this `server/` folder to your host.
2. `npm install --production`
3. Set environment variables (optional but recommended):
   - `PORT` — defaults to 3000.
   - `SESSION_SECRET` — any long random string. If omitted, one is
     generated per process restart, which logs everyone out on redeploy.
4. `npm start` (or let your host's process manager run `node app.js`).
5. Put it behind HTTPS (your host's load balancer, or a reverse proxy like
   Caddy/Nginx with Let's Encrypt) — admin login should never run over
   plain HTTP in production.

### Persisting data across deploys

`data/content.json`, `data/admin.json` and `public/uploads/` are the only
things that change at runtime. Make sure your hosting setup keeps these
across deploys/restarts:
- A VPS: nothing special needed, it's just local disk.
- A platform with ephemeral filesystems (e.g. most serverless/container
  platforms without a persistent volume): attach a persistent volume/disk
  for the `server/data` and `server/public/uploads` directories, or this
  data will reset on every redeploy.

## Regenerating content from scratch

`data/seed.js` is the original migration script that produced
`data/content.json` from the translated copy. Re-running it
(`node data/seed.js`) overwrites `content.json` completely — only use it if
you want to discard all admin edits and start over from the original
translated content.
