# Galleta Café – Ordering & Management System (PHP + MySQL + Tailwind)

## Install (XAMPP)
1. Copy the `galleta-cafe` folder to `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in XAMPP.
3. Open http://localhost/phpmyadmin → **Import** → choose `database/pink_cafe_db.sql` → Go.
4. Visit http://localhost/galleta-cafe/ (internet needed for the Tailwind and Chart.js CDNs).
5. If your folder name or DB password differs, edit `config/app.php`.

## Default logins (password for all: `password`)
| Role | Email |
|---|---|
| Admin | admin@galleta.cafe |
| Server | server@galleta.cafe |
| Customer | customer@galleta.cafe |

Change these passwords before going live.

## Replacing menu items, prices, photos
Admin → **Products**: edit name, price, description, availability, featured/best-seller flags, and upload a JPG/PNG/WEBP (max 2 MB). Items without a photo show a pink fallback tile. Old orders keep their original prices.
Cafe name, tagline, tax/service rates and payment methods are rows in the `cafe_settings` table (edit in phpMyAdmin). The logo is `assets/images/logo/logo.jpg`.
Guests can order without logging in and receive a short order number such as `ALEX-4821`, based on their serving nickname. Signed-in customers can track orders from **My Orders**.

## Structure
`config/app.php` (DB, helpers, CSRF, roles) · `includes/layout.php` (shared UI) · root = customer pages · `server/board.php` · `admin/*`.
