# Job Portal Web Application

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![PHP](https://img.shields.io/badge/PHP-8.x-blue)
![MySQL](https://img.shields.io/badge/Database-MySQL-orange)
![MVC](https://img.shields.io/badge/Architecture-MVC-green)


A full-stack Job Portal Web Application built using Laravel and MySQL that enables secure job posting, application tracking, and role-based access control using MVC architecture.

---

##  Features

### User (Job Seeker)
- User Registration & Login
- Profile Creation & Update
- Browse & Search Jobs
- Apply for Jobs
- View Applied Jobs History

###  Admin
- Admin Dashboard
- Manage Job Categories
- Add / Edit / Delete Jobs
- View Job Applications
- Select Candidates

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

##  Requirements

- PHP >= 8.x
- Composer
- MySQL
- Apache / XAMPP
- Node.js (if frontend assets used)


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
php artisan migrate


(Optional: If seeders are available)

php artisan db:seed

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
