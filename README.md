<div align="center">

# Job Portal Web Application

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-260%2B%20Feature%20Tests-success?style=for-the-badge)](tests)

A fully functional Job Portal Web Application built using Laravel 12, PHP 8, MySQL, Bootstrap 4, and AdminLTE 3.
This project is designed with professional architecture, role-based access control, security best practices, SEO, GDPR compliance, and a modern UI.

</div>

---

## Project Overview

**JobConnect** is a production-ready job portal application built with Laravel 12.
It supports three distinct user roles — **SuperAdmin**, **Admin (Company)**,
and **Job Seeker** — each with their own dashboard, features, and access controls.

Built as a portfolio project demonstrating real-world Laravel development practices
including REST APIs, queued notifications, scheduled commands, feature testing,
SEO fundamentals, GDPR-style data rights, and professional code architecture.

It includes:

- Job Posting & Management
- Public Company Profile Pages
- Resume Upload & Job Application
- Application Status Tracking
- Admin Dashboard with Analytics
- Role-Based Authentication & Authorization
- Secure Access Control using Middleware & Policies
- SEO (Sitemap, robots.txt, JobPosting Schema.org markup)
- GDPR-Style Data Export & Account Deletion (Users and Companies)

Built specifically as a placement-ready full-stack Laravel project.

## Role System

### Admin (Company)

- Manage Job Categories
- Manage Job Roles
- Post Jobs
- Public Company Profile Page (`/company/{slug}`) — showcases active jobs, follower count, and approved testimonials
- View Applications
- Update Application Status (Pending / Shortlisted / Hired / Rejected)
- Interview Scheduling — schedule/reschedule/cancel interviews
- Dashboard Analytics (Charts & Metrics)
- Notification System — real-time notifications for new applications
- Candidate Matching — skill-based match percentage for applicants
- Export My Data — download a JSON export of the company profile, jobs, and applications received
- Delete My Account — permanently deletes the company account and everything tied to it (password-confirmed)

### Super Admin

- Admin Management — create, suspend, reactivate, delete admins
- Platform Overview — total jobs, users, admins stats
- Access Control — full platform control

### User (Job Seeker)

- Register / Login
- Browse Jobs
- Follow Companies — follow a company from its public profile page and get it listed in your dashboard
- Apply for Jobs
- Upload Resume
- Track Application Status
- View Only Own Applications (Security Protected)
- Job Alerts — get an email when matching jobs are posted
- Users cannot access others' applications
- Protected Admin Routes
- Interview Tracking — view scheduled interviews with countdown
- Job Search — search & filter by location, salary, type, experience
- Secure File Upload Handling
- Application History — track all applications and their status
- Export My Data — download a JSON export of your profile, applications, resumes, saved jobs, and activity
- Delete My Account — permanently deletes your account and all personal data (password-confirmed)

### REST API

- Token Auth — Laravel Sanctum Bearer tokens
- Jobs API — list, filter, search, save jobs
- Applications API — apply, track applications
- Profile API — view and update profile

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
- Public Company Profile Pages
- SEO Essentials (sitemap.xml, robots.txt, Open Graph tags, JobPosting/Organization Schema.org JSON-LD)
- GDPR-Style Data Rights (data export + right-to-erasure account deletion for both Users and Companies)

## Dashboard Features

**Admin Dashboard**
- Total Jobs
- Active Jobs
- Total Applications
- Application Status Chart
- Pending / Shortlisted / Hired / Rejected Analytics

**User Dashboard**
- Applied Jobs
- Application Status Overview
- Profile Strength Indicator
- Quick Actions Panel

---

## SEO

- **Dynamic meta tags** — every public page (home, job listing, job detail, company profile) sets its own `<title>`, meta description, canonical URL, Open Graph, and Twitter Card tags instead of one static site-wide title.
- **`/sitemap.xml`** — auto-generated, cached for 30 minutes. Includes the homepage, job listing page, every live job, and every active company profile page.
- **`/robots.txt`** — dynamically generated, keeps admin/dashboard areas out of crawl budget and points crawlers at the sitemap.
- **JobPosting structured data (Schema.org JSON-LD)** on every job detail page — the metadata Google Jobs needs to surface a listing (title, salary, location, employment type, employer, expiry date).
- **Organization structured data** on every company profile page.
- Authenticated dashboard pages (Admin/User panels) are marked `noindex, nofollow` since they require login and shouldn't be indexed anyway.

## GDPR-Style Data Rights

Available from **Account Settings** (User) and **Company Profile** (Admin):

| Right | What it does |
|---|---|
| **Export My Data** | Downloads a JSON file of everything the platform holds about the account — profile, applications, resumes (metadata), saved jobs, alerts, invites, follows, testimonials, and activity log. |
| **Delete My Account** | Password-confirmed, permanent deletion. Related records (applications, resumes, saved jobs, alerts, invites, follows, activity logs) are removed via database cascades; physical files (resumes, job images, category images, profile photos) are cleaned up explicitly since DB-level cascades bypass Laravel's model events. Testimonials and reports the account left behind are anonymised (the personal link is removed) rather than deleted outright, since they remain part of the historical record other users rely on. |

---

### Authentication & Security
- Role-based Access Control (SuperAdmin, Admin, User)
- Secure Login System
- Protected Routes
- Environment-based configuration
- Password-confirmed account deletion

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

### Frontend

- CSS Framework: Bootstrap 4 (via AdminLTE 3)
- Admin Panel: Laravel AdminLTE
- Charts: Chart.js 4.x
- Icons: Font Awesome / Bootstrap Icons
- Build Tooling: Vite + Tailwind CSS (for newer pages)

## Tech Stack
- **Backend:** Laravel (PHP Framework)
- **Frontend:** Blade Templates, HTML, CSS, Bootstrap
- **Database:** MySQL
- **Authentication:** Laravel Auth + Sanctum
- **Server:** XAMPP / Apache

### DevOps & Tools

- Version Control: Git + GitHub
- Local Server: XAMPP (Apache + MySQL)
- IDE: VS Code
- API Testing: Postman
- Code Style: Laravel Pint (PSR-12)

## Project Structure

```
app/                → Application Logic
resources/views/    → Blade Templates (Frontend)
routes/web.php      → Web Routes
routes/api.php      → REST API Routes (v1)
routes/console.php  → Artisan-only commands
public/             → Public Assets
storage/            → Logs & Temporary Files
database/seeders/   → Only seeds a SuperAdmin account (see Installation)
```

## Project Architecture Highlights

- Clean MVC Structure
- Proper Route Grouping
- Role-Based Middleware
- Policy Authorization
- Service-Oriented Logic Separation
- Reusable Blade Components
- Pagination Optimized Queries
- Secure Resume Storage System

## Requirements

- PHP >= 8.2
- Composer
- MySQL
- Apache / XAMPP
- Node.js (for frontend asset building)

---

## Installation Guide

**1. Clone the Repository**
```bash
git clone https://github.com/abdul-samad820/job-portal.git
cd job-portal
```

**2. Install Dependencies**
```bash
composer install
npm install && npm run build
```

**3. Create Environment File**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Configure `.env`**

Update database credentials inside `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=job_portal
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS="noreply@jobportal.com"
MAIL_FROM_NAME="JobConnect"
```

**5. Run Database Migrations, then Seed**

> ⚠️ Migrations create the tables — always run them *before* seeding, not after.

```bash
php artisan migrate
php artisan db:seed
```

`db:seed` only creates a single **SuperAdmin** account — that's the one account every fresh install genuinely needs to log in and start approving/creating companies. It doesn't create sample companies, jobs, or job seekers; add your own through the app, or import a separate demo-data SQL dump if you want a pre-populated dataset for local development.

Optional `.env` overrides for seeding:
```env
SEED_SUPERADMIN_PASSWORD=SuperAdmin@123   # override the default seeded password
ALLOW_PROD_SEEDING=false                  # seeding is blocked in production unless this is true
```

**6. Create Storage Link**
```bash
php artisan storage:link
```

**7. Start the Development Server**
```bash
# Terminal 1: Web server
php artisan serve

# Terminal 2: Queue worker (for emails/notifications)
php artisan queue:work

# Terminal 3: Scheduler (for job alerts, notification cleanup)
php artisan schedule:work
```

Visit: **http://localhost:8000**

## 🔑 Default Login Credentials

> ⚠️ Change these immediately in production!

Only the SuperAdmin account is created automatically by the seeder:

| Role | Email | Password |
|------|-------|----------|
| SuperAdmin | superadmin@jobhub.com | SuperAdmin@123 |

Log in as SuperAdmin to create/approve Company (Admin) accounts. Job Seeker accounts are created through the normal **Register** flow on the site.

### Scheduled Commands

Registered in `bootstrap/app.php` and run automatically by `php artisan schedule:work`:

| Command | Schedule | What it does |
|---|---|---|
| `admin:check-notifications` | Hourly | Checks for new admin notifications |
| `jobs:send-alerts` | Daily at 9:00 AM | Sends job alert emails to subscribed users |
| `app:delete-old-notifications` | Daily | Prunes old notifications so the table doesn't grow unbounded |

Available to run manually (not auto-scheduled):
```bash
php artisan app:delete-expired-jobs   # Deletes jobs past their last application date
```

## API Documentation
`http://localhost:8000/api/v1`

### Authentication
All protected API routes require a Bearer token (Laravel Sanctum):
```
Authorization: Bearer <token>
```

## Testing

**Run All Tests**
```bash
php artisan test
```

**Run Specific Test File**
```bash
php artisan test tests/Feature/UserJobBrowsingTest.php
```

**Run with Coverage**
```bash
php artisan test --coverage
```

**Test Suite**
- 32 Feature test files, 260+ test methods covering auth, jobs, applications, admin management, SuperAdmin flows, and more.

## Screenshots
<img width="1920" height="6832" alt="screencapture-127-0-0-1-8000-2026-07-12-16_45_50" src="https://github.com/user-attachments/assets/71c27945-6cce-455a-8e4b-e643df3ca917" />
<img width="1920" height="2473" alt="screencapture-127-0-0-1-8000-user-form-apply-12-2026-07-12-17_03_36" src="https://github.com/user-attachments/assets/fbd7a6e8-112b-4c1c-8d6b-7bb6a44c09e0" />

<img width="1920" height="3133" alt="screencapture-127-0-0-1-8000-user-single-job-12-2026-07-12-17_02_35" src="https://github.com/user-attachments/assets/599a99d4-7537-489e-8eb7-be54b57b19cd" />
<img width="1920" height="1405" alt="screencapture-127-0-0-1-8000-superadmin-reports-2026-07-12-16_58_27" src="https://github.com/user-attachments/assets/2e2d148e-f21d-4303-a3b4-f841730fa369" />
<img width="1920" height="1113" alt="screencapture-127-0-0-1-8000-superadmin-settings-2026-07-12-16_58_41" src="https://github.com/user-attachments/assets/c1cb21a9-3c93-42d0-9f0c-aaa2f1144ef0" />
<img width="1920" height="4431" alt="screencapture-127-0-0-1-8000-user-job-2026-07-12-17_02_06" src="https://github.com/user-attachments/assets/cd58f581-46c8-4554-97f7-bfe3015fb853" />
<img width="1920" height="1357" alt="screencapture-127-0-0-1-8000-user-dashboard-2026-07-12-16_48_18" src="https://github.com/user-attachments/assets/8aaf4cd8-13c0-4c70-ad0a-26801ea571c4" />
<img width="1920" height="958" alt="screencapture-127-0-0-1-8000-user-user-profile-2026-07-12-16_48_41" src="https://github.com/user-attachments/assets/8eec5c86-2373-466e-8244-8e30a9445a26" />
<img width="1920" height="878" alt="screencapture-127-0-0-1-8000-user-applied-job-2026-07-12-16_49_25" src="https://github.com/user-attachments/assets/de74b6d7-a251-446f-9212-98c42fc5634c" />
<img width="1920" height="977" alt="screencapture-127-0-0-1-8000-job-alerts-2026-07-12-16_49_46" src="https://github.com/user-attachments/assets/c6cfeb9c-dabf-43ce-8c1c-c8dc0f2045ec" />
<img width="1920" height="878" alt="screencapture-127-0-0-1-8000-my-interviews-2026-07-12-16_50_05" src="https://github.com/user-attachments/assets/3c2946ba-66e4-43e7-abc3-fe08d2f0e7d0" />
<img width="1920" height="878" alt="screencapture-127-0-0-1-8000-user-resume-builder-2026-07-12-16_50_45" src="https://github.com/user-attachments/assets/fa473793-b459-4934-975a-3334e824fc25" />
<img width="1920" height="3456" alt="screencapture-127-0-0-1-8000-admin-2026-07-12-16_52_18" src="https://github.com/user-attachments/assets/653c8ae1-9231-4f55-afac-22b711ef5167" />
<img width="1920" height="1021" alt="screencapture-127-0-0-1-8000-admin-job-2026-07-12-16_52_55" src="https://github.com/user-attachments/assets/437f198e-9efe-4bc5-a8f6-191a659a28f7" />
<img width="1920" height="1033" alt="screencapture-127-0-0-1-8000-admin-applications-2026-07-12-16_53_14" src="https://github.com/user-attachments/assets/3f8bfdc3-f9b6-4c0a-bc78-5aceb5a5c1e9" />
<img width="1920" height="878" alt="screencapture-127-0-0-1-8000-faq-2026-07-12-16_53_34" src="https://github.com/user-attachments/assets/f31377f8-9d7e-4b30-8cc0-4f377da5162f" />
<img width="1920" height="887" alt="screencapture-127-0-0-1-8000-admin-testimonials-2026-07-12-16_53_47" src="https://github.com/user-attachments/assets/f0a8a152-18f6-4364-a20f-799a955163af" />
<img width="1920" height="1589" alt="screencapture-127-0-0-1-8000-admin-profile-2026-07-12-16_54_07" src="https://github.com/user-attachments/assets/f7f758f3-a44f-489d-8bd8-253a28492496" />
<img width="1920" height="937" alt="screencapture-127-0-0-1-8000-superadmin-dashboard-2026-07-12-16_55_37" src="https://github.com/user-attachments/assets/6ddde6de-9be5-49b1-9ebc-a794d8060afb" />
<img width="1920" height="937" alt="screencapture-127-0-0-1-8000-superadmin-admins-2026-07-12-16_56_24" src="https://github.com/user-attachments/assets/fe3000e0-1d50-4a30-a023-4d8baca83a4d" />
<img width="1920" height="937" alt="screencapture-127-0-0-1-8000-superadmin-users-2026-07-12-16_56_45" src="https://github.com/user-attachments/assets/b8df7274-3ad2-4512-a9ae-fc019d6f1784" />
<img width="1920" height="937" alt="screencapture-127-0-0-1-8000-superadmin-users-2026-07-12-16_56_45" src="https://github.com/user-attachments/assets/8e397215-b1ea-498b-b51c-04b3a5ed22ce" />
<img width="1920" height="1259" alt="screencapture-127-0-0-1-8000-superadmin-jobs-2026-07-12-16_57_07" src="https://github.com/user-attachments/assets/b7c5034e-62e5-4fc2-a120-301af87f33d7" />
<img width="1920" height="937" alt="screencapture-127-0-0-1-8000-superadmin-testimonials-2026-07-12-16_57_24" src="https://github.com/user-attachments/assets/f2796560-2616-4a94-8807-af77aff1476a" />
<img width="1920" height="937" alt="screencapture-127-0-0-1-8000-superadmin-faqs-2026-07-12-16_57_47" src="https://github.com/user-attachments/assets/75853fb4-b732-422f-9f09-8bdd1be05612" />
<img width="1920" height="937" alt="screencapture-127-0-0-1-8000-superadmin-broadcast-2026-07-12-16_58_04" src="https://github.com/user-attachments/assets/85693a86-fc9f-4603-b1c0-7398b460e69f" />


## Future Improvements

- Employer ↔ Candidate in-app messaging
- Deployment on a cloud platform
- Optional fuzzy/typo-tolerant search (Meilisearch/Algolia integration)

## Developer
Abdul Samad
BCA Final Year Student | Laravel Developer

GitHub: https://github.com/abdul-samad820

<div align="center">

**⭐ If you found this project helpful, consider giving it a star on GitHub!**

*Built with ❤️ using Laravel*

</div>
