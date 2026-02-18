# Job Portal Web Application

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![PHP](https://img.shields.io/badge/PHP-8.x-blue)
![MySQL](https://img.shields.io/badge/Database-MySQL-orange)
![MVC](https://img.shields.io/badge/Architecture-MVC-green)


A fully functional Job Portal Web Application built using Laravel 12, PHP 8, MySQL, Bootstrap 4, and AdminLTE 3.
This project is designed with professional architecture, role-based access control, security best practices, and modern UI.

---

## Project Overveiw
 -- This Job Portal system connects Users (Job Seekers) and Admins (Recruiters) with secure role-based access and professional workflow management. --

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
- Dashboard Analytics (Charts & Metrics)
- Notification System

## User

- Register / Login
- Browse Jobs
- Apply for Jobs
- Upload Resume
- Track Application Status
- View Only Own Applications (Security Protected)
- Security Features
- Middleware-based role protection
- Policy-based authorization
- Users cannot access others' applications
- Protected Admin Routes
- Secure File Upload Handling

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

---

##  Tech Stack
- **Backend:** Laravel (PHP Framework)
- **Frontend:** Blade Templates, HTML, CSS, Bootstrap
- **Database:** MySQL
- **Authentication:** Laravel Auth
- **Server:** XAMPP / Apache

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
composer install

3️ Create Environment File
cp .env.example .env


Update database credentials inside .env:

DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

4️ Generate Application Key

php artisan key:generate

5️ Run Database Migrations

php artisan db:seed
php artisan migrate

6️ Create Storage Link
php artisan storage:link

7️ Start the Development Server
php artisan serve


Now open in browser:

http://127.0.0.1:8000


## Future Improvements

REST API Integration
Deployment on Cloud Platform

## Developer
Abdul Samad
BCA Final Year Student | Laravel Developer

GitHub: https://github.com/abdul-samad820

___--- If you found this project useful, consider giving it a star!
