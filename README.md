# ⚡ TechFusion: Next-Generation Electronics Marketplace

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-00758F?style=flat-square&logo=mysql&logoColor=white)](https://mariadb.org)
[![Design System](https://img.shields.io/badge/Design%20System-Google%20Stitch-00F0FF?style=flat-square)](https://stitch.withgoogle.com)
[![Deployment](https://img.shields.io/badge/Deployment-Vercel%20Serverless-000000?style=flat-square&logo=vercel&logoColor=white)](https://vercel.com)
[![Build Status](https://img.shields.io/badge/Tests-19%20Passed%20%2F%200%20Failed-00E676?style=flat-square)]()

**TechFusion** is a cyber-luxury electronics e-commerce platform and marketplace engineered for high-performance hardware, flagship OLED displays, smart temperature control systems, and flagship 5G smartphones.

Built with a hardened PHP backend, zero data-loss database architecture, Google Stitch "Cyber-Luxury 3D Grid" visual identity, and full compatibility for serverless hosting on **Vercel**.

---

## 🌟 Key Highlights & Features

- **Cyber-Luxury Visual Identity:** Powered by Google Stitch design tokens with deep obsidian foundations (`#050811`), glowing electric cyan accents (`#00F0FF`), frosted glassmorphic card overlays, and fluid micro-interactions.
- **Normalized Hardware Specifications:** Deep technical specification matrices for TVs (`tv_specs`), Refrigerators (`fridge_specs`), and Mobiles (`mobile_specs`).
- **Dynamic Slide-Out Cart Drawer:** Real-time quantity updates, subtotal calculations, and stock availability validation.
- **Atomic Order Fulfillment:** Transactional order placement with strict row locking (`FOR UPDATE`) to prevent inventory overselling.
- **Live Shipment Telemetry:** Real-time visual progress stepper (Placed → Processing → Shipped → Delivered) via unique tracking IDs (`TF-2026-XXXX`).
- **Executive Control Center (Admin Panel):** Gross sales telemetry, real-time inventory adjustments, order status transitions, and immutable security audit logs.
- **Hardened Security Architecture:** 100% prepared PDO statements, timing-attack safe CSRF protection, Bcrypt hashing with auto-rehash, and rate-limited auth endpoints.
- **Vercel Serverless Ready:** Pre-configured `vercel.json` and front-controller dispatching compatible with `vercel-php`.

---

## 🏗️ Architecture & Project Map

```
electronic_website/
├── api/                       # Serverless API endpoints (Cart, Products, Admin)
├── bin/                       # CLI tools (migrate.php)
├── config/                    # Configuration (bootstrap.php, database.php)
├── docs/                      # Architectural audits, design systems & guides
│   ├── TECHFUSION_ASSESSMENT.md
│   ├── TECHFUSION_DESIGN_SYSTEM.md
│   ├── TECHFUSION_TEST_REPORT.md
│   ├── TECHFUSION_DEPLOYMENT_GUIDE.md
│   ├── backups/               # Initial database snapshots
│   └── migrations/            # Versioned SQL migrations
├── public/                    # Web root (Index, Product, Cart, Checkout, Admin)
│   ├── assets/                # Core CSS tokens, components, animations & JS
│   ├── admin/                 # Control center portal (Overview, Stock, Orders, Audit)
│   ├── robots.txt             # Search engine rules
│   └── sitemap.xml            # Search indexing sitemap
├── src/                       # PSR-4 Architecture (TechFusion\)
│   ├── Auth/                  # Admin authentication & RBAC
│   ├── Database/              # Migration runner & connection pooling
│   ├── Models/                # Product, Order, Admin models
│   └── Services/              # Cart, Payment & Email notification services
├── templates/                 # Reusable layout partials (header, footer, cart drawer)
├── tests/                     # Automated E2E verification test suite
├── vercel.json                # Vercel serverless routing configuration
└── techfusion_antigravity_prompt.md
```

---

## 🚀 Quickstart & Local Setup

### 1. Requirements
- **PHP 8.0+** with `pdo_mysql`, `mbstring`, `openssl`, `json` extensions.
- **MySQL / MariaDB** (Running locally on XAMPP or remote host).

### 2. Database Initialization
```bash
# 1. Clone repository
git clone https://github.com/piyushrlx/electronic_website.git
cd electronic_website

# 2. Configure Environment
cp .env.example .env

# 3. Apply Reversible Database Migrations
php bin/migrate.php
```

### 3. Run Automated Tests
```bash
php tests/run_tests.php
```

### 4. Serve Locally
```bash
# Via PHP Built-in Server
php -S localhost:8000 -t public

# Or access via XAMPP Apache:
# http://localhost/electronic_website/public/
```

---

## 🛡️ Default Administrative Credentials

Access the Control Center at `/login.php` or `/admin/`:
- **Role `admin`:** Username: `manager` | Email: `manager@techfusion.com`
- **Role `super_admin`:** Username: `dev_lead` | Email: `dev@techfusion.com`

---

## ☁️ Deployment (Vercel)

Follow the complete instructions in [docs/TECHFUSION_DEPLOYMENT_GUIDE.md](file:///d:/XAMP/htdocs/electronic_website/docs/TECHFUSION_DEPLOYMENT_GUIDE.md) to link this GitHub repository directly to Vercel with a managed cloud database.

---

## 📄 License
Proprietary & Confidential. All rights reserved by TechFusion Systems Inc.
