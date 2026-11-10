# EduAcademy

EduAcademy is a centralized, collaborative online learning platform built with **Laravel 13**, **Blade**, **Tailwind CSS**, and **Alpine.js**.

---

## 1. Prerequisites

Before installing the application, verify that your machine meets all requirements:

- **PHP 8.3, 8.4, or 8.5**
  - Required PHP extensions: `mbstring`, `openssl`, `pdo_mysql`, `fileinfo`, `ctype`, `json`, `tokenizer`, `xml`, `curl`.
  - > [!IMPORTANT]
  - > Always verify `php -v` in your command line / terminal (e.g., PowerShell or Git Bash), not only inside your web server control panel. XAMPP's bundled CLI PHP is often older than 8.3. Use Laragon, Laravel Herd, or update your system environment PATH to point to PHP ≥ 8.3.
- **Composer 2.x**
- **Node.js LTS (v20+ or v22+) & npm**
- **MySQL 8.0+ or MariaDB 10.6+**
- **Git**

---

## 2. Getting Started & Installation

Follow these steps for Laragon, Laravel Herd, or XAMPP:

### Step 1: Clone the repository
```bash
git clone <repository-url> eduacademy
cd eduacademy
```

### Step 2: Install PHP & Node dependencies
```bash
composer install
npm install
```

### Step 3: Configure Environment
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```
Generate an application key:
```bash
php artisan key:generate
```

### Step 4: Database Setup
1. Create a MySQL database named `eduacademy` with collation `utf8mb4_unicode_ci` (via phpMyAdmin, MySQL CLI, or GUI client).
2. Configure your DB credentials in `.env`:
   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=eduacademy
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Step 5: Run Migrations and Seeders
Execute migrations and populate the initial database with administrator and local demo accounts:
```bash
php artisan migrate --seed
```

### Step 6: Create Storage Symlink
Link the public storage directory:
```bash
php artisan storage:link
```

### Step 7: Build Assets and Run the Application
In your terminal, build assets or start the development server:
```bash
# Build frontend assets:
npm run build

# Start local server:
php artisan serve
```
If using Laravel Herd or Laragon virtual hosts, navigate directly to `http://eduacademy.test`.

---

## 3. Seeded Demo Accounts (Local Environment)

In local development (`APP_ENV=local`), all accounts are seeded with password: `password`

| Role | Account / State | Email | Password |
|------|-----------------|-------|----------|
| **Admin** | System Administrator | `admin@eduacademy.test` | `password` |
| **Instructor** | Active & Onboarded (with Profile & CV) | `instructor.active@eduacademy.test` | `password` |
| **Instructor** | Pending Approval | `instructor.pending@eduacademy.test` | `password` |
| **Instructor** | Rejected Registration | `instructor.rejected@eduacademy.test` | `password` |
| **Student** | Active & Onboarded | `student.active@eduacademy.test` | `password` |
| **Student** | Active & Unonboarded | `student.unonboarded@eduacademy.test` | `password` |
| **Student** | Suspended | `student.suspended@eduacademy.test` | `password` |

---

## 4. Running Tests

Run the full automated feature and unit test suites:
```bash
php artisan test
```
Or directly using PHPUnit:
```bash
php vendor/phpunit/phpunit/phpunit
```

---

## 5. Git Workflow & Commit Guidelines

- **Branches**:
  - `main`: Production-ready release branch.
  - `develop`: Integration branch.
  - `SCRUM-XX-pbi-YY-<topic>` or `feature/pbi-XX-<topic>`: Topic branches for sprint tasks.
- **Commit convention**:
  - `SCRUM-11 <TYPE>: <message>` (e.g., `SCRUM-11 FEAT: Implement user and role management (PBI-07)`)
  - Commit types: `FEAT`, `UP`, `FIX`, `REFACTOR`, `DOCS`, `TEST`, `CHORE`.

---

## 6. Architecture & Sprint 1 Scope

- **Framework**: Laravel 13 (MVC)
- **Frontend**: Blade Components + Tailwind CSS v4 + Alpine.js
- **PBI-07**: User registration & authentication, single-role backed enums (`admin`, `instructor`, `student`), account lifecycle statuses (`active`, `pending`, `suspended`, `rejected`), instructor approval workflows, temporary password generation with forced change, and role-based access control middleware.
