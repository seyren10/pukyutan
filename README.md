# Puyo

**Your Paluwagan, finally organized.**

Puyo is a full-stack web app for running a *paluwagan* (also known as a ROSCA — Rotating Savings and Credit Association). It replaces the group chat and notebook tracking most paluwagan organizers rely on with a single place to manage members, contributions, payout rounds, and group activity — with support for co-managing a group with other trusted people.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
  - [Backend Setup](#backend-setup)
  - [Frontend Setup](#frontend-setup)
- [Environment Variables](#environment-variables)
- [Scheduled Tasks](#scheduled-tasks)
- [Testing](#testing)
- [Future Enhancements](#future-enhancements)

---

## Features

### Group & Cycle Management
- Create paluwagan groups with a contribution amount and a configurable frequency (daily, weekly, or monthly)
- Auto-generated, shareable invite codes for each group
- Draft → Active → Completed group lifecycle
- Automatic generation of payout rounds/cycles based on member count and frequency
- Disburse a cycle's payout to its recipient member, with amount tracking
- Reorder members (drag-and-drop) to control payout order

### Contributions
- Record and undo member contributions per cycle
- Per-member ledger with running balance and summary totals
- Downloadable, branded **PDF ledger export** per member (generated asynchronously and polled for status, rendered with a real headless Chrome instance for pixel-accurate output)

### Collaboration / Group Sharing
- Invite other users to co-manage a group via share requests
- Accept, reject, or revoke sharing access
- View groups shared with you separately from groups you own
- Per-group and per-user **activity feed** (powered by an audit log) so everyone can see who did what and when

### Notifications & Reminders
- In-app notifications for share requests, responses, and revocations
- Automated email reminders for members with an upcoming/due cycle payment (configurable lookahead window via an Artisan command)

### Accounts
- Email/password registration with email verification
- Google OAuth login (Socialite)
- Password reset flow
- Per-user profile with a customizable Dicebear-generated avatar (seed can be regenerated or cleared)

### Dashboard
- At-a-glance stats across a user's groups (active groups, totals, upcoming cycles, etc.)

---

## Tech Stack

### Backend
| | |
|---|---|
| **Framework** | [Laravel 13](https://laravel.com) (PHP 8.3+) |
| **Auth** | Laravel Sanctum (SPA token auth) + Laravel Socialite (Google OAuth) |
| **Activity/Audit Logging** | [spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog) |
| **PDF Generation** | [spatie/browsershot](https://github.com/spatie/browsershot) (headless Chrome) |
| **Email** | [Resend](https://resend.com) |
| **Queues** | Database-driven queue (background PDF generation, reminder emails) |
| **Testing** | [Pest](https://pestphp.com) |
| **Local Dev** | Laravel Sail (Docker) — MySQL 8.4, Redis, Mailpit |
| **Log Viewer** | [opcodesio/log-viewer](https://github.com/opcodesio/log-viewer) |

### Frontend
| | |
|---|---|
| **Framework** | [Vue 3](https://vuejs.org) + TypeScript |
| **Build Tool** | [Vite](https://vitejs.dev) |
| **Routing** | Vue Router |
| **State Management** | Pinia |
| **Server State / Data Fetching** | TanStack Query (Vue Query) + Axios |
| **UI Components** | [reka-ui](https://reka-ui.com) + [shadcn-vue](https://www.shadcn-vue.com) (New York style) |
| **Styling** | Tailwind CSS v4 |
| **Forms & Validation** | vee-validate + Zod |
| **Icons** | Lucide |
| **Avatars** | Dicebear |
| **Misc** | vue-draggable-plus (member reordering), vue-sonner (toasts), date-fns, VueUse |
| **Analytics/Deploy** | Vercel Analytics, deployed via Vercel |

---

## Project Structure

```
puyo/
├── backend/     # Laravel API (routes, models, services, jobs, migrations)
│   ├── app/
│   │   ├── Http/Controllers/V1/   # Versioned REST API controllers
│   │   ├── Models/                 # Group, Member, Cycle, Contribution, GroupShare, ...
│   │   ├── Services/                # Business logic (ledger calc, PDF export, cycle generation, ...)
│   │   ├── Jobs/                    # Queued jobs (e.g. PDF ledger generation)
│   │   ├── Notifications/           # In-app + email notifications
│   │   └── Console/Commands/        # Scheduled/Artisan commands (e.g. cycle reminders)
│   ├── routes/
│   │   ├── v1/api.php               # Versioned API routes
│   │   ├── api.php / auth.php       # Core auth + user routes
│   └── database/migrations/
└── frontend/    # Vue 3 SPA
    └── src/
        ├── pages/        # Route-level views (groups, activity, landing, profile, share-requests, ...)
        ├── components/   # Reusable UI + feature components
        ├── router/       # Route definitions, split by domain
        ├── stores/       # Pinia stores (auth/user bootstrap, etc.)
        └── services/     # Axios instance, TanStack Query client
```

---

## Getting Started

### Backend Setup

The backend uses **Laravel Sail** for local development.

```bash
cd backend
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

The API will be available at `http://localhost` (or the `APP_PORT` you configure).

> The `composer setup` script (in `composer.json`) also runs `key:generate`, `migrate`, and builds frontend assets in one go if you're not using Sail.

### Frontend Setup

```bash
cd frontend
npm install
npm run dev
```

By default the dev server runs on Vite (`vite --host 0.0.0.0`). Point `VITE_APP_NAME` and your API base URL (via `.env.development`) at your local backend.

---

## Environment Variables

Key variables to configure in `backend/.env`:

| Variable | Purpose |
|---|---|
| `DB_*` | MySQL connection (matches Sail's `compose.yaml` by default) |
| `RESEND_API_KEY` | Transactional email (verification, reminders, share notifications) |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` / `GOOGLE_REDIRECT` | Google OAuth login |
| `BROWSERSHOT_CHROME_PATH` | Path to a Chrome/Chromium binary for PDF ledger exports |
| `ADMIN_EMAIL` | Administrative contact/notification address |
| `QUEUE_CONNECTION` | Should be a real queue driver (e.g. `database`) since PDF export and reminders are queued jobs |

---

## Scheduled Tasks

A custom Artisan command sends reminder emails for cycles that are due soon:

```bash
php artisan app:send-reminder --days=3
```

Hook this into your scheduler (cron + `schedule:run`, or a hosted scheduler) to run daily.

---

## Testing

The backend uses **Pest**:

```bash
cd backend
./vendor/bin/sail test
# or, without Sail:
composer test
```

---

## Future Enhancements

- Extended member-lifecycle activity logging and causer display on the activity feed

---

## License

No license has been specified for this project yet.