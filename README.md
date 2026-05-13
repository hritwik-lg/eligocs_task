# 🚀 Laravel Multi-Tenant Task Management System

This project is a **multi-tenant SaaS-style Laravel application** with separate **Admin panel** and **Tenant workspaces**.

---

## 🧩 Tech Stack

- Laravel 12
- PHP 8.2+
- PostgreSQL
- Blade UI
- Multi-tenant architecture (schema-based)
- XAMPP / Localhost setup

---

## 🏗️ System Architecture

### 1. Admin Panel (Super Admin)

Used to manage tenants.

🔗 URL: http://admin.localhost:8000/admin/login


### Features:
- Manage tenants
- Activate / deactivate tenants
- View system overview

---

### 2. Tenant Application

Each tenant runs on a **subdomain-based workspace**.

🔗 URL Pattern: http://{slug}.localhost:8000/

Example:

http://softtricks.localhost:8000/

http://acme.localhost:8000/


### Features:
- Task management (CRUD)
- Task filtering (status, priority)
- Tenant isolated database schema
- Dashboard per tenant

---

## ⚙️ Key Features

### 🧑 Multi-Tenant System
- Each tenant has isolated data using PostgreSQL schema (`search_path`)
- Middleware-based tenant identification

### 📌 Task Module
- Create tasks
- Update tasks
- Delete tasks
- Filter by status & priority
- Inline status update

---

## 🔐 Authentication

- Admin authentication for super admin panel
- Tenant authentication (optional depending on setup)

---

## 🛠️ Installation

### 1. Clone project

```bash
git clone <repo-url>
cd project-folder

2. Install dependencies
composer install
npm install && npm run dev

3. Environment setup
cp .env.example .env
php artisan key:generate

4. Setup database
Configure PostgreSQL in .env:

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=eligocs_task
DB_USERNAME=postgres
DB_PASSWORD=

Run migrations:
php artisan migrate

Run project
php artisan serve

🌐 Local Domain Setup (Important)

Add in your hosts file:

127.0.0.1 admin.localhost
127.0.0.1 softtricks.localhost
127.0.0.1 acme.localhost

📂 Project Structure Highlights
app/
 ├── Http/
 │    ├── Controllers/
 │    │     ├── Admin/
 │    │     └── Tenant/
 │    ├── Middleware/
 │    │     ├── IdentifyTenant.php
 │    │     └── TenantActive.php
 ├── Models/
 │    ├── Tenant.php
 │    └── Task.php
