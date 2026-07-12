# Job Portal Web Application

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-38%20Passing-success?style=for-the-badge)](tests)

</div>

A fully functional Job Portal Web Application built using Laravel 12, PHP 8, MySQL, Bootstrap 4, and AdminLTE 3.
This project is designed with professional architecture, role-based access control, security best practices, and modern UI.

---

## Project Overview

**JobConnect** is a portfolio job portal application built with Laravel 12, built to demonstrate
real-world full-stack development practices rather than as a live commercial product.
It supports three distinct user roles — **SuperAdmin**, **Admin (Company)**, 
and **Job Seeker** — each with their own dashboard, features, and access controls.

Built as a portfolio project demonstrating real-world Laravel development practices 
including REST APIs, queued notifications, scheduled commands, feature testing, 
and professional code architecture.

It includes:

- Job Posting & Management
- Resume Upload & Job Application
- Application Status Tracking
- Admin Dashboard with Analytics
- Role-Based Authentication & Authorization
- Secure Access Control using Middleware & Policies

Built specifically as a placement-ready full-stack Laravel project.

## Role System

-- Admin

- Manage Job Categories
- Manage Job Roles
- Post Jobs
- View Applications
- Update Application Status (Pending / Shortlisted / Hired / Rejected)
- Interview Scheduling | Schedule/reschedule/cancel interviews |
- Dashboard Analytics (Charts & Metrics)
- Notification System | Real-time notifications for new applications 
- Candidate Matching | Skill-based match percentage for applicants |

--Super Admin

- Admin Management | Create, suspend, reactivate, delete admins |
- Platform Overview | Total jobs, users, admins stats |
- Access Control | Full platform control |
  
-- User

- Register / Login
- Browse Jobs
- Apply for Jobs
- Upload Resume
- Track Application Status
- View Only Own Applications (Security Protected)
- Security Features
- Middleware-based role protection
- Policy-based authorization
- Job Alerts | Get email when matching jobs are posted |
- Users cannot access others' applications
- Protected Admin Routes
- Interview Tracking | View scheduled interviews with countdown 
- Job Search | Search & filter by location, salary, type, experience |
- Secure File Upload Handling
- Application History | Track all applications and their status

--Rest Api

- Token Auth | Laravel Sanctum Bearer tokens 
- Jobs API | List, filter, search, save jobs 
- Applications API | Apply, track applications 
- Profile API | View and update profile 
  
## Core Features

- User Registration & Login
- Resume Upload
- Job Application System
- Status Update System
- Search & Filter
- Pagination
- Category Management
- Responsive UI Design
- Admin Dashboard with Analytics
- Notification System
- Role-Based Access Control

## Dashboard Features

-- Admin Dashboard --
- Total Job
- Active Jobs
- Total Applications
- Application Status Chart
- Pending / Shortlisted / Hired / Rejected Analytics

-- User Dashboard -- 

- Applied Jobs
- Application Status Overview
- Profile Strength Indicator
- Quick Actions Panel

---

###  Authentication & Security
- Role-based Access Control (Admin, User)
- Secure Login System
- Protected Routes
- Environment-based configuration
- 
---

### Backend

- Framework: Laravel 12.x
- Language: PHP 8.2+
- Database: MySQL 8.0
- Authentication: Laravel Guards + Sanctum (API)
- Authorization: Laravel Policies
- Mail: SMTP (Mailtrap for development)
- Queue: Laravel Queue (Database driver)
- Testing: PHPUnit + Laravel Feature Tests
  
---
### Frontend

- CSS Framework: Bootstrap 5
- Admin Panel: Laravel AdminLTE
- Charts: Chart.js 4.x
- Icons: Font Awesome


##  Tech Stack
- **Backend:** Laravel (PHP Framework)
- **Frontend:** Blade Templates, HTML, CSS, Bootstrap
- **Database:** MySQL
- **Authentication:** Laravel Auth
- **Server:** XAMPP / Apache

### DevOps & Tools

- Version Control: Git + GitHub
- Local Server: XAMPP (Apache + MySQL)
- IDE: VS Code
- API Testing: Postman
- Code Style: Laravel Pint (PSR-12)


 ## Project Structure
 
app/                → Application Logic  
resources/views/    → Blade Templates (Frontend)  
routes/web.php      → Application Routes  
public/             → Public Assets  
storage/            → Logs & Temporary Files  


## Project Architecture Highlights

- Clean MVC Structure
- Proper Route Grouping
- Role-Based Middleware
- Policy Authorization
- Service-Oriented Logic Separation
- Reusable Blade Components
- Pagination Optimized Queries
- Secure Resume Storage System


##  Requirements

- PHP >= 8.2
- Composer
- MySQL 8.0
- Apache / XAMPP (or nginx)
- Node.js (only needed if you plan to rebuild frontend assets — the app currently ships pre-built AdminLTE assets and does not use a Vite build step)
- PHP extensions: pdo_mysql, gd, fileinfo, mbstring

   ---

## Installation Guide

1️ Clone the Repository
```bash
git clone https://github.com/abdul-samad820/job-portal.git
cd job-portal
```

2️ Install Dependencies
```bash
composer install
```

3️ Create Environment File
```bash
cp .env.example .env
php artisan key:generate
```
> ⚠️ **Do this before anything else.** Laravel will not boot with a blank `APP_KEY` — every encrypted session, cookie, and password-reset link depends on it.

For **local development only**, also edit `.env` and set:
```
APP_ENV=local
APP_DEBUG=true
SESSION_SECURE_COOKIE=false
```
(`.env.example` defaults to production-safe values — see the note at the top of that file.)

Step 4 — Configure `.env`

Update database credentials inside .env:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=job_portal
DB_USERNAME=root
DB_PASSWORD=
```

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS="noreply@jobportal.com"
MAIL_FROM_NAME="JobConnect"
```

5️ Run Migrations, then Seed
```bash
php artisan migrate
php artisan db:seed
```
> Order matters — seeding before migrating will fail because the tables don't exist yet.

6️ Create Storage Link
```bash
php artisan storage:link
```
> Without this, every uploaded resume/profile/category image returns a 404 — Laravel serves `storage/app/public/*` through the `public/storage` symlink, which isn't (and can't be) committed to git.

7️ (Production only) Cache config and routes
```bash
php artisan config:cache
php artisan route:cache
```

8️ Start the Application

```bash
# Web server (local dev)
php artisan serve

# Queue worker — required for status-change and job-alert emails to send.
# In production, run this under Supervisor/systemd so it restarts if it
# crashes — it should NOT be a one-off terminal command.
php artisan queue:work

# Scheduler — required for expired-job cleanup, job-alert emails, and
# admin notifications. In production, add ONE crontab entry (not
# `schedule:work`, which is a dev-only convenience command):
#   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
php artisan schedule:work
```

Visit: **http://localhost:8000**

## 🔑 Default Login Credentials

> ⚠️ Change these immediately in production!

| Role | Email | Password |
|------|-------|----------|
| SuperAdmin | superadmin@jobhub.com | SuperAdmin@123 |
| Admin | hr@techcorp.com | Admin@123 |
| Admin | careers@financehub.com | Admin@123 |
| Admin | jobs@designstudio.com | Admin@123 |
| User | samad@jobhub.com | User@123 |

> Default passwords can be overridden via `SEED_SUPERADMIN_PASSWORD`, `SEED_ADMIN_PASSWORD`, and `SEED_USER_PASSWORD` in `.env` before running `db:seed`.

### Scheduled Commands

# Manual run
php artisan jobs:send-alerts        # Job alert emails
php artisan app:delete-expired-jobs # Delete expired jobs
php artisan app:prune-old-notifications # Delete read notifications older than 30 days

## Deployment

### CI
Every push/PR to `main` runs the full test suite against a real MySQL service via GitHub Actions (`.github/workflows/ci.yml`) — a broken commit fails CI before it can reach production.

### Manual production checklist
- [ ] `.env` has `APP_ENV=production`, `APP_DEBUG=false`, real `APP_KEY`
- [ ] `SESSION_SECURE_COOKIE=true` (requires HTTPS) and `SESSION_DOMAIN` set to your real domain
- [ ] Real SMTP credentials (not the Mailtrap sandbox defaults)
- [ ] `php artisan storage:link` run — without it every uploaded file 404s
- [ ] `php artisan config:cache && php artisan route:cache` run after every deploy
- [ ] A **queue worker** running persistently under Supervisor/systemd (`php artisan queue:work`) — without it, status/job-alert emails silently pile up unsent
- [ ] The **scheduler** wired into crontab: `* * * * * php artisan schedule:run` — without it, expired jobs are never cleaned up and alert emails never send
- [ ] HTTPS/SSL terminated in front of the app

## API Documentation
-- http://localhost:8000/api/v1

## Authentication
All protected routes require Bearer token:

##  Testing

-- Run All Tests
```bash
php artisan test
```

-- Run Specific Test File
```bash
php artisan test tests/Feature/UserAuthTest.php
```

-- Run with Coverage
```bash
php artisan test --coverage
```

-- Test Suites
 - Tests: 38 passed (7 feature test files covering auth, jobs, applications, superadmin, API, and security regressions)
## Screenshots

<img width="1901" height="6560" alt="127 0 0 1_8000_" src="https://github.com/user-attachments/assets/6138ff24-1719-446d-9edc-db4474a83189" />
<br><br>
<img width="1907" height="877" alt="Admin_dashboard" src="https://github.com/user-attachments/assets/b3d9d1f6-893e-40df-9185-fbcf111b93d2" />
<br><br>
<img width="1905" height="873" alt="User_dashboard" src="https://github.com/user-attachments/assets/10de1c3f-50e2-4d03-9170-d301f62fd2ea" />
<br><br>

<img width="1907" height="876" alt="Admin_dashboard_feature" src="https://github.com/user-attachments/assets/b482aec0-c2b2-47c5-b6b4-394c434cadcc" />

<br><br>

<img width="1905" height="873" alt="User_dashboard_feature" src="https://github.com/user-attachments/assets/d663da93-e0db-4c3d-a6b8-94ed99913896" />

<br><br>
<img width="2480" height="4520" alt="127 0 0 1_8000_user_job (1)" src="https://github.com/user-attachments/assets/5629f3dd-4745-4a90-b2f0-0e90d163695c" />
<br><br>
<img width="2480" height="3976" alt="127 0 0 1_8000_user_single-job_12" src="https://github.com/user-attachments/assets/e298e4df-1889-4c2f-b1c9-63fa7fe4ab5a" />
<br><br>
<img width="2480" height="3072" alt="127 0 0 1_8000_user_form_apply_12" src="https://github.com/user-attachments/assets/ea0ee003-92db-4e07-9cf0-d35c04939f09" />




## Future Improvements

Deployment on Cloud Platform

## Developer
Abdul Samad
BCA Final Year Student | Laravel Developer

GitHub: https://github.com/abdul-samad820

<div align="center">

**⭐ If you found this project helpful, consider giving it a star on GitHub!**

*Built with ❤️ using Laravel*

</div>
