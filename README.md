# EduAcademy

EduAcademy is a centralized, collaborative online learning platform built with **Laravel 13**, **Blade**, **Tailwind CSS**, and **Alpine.js**.

---

## 1. Prerequisites

Before installing the application, verify that your machine meets all requirements:

- **PHP 8.3, 8.4, or 8.5**
  - Required PHP extensions: `mbstring`, `openssl`, `pdo_mysql`, `fileinfo`, `ctype`, `json`, `tokenizer`, `xml`, `curl`, plus `pdo_sqlite` (the test suite runs on in-memory SQLite).
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
- `SubjectSeeder` runs in **every** environment (Programming, Design, Mathematics, Science, Languages, Business, Other).
- `DemoUserSeeder` and `DemoCourseSeeder` only populate demo data when `APP_ENV=local`.
- To reset everything during development: `php artisan migrate:fresh --seed`.

### Step 6: Create Storage Symlink
Link the public storage directory:
```bash
php artisan storage:link
```
Class banners are uploaded to the **public** disk (`storage/app/public/course-banners`) with random filenames, so this symlink is required for banners to display. Sensitive files (e.g. instructor CVs) stay on the private disk.

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
| **Instructor** | Suspended (owns 1 published class, hidden from catalog) | `instructor.suspended@eduacademy.test` | `password` |
| **Student** | Active & Onboarded | `student.active@eduacademy.test` | `password` |
| **Student** | Active & Unonboarded | `student.unonboarded@eduacademy.test` | `password` |
| **Student** | Suspended | `student.suspended@eduacademy.test` | `password` |

### Seeded demo classes (Local Environment)

`instructor.active` owns 4 classes: 2 published, 1 draft, 1 archived. `student.active` is enrolled in one published class and the archived class. The suspended instructor's published class must **not** appear in the student catalog (see PBI-11 rule S2-11).

---

## 4. Running Tests

Run the full automated feature and unit test suites:
```bash
php artisan test
```
Tests use in-memory SQLite (see `phpunit.xml`), so they do not touch your MySQL data. To run only the class-management tests:
```bash
php artisan test --filter=Course
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

## 6. Architecture & Scope

- **Framework**: Laravel 13 (MVC)
- **Frontend**: Blade Components + Tailwind CSS v4 + Alpine.js
- **PBI-07**: User registration & authentication, single-role backed enums (`admin`, `instructor`, `student`), account lifecycle statuses (`active`, `pending`, `suspended`, `rejected`), instructor approval workflows, temporary password generation with forced change, and role-based access control middleware.

### Sprint 2: PBI-11 Master Classes / Courses

- **Instructors** create, edit, publish, archive and delete their own classes (title, description, subject, level, optional banner). Status flow: `draft → published → archived`, with `published → draft` only when no students are enrolled. Classes with enrolled students cannot be deleted (archive instead); deletion is a soft delete.
- **Students** browse a catalog of published classes (search, subject and level filters), open a class and enroll with one click (free, no un-enroll), then find it under **My Classes**.
- **Visibility rule**: a class is in the catalog only if it is `published` and its instructor is `active` and not deleted. Suspending an instructor hides their classes; existing enrollments and student access are kept, and reactivating restores visibility.
- **Authorization**: `CoursePolicy` (`manage` for instructors, `browse`/`enroll`/`access` for students). Other instructors' classes return 403; hidden classes return 404.
- **Class hub**: tabs are config-driven (`config/course_tabs.php`) and appear automatically once the named route exists. Wrap hub pages in `<x-instructor.course-layout>` / `<x-student.course-layout>`. Reserved routes for Sprint 3 (modules, assignments, sessions, quizzes, reviews) are listed in that file.
- **Shared `subjects` table** (seeded, no admin CRUD) is also meant for the discussion forum (PBI-14, use `subject_id`).
- New tables: `subjects`, `courses`, `enrollments`. Full details: `docs/EduAcademy_Sprint2_PBI-11_Spec.md`.