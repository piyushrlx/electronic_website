# TASK: Upgrade the existing Electronic Store into "TechFusion"

**Project path:** `D:\XAMP\htdocs\electronic_store`
**Tools available:** Google Stitch (connected) for UI/UX design, and you (Antigravity) for implementation.

## Goal
Transform the existing sample electronics website into a professional, production-ready e-commerce website named **TechFusion**.

- Keep PHP as the backend technology and keep all essential business logic.
- Preserve existing working functionality and database integrity. Never drop or destroy existing data.
- Deliver a completely new UI, modern theme colors, a premium visual identity, smooth live animations, responsive layouts, robust security, and complete e-commerce functionality.
- The final deployment strategy must support the intended **Vercel** hosting target.

## Working rules (strict)
1. Work in the phases below, in order. Do not skip phases or merge them.
2. **At the end of every phase, stop, give me a short report, and wait for my explicit approval** before starting the next phase.
3. Do not make unrelated changes or add features that are not in the plan.
4. Before changing any file in a phase, create a backup or git commit so every phase can be rolled back.
5. Never hardcode secrets. Use environment variables.
6. Do not modify the database schema destructively. Use additive, reversible migration scripts only, and show me each migration before running it.
7. If something is ambiguous or risky, ask me instead of guessing.

## START NOW: Phase 0 only (read-only assessment)
Do **not** modify any file in this step. Analyze the project and produce `docs/TECHFUSION_ASSESSMENT.md` containing:

1. **Structure map:** folder layout, entry points, routing, includes/config files, PHP version required, dependencies.
2. **Database inventory:** all tables, columns, relations, and which hold users, products, orders, payments, and carts. Note any missing indexes or foreign keys.
3. **Feature inventory:** what works today (catalog, search, cart, checkout, auth, orders, admin) and what is broken, incomplete, or duplicated.
4. **Security audit:** SQL injection, XSS, CSRF, password hashing, session handling, file upload handling, access control on admin pages, hardcoded credentials, error exposure. Rate each finding High, Medium, or Low with file and line references.
5. **UI audit:** pages and templates to be redesigned, shared layout pieces, assets used.
6. **"Do not break" list:** the business logic and data that must be preserved exactly.
7. **Vercel deployment analysis:** Vercel has no official PHP runtime. Compare these options for this specific codebase and recommend one:
   - A: Vercel with a community PHP runtime (`vercel-php`) as serverless functions
   - B: Vercel-hosted frontend with the PHP API on a separate PHP host
   - C: Docker deployment on a PHP-capable host
   For each option state what must change: external MySQL hosting, object storage for uploads, sessions, cron/emails, and environment variables.
8. **Risks and open questions** for me to decide.

Then present the full phased plan below, adjusted to what you found, with an effort estimate per phase.

**After presenting the assessment and the adjusted plan, STOP and wait for my approval.**

## Phased plan (to be executed only after approval, one phase at a time)

**Phase 1: Foundation and safety**
Git init and full backup of files and database. Move configuration and secrets to environment variables. Set up a local dev setup and a staging copy of the database with a safe migration process.

**Phase 2: Design system (with Google Stitch)**
Define the TechFusion brand: logo direction, color palette (modern, premium, tech-oriented, with dark and light support), typography, spacing, and component rules. Generate screens in Stitch for: home, catalog, product detail, cart, checkout, login/register, account, order tracking, and admin. Output a design-system file and screen designs. **I approve the designs before any code changes.**

**Phase 3: Frontend rebuild**
Replace the old templates with the approved design. Build reusable layout components and rebuild all customer pages. Add smooth live animations: scroll reveals, hover and micro-interactions, page transitions, skeleton loaders, animated cart updates. Respect `prefers-reduced-motion`. Make every page fully responsive (mobile first) and accessible (semantic HTML, contrast, keyboard navigation, alt text).

**Phase 4: Backend hardening**
Fix every issue from the security audit: prepared statements everywhere, CSRF tokens, output escaping, `password_hash`/`password_verify` with a safe rehash-on-login path for old hashes, secure session cookies, rate limiting on login, strict file upload validation, server-side validation, proper error handling and logging, and admin access control. Keep the existing business logic and schema compatible.

**Phase 5: E-commerce completeness**
Fill gaps only where missing: search and filters and sorting, wishlist, product reviews and ratings, coupons, shipping and tax rules, stock handling, order status flow with email notifications, invoices, and a payment gateway in test mode (Razorpay preferred; ask me to confirm). Use idempotent order creation and verify payment signatures server-side.

**Phase 6: Admin panel**
Restyle and complete product, category, order, customer, inventory, and coupon management. Add role-based permissions and a dashboard with sales and stock overview.

**Phase 7: Performance and SEO**
Image optimization and lazy loading, asset minification, caching headers, meta tags, Open Graph, sitemap, robots.txt, and structured product data.

**Phase 8: Testing**
End-to-end checks of browse, cart, checkout, payment, order, and admin flows. Security re-test. Cross-browser and cross-device testing. Basic load check. Provide a written test report.

**Phase 9: Deployment (Vercel)**
Implement the approved Vercel strategy, including hosted MySQL, object storage, environment variables, domain, SSL, and monitoring. Provide a rollback plan, a deployment guide, and a handover document.

## Definition of done
- The site is branded TechFusion everywhere (titles, emails, invoices, admin).
- All previously working features still work and existing data is intact.
- No High or Medium security findings remain open.
- The site passes responsive and accessibility checks and deploys successfully to the approved Vercel setup.

**Begin with Phase 0 now. Do not modify any files. Stop after the assessment and plan and wait for my approval.**
