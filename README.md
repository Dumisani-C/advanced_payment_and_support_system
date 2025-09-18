# Advanced Payment & Support System (ATPSS)

A PHP-based system for tenant payments and support requests (admin + tenant areas).

## Features
- Tenant payments, receipts, and invoices
- Admin dashboard & audit notes
- Basic email notifications (PHPMailer)
- Exportable reports

## Quick Start (XAMPP)
1. Copy this folder into `C:\xampp\htdocs\advanced_payment_and_support_system`.
2. Start **Apache** and **MySQL** in XAMPP.
3. Open **phpMyAdmin** → create database: `atpss_db`.
4. Import `database/ark_apartment.sql`.
5. Visit: `http://localhost/advanced_payment_and_support_system/`

## Configuration
- `include/config.php` has DB connection details.
- Host: http://localhost/phpmyadmin

## Tech Stack
- PHP 7+ (procedural), MySQL, PHPMailer, Bootstrap, DataTables

## Folder Structure
- `admin/` – admin UI
- `tenant/` – tenant UI
- `database/` – SQL dump(s)
- `include/` – shared config & helpers
- `images/`, `css/`, `webfonts/` – assets


