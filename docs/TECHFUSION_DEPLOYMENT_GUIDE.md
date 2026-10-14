# TechFusion: Production Deployment & Handover Guide (Vercel Target)

**Project Name:** TechFusion  
**Repository:** [github.com/piyushrlx/electronic_website](https://github.com/piyushrlx/electronic_website) (Branch: `main`)  
**Deployment Target:** Vercel (using Serverless PHP runtime `vercel-php`)  
**Database:** Hosted MySQL / MariaDB (e.g. Aiven, PlanetScale, Railway, AWS RDS)  

---

## 1. Prerequisites Checklist

Before triggering a production deployment on Vercel:

1. **Hosted Cloud MySQL Instance:**
   - Create a free or production MySQL service on [Aiven.io](https://aiven.io), [Railway.app](https://railway.app), or [PlanetScale](https://planetscale.com).
   - Ensure the server allows incoming connections from serverless IP ranges (`0.0.0.0/0` with strong password and SSL enabled).
   - Note down: Host, Port, Database Name, Username, Password, and SSL CA cert.

2. **GitHub Repository Sync:**
   - The repository [piyushrlx/electronic_website](https://github.com/piyushrlx/electronic_website) is fully synced with all code, assets, and migrations on `main`.

---

## 2. Step-by-Step Production Deployment

### Step 2.1: Seed Production Database
Connect to your remote cloud database and import the baseline schema and seed data from the repository backup:
```bash
mysql -h <HOST> -P <PORT> -u <USER> -p <DB_NAME> < docs/backups/electronic_store_backup_initial.sql
```
Or execute the migration runner targeting the cloud database:
```bash
php bin/migrate.php
```

### Step 2.2: Import Repository into Vercel
1. Log in to your [Vercel Dashboard](https://vercel.com).
2. Click **Add New...** -> **Project**.
3. Select and import the repository `piyushrlx/electronic_website`.
4. Framework Preset: Leave as **Other**.
5. Root Directory: `./` (leave default).

### Step 2.3: Configure Production Environment Variables in Vercel
In the Vercel project settings, navigate to **Environment Variables** and add the following keys for **Production** and **Preview**:

| Variable Key | Example Production Value | Notes |
| :--- | :--- | :--- |
| `APP_NAME` | `TechFusion` | Brand title |
| `APP_ENV` | `production` | Enables production error suppression |
| `APP_DEBUG` | `false` | Disables raw stack traces |
| `APP_URL` | `https://your-domain.vercel.app` | Production root URL |
| `DB_HOST` | `mysql-xxxx.aivencloud.com` | Cloud MySQL host |
| `DB_PORT` | `3306` | Cloud MySQL port |
| `DB_DATABASE` | `electronic_store` | Target database name |
| `DB_USERNAME` | `avnadmin` | Database user |
| `DB_PASSWORD` | `<STRONG_SECRET_PASSWORD>` | Database password |
| `DB_SSL_MODE` | `required` | Enforce TLS connection |
| `SESSION_DRIVER` | `database` | Persistent serverless sessions |
| `RAZORPAY_KEY_ID` | `rzp_live_...` or test key | Payment gateway |
| `RAZORPAY_KEY_SECRET`| `<RAZORPAY_SECRET>` | Payment HMAC verification |
| `MAIL_DRIVER` | `log` (or `smtp`) | Email notifications |

### Step 2.4: Deploy
Click **Deploy**. Vercel will install the `vercel-php` runtime, package the static assets (`public/assets/`), and expose both the dynamic pages and API endpoints.

---

## 3. Rollback & Disaster Recovery Plan

If any critical issue arises in production:

1. **Instant Vercel Instant Rollback:**
   - In the Vercel Dashboard, go to **Deployments**.
   - Locate the previous stable deployment commit (e.g. `15f9463`).
   - Click the three dots `...` and select **Rollback to this Deployment**. Vercel will instantly switch 100% of traffic in < 1 second.
2. **Database Reversion:**
   - If a database schema modification needs rollback, use the backup file:
     `docs/backups/electronic_store_backup_initial.sql`.
   - The migration system in `src/Database/MigrationRunner.php` is strictly additive to prevent table drops.

---

## 4. Handover & Maintenance Checklist

- **Admin Access:**
  - Login URL: `https://your-domain.vercel.app/login.php`
  - Default administrative accounts: `manager@techfusion.com` and `dev@techfusion.com`.
  - Passwords can be safely rotated from the staff management interface.
- **Order Fulfillment:**
  - Track customer shipments directly at `/track-order.php`.
  - Update orders in real-time from `/admin/orders.php`.
- **Audit Logs:**
  - All admin logins, stock updates, and order status transitions are recorded immutably in `/admin/audit.php`.
