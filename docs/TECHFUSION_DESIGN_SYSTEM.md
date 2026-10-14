# TechFusion Design System: "Cyber-Luxury 3D Grid"

**Version:** 1.0.0  
**Source:** Aligned with Google Stitch Project `projects/14467088421638620394`  
**Target:** TechFusion Cyber-Luxury Electronics Store & Marketplace  

---

## 1. Brand Identity & Visual Theme

The TechFusion brand embodies **high-precision, next-generation spatial computing and luxury electronics**. The visual direction blends aerospace telemetry, sleek glassmorphism, and laser-sharp cyan emissives over a deep obsidian foundation.

- **Brand Logo:** A stylized isometric hexagonal fusion core enclosing an energetic electronic wave, rendered in vivid gradient `#00F0FF` -> `#66FCF1`.
- **Primary Emotional Tone:** High-tech, hyper-clean, trustworthy, high-performance, and futuristic.

---

## 2. Color Palette & Semantic Tokens

### 2.1 Dark Foundation (Default)
| Token Name | Value | Purpose |
| :--- | :--- | :--- |
| `--tf-bg-base` | `#050811` | Pure obsidian background canvas |
| `--tf-surface` | `#0F131D` | Primary surface tier |
| `--tf-surface-container` | `#1C1F2A` | Card containers and elevated panels |
| `--tf-surface-glass` | `rgba(11, 15, 25, 0.70)` | Frosted glassmorphism panels (backdrop blur 20px) |
| `--tf-surface-glass-card` | `rgba(16, 24, 40, 0.75)` | Product cards, cart drawers, telemetry modules |
| `--tf-primary` | `#00F0FF` | Primary electric cyan accent & active state |
| `--tf-primary-hover` | `#66FCF1` | Bright hover luminescence |
| `--tf-primary-glow` | `rgba(0, 240, 255, 0.35)` | Exterior glow aura on interactive triggers |
| `--tf-secondary` | `#45A29E` | Industrial teal, secondary indicators |
| `--tf-text-primary` | `#F0F6FC` | Crisp, high-contrast readable white-slate |
| `--tf-text-muted` | `#8B9BB4` | Specifications, labels, timestamp slate |
| `--tf-border-glass` | `rgba(102, 252, 241, 0.15)` | Subtle technical hairline border |
| `--tf-border-glow` | `rgba(0, 240, 255, 0.50)` | Hover and focus state glow border |
| `--tf-success` | `#00E676` | Stock available, payment confirmed |
| `--tf-warning` | `#FFD600` | Low stock warning, pending status |
| `--tf-error` | `#FF1744` | Out of stock, error alerts, payment failure |

### 2.2 Light Mode Support (`[data-theme="light"]`)
| Token Name | Value | Purpose |
| :--- | :--- | :--- |
| `--tf-bg-base` | `#F4F7FC` | Clean technical cool-grey background |
| `--tf-surface` | `#FFFFFF` | Solid white surface |
| `--tf-surface-glass` | `rgba(255, 255, 255, 0.85)` | High-clarity light glass with subtle blur |
| `--tf-primary` | `#008B99` | Deepened cyan for high light-mode contrast |
| `--tf-text-primary` | `#0A0E17` | Deep obsidian black text |
| `--tf-text-muted` | `#5A6678` | Readable slate grey |

---

## 3. Typography Hierarchy

Imported from Google Fonts:
- **`Space Grotesk`** (Weights: 500, 600, 700): Hero display, page titles, technical spec badges, stock metrics.
- **`Sora`** (Weights: 400, 500, 600): Body copy, product descriptions, input values, tabular data.

| Scale Token | Font | Size | Weight | Line Height | Letter Spacing |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `display-hero` | Space Grotesk | 48px / 3rem | 700 | 1.15 | -0.03em |
| `headline-lg` | Space Grotesk | 32px / 2rem | 700 | 1.25 | -0.02em |
| `headline-md` | Space Grotesk | 24px / 1.5rem | 600 | 1.30 | -0.01em |
| `headline-sm` | Space Grotesk | 18px / 1.125rem | 600 | 1.40 | 0.00em |
| `body-lg` | Sora | 16px / 1rem | 400 | 1.60 | 0.01em |
| `body-md` | Sora | 14px / 0.875rem | 400 | 1.50 | 0.01em |
| `body-sm` | Sora | 12px / 0.75rem | 400 | 1.40 | 0.01em |
| `label-badge` | Space Grotesk | 11px / 0.6875rem | 700 (Uppercase)| 1.20 | 0.12em |

---

## 4. Component Rules

1. **Cyber Button (`.btn-cyber`):**
   - Solid electric cyan fill (`#00F0FF`), obsidian text (`#050811`), bold `Space Grotesk`.
   - Subtle outer glow `box-shadow: 0 0 20px rgba(0, 240, 255, 0.35)`.
   - Hover escalates brightness with a micro scale transform `scale(1.02)`.
2. **Ghost Glass Button (`.btn-ghost-glass`):**
   - Translucent background `rgba(11, 15, 25, 0.6)`, hairline border `1px solid rgba(102, 252, 241, 0.3)`, text `#66FCF1`.
3. **Product Showcase Card (`.tf-card`):**
   - Glassmorphic card `rgba(16, 24, 40, 0.75)`, 16px border radius, backdrop blur `24px`.
   - Top-right live telemetry badge (e.g. `4K OLED • 15 IN STOCK`).
   - On hover: Z-axis elevation (`translateY(-4px)`), border illuminates to glowing cyan.
4. **Cyber Inputs (`.tf-input`):**
   - Dark core `rgba(5, 8, 17, 0.85)` with `1px solid rgba(69, 162, 158, 0.3)`.
   - Focused state transitions smoothly to `border-color: #00F0FF` with a cyan focus ring `box-shadow: 0 0 12px rgba(0, 240, 255, 0.3)`.
5. **Interactive Telemetry Badges (`.tf-badge`):**
   - Compact rounded container with uppercase `Space Grotesk` tracking. Glowing cyan or mint circular pip.

---

## 5. Animation Directives & Accessibility

- **Scroll Reveals:** Smooth staggered reveal (`opacity: 0 -> 1`, `translateY(20px -> 0)`).
- **Cart Update Micro-Animation:** Pulse wave on cart counter badge upon product addition.
- **Hardware Acceleration:** All transforms utilize `will-change: transform` and `translate3d()`.
- **Accessibility:** Full compliance with `@media (prefers-reduced-motion: reduce)` — disables extraneous transitions and transforms for motion-sensitive users.
