# Job Portal Web Application

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-35%20Passing-success?style=for-the-badge)](tests)

</div>

A fully functional Job Portal Web Application built using Laravel 12, PHP 8, MySQL, Bootstrap 4, and AdminLTE 3.
This project is designed with professional architecture, role-based access control, security best practices, and modern UI.

---

## Project Overveiw

**JobConnect** is a production-ready job portal application built with Laravel 12. 
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

- PHP >= 8.x
- Composer
- MySQL
- Apache / XAMPP
- Node.js (if frontend assets used)
  
   ---

## Installation Guide

1️ Clone the Repository
git clone https://github.com/abdul-samad820/job-portal.git
cd job-portal

2️ Install Dependencies
- composer install
- npm install && npm run build

3️ Create Environment File
cp .env.example .env
- Generate Application Key
-php artisan key:generate

Step 4 — Configure `.env`

Update database credentials inside .env:

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

5️ Run Database Migrations

php artisan db:seed
php artisan migrate

6️ Create Storage Link
php artisan storage:link

7️ Start the Development Server

# Terminal 1: Web server
php artisan serve

# Terminal 2: Queue worker (for emails/notifications)
php artisan queue:work

# Terminal 3: Scheduler (for job alerts)
php artisan schedule:work

Visit: **http://localhost:8000**

## 🔑 Default Login Credentials

> ⚠️ Change these immediately in production!

| Role | Email | Password |
|------|-------|----------|
| SuperAdmin | superadmin@jobportal.com | SuperAdmin@123 |
| Admin | admin@techcorp.com | Admin@123 |
| User | user@example.com | User@123 |

### Scheduled Commands

# Manual run
php artisan jobs:send-alerts    # Job alert emails
php artisan jobs:delete-expired # Delete expired jobs

##  API Documentation
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
 - Tests: 35 passed
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
