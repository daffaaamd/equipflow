<div align="center">

# 🚜 EquipFlow

**Heavy Equipment Rental & Fleet Management Platform**

A modern, full-featured web platform for managing equipment rental companies — from the public catalog and quote requests to fleet monitoring, contracts, invoicing, and analytics.

**Live Demo → [equipflow-chi.vercel.app](https://equipflow-chi.vercel.app)**

<br/>

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-v4-38B2AC?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3-77C1D8?style=for-the-badge&logo=alpine.js&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-8-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![Vercel](https://img.shields.io/badge/Deployed%20on-Vercel-000000?style=for-the-badge&logo=vercel&logoColor=white)

</div>

<p align="center">
    <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1400&q=80"
         alt="EquipFlow — heavy equipment at a construction site" width="100%">
</p>

---

## ✨ Highlights

- **Public website** — landing page, equipment catalog with live filters (category, brand, location, capacity, status), detailed equipment pages with gallery & specs, and a request-a-quote flow.
- **Role-based portals** — separate dashboards for `admin`, `sales`, `operations`, `maintenance`, `finance`, and `customer` with permission-gated routes.
- **Full rental workflow** — rental requests → quotations → contracts → deliveries → invoices → payments, all tracked end-to-end.
- **Fleet management** — equipment CRUD, status tracking (available / rented / maintenance), operators with certifications, and maintenance history.
- **Analytics & reports** — revenue trends, rental activity, equipment utilization, customer growth, finance dashboards with Chart.js visualizations, plus exportable reports.
- **Operations** — live fleet monitoring, project tracking, delivery scheduling, notifications, and a full audit log.
- **Responsive design** — mobile-friendly UI built with Tailwind CSS and Alpine.js.

---

## 👥 Demo Accounts

All demo accounts use password **`password`**.

| Role        | Email                     | Access                         |
|-------------|---------------------------|--------------------------------|
| Admin       | `admin@equipflow.com`     | Full access to everything      |
| Sales       | `sales@equipflow.com`     | Quotes, contracts, customers   |
| Operations  | `operations@equipflow.com`| Fleet, deliveries, monitoring  |
| Maintenance | `maintenance@equipflow.com`| Service records & scheduling   |
| Finance     | `finance@equipflow.com`   | Invoices, payments, finance    |
| Customer    | `customer@equipflow.com`  | Customer portal                |

---

## 🛠 Tech Stack

| Layer      | Technology                                                        |
|------------|-------------------------------------------------------------------|
| Backend    | PHP 8.3, Laravel 13                                                |
| Frontend   | Tailwind CSS v4, Alpine.js 3, Chart.js                             |
| Build tool | Vite + Laravel Vite Plugin                                         |
| Database   | MySQL (local) · SQLite (Vercel serverless)                         |
| Hosting    | Vercel (`vercel-php` runtime) + GitHub                             |

---

## 🚀 Getting Started

### Prerequisites

- PHP ≥ 8.3
- Composer
- Node.js ≥ 20.19
- MySQL (or SQLite for a zero-setup start)

### Local development

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Configure database in .env (defaults to SQLite below)
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite

# 4. Run migrations & seed demo data
php artisan migrate --seed

# 5. Build frontend assets
npm run build

# 6. Start the dev server
php artisan dev
```

Then open `http://127.0.0.1:8000` (or the URL printed by the dev server).

> 💡 Demo accounts are created by `UserSeeder` — see the table above.

---

## ☁️ Deploying to Vercel

This project ships with everything needed to deploy on Vercel's free tier:

1. Push this repository to GitHub.
2. Import the repository in [Vercel](https://vercel.com/new) (framework preset: **Other**).
3. Build command: `npm run build` · Output directory: `dist`
4. Vercel uses the `vercel-php` runtime via [`api/index.php`](api/index.php) and serves the pre-seeded SQLite database automatically.
5. Set your production `APP_KEY` in the Vercel project environment variables.

Key files:

```
vercel.json     → serverless config, env vars, catch-all route to api/index.php
api/index.php   → PHP bridge for the serverless runtime (tmp dirs, sqlite, Laravel)
build-dist.js   → builds Vite assets & prepares the dist/ output
.vercelignore   → keeps build/cache artifacts out of the deployment
```

> The analytics queries are written **driver-agnostic** (`DATE_FORMAT` on MySQL / `strftime` on SQLite) so the app runs identically in both environments.

---

## 📁 Project Structure

```
app/
├── Http/Controllers/     → Public, Auth, and admin/customer portal controllers
├── Models/               → Eloquent models (Equipment, Contract, Invoice, Payment, …)
└── Services/
    └── AnalyticsService.php  → driver-agnostic analytics & trend computation
resources/
└── views/
    ├── components/       → Layouts, badges, cards, empty states
    └── pages/            → landing pages, auth, and dashboard views
routes/web.php            → public, portal, and management route groups
database/seeders/         → demo data (users, fleet, customers, projects, rentals)
```

---

## 🗺 Roadmap Ideas

- [ ] Email notifications & PDF export for invoices/quotations
- [ ] Realtime fleet tracking integration
- [ ] Multi-company / multi-branch support
- [ ] API endpoints with Laravel Sanctum

---

## 📄 License

This is a portfolio project built with [Laravel](https://laravel.com) (MIT license).

<div align="center">

**Built with ❤️ by Daffa Ahmad Baihaqi**

</div>
