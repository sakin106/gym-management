# 🏋️ Gym Management System

A full-featured, role-based **Gym Management System** built with **PHP** and **MySQL (PDO)**. Designed to streamline gym operations including member management, payments, attendance tracking, trainer assignments, workout & diet plans, and more.

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [User Roles](#-user-roles)
- [Database Structure](#-database-structure)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Project Structure](#-project-structure)
- [Screenshots](#-screenshots)
- [License](#-license)

---

## ✨ Features

### Admin Panel
- 📊 Dashboard with analytics and charts
- 👥 Member management (add, edit, activate, freeze)
- 💳 Membership plans & services management
- 💰 Payment processing with receipt generation
- 🎟️ Discount & coupon code system
- 📅 Attendance tracking
- 🏃 Trainer management & member assignments
- 🧘 Class scheduling & enrollment
- 🥗 Diet plan creation & assignment
- 💪 Workout plan management
- 📈 Member progress tracking
- 🔧 Equipment & maintenance logs
- 📢 Announcements & notifications
- 📊 Reports generation
- 👨‍💼 Staff account management

### Staff Panel
- View & manage member list
- Process payments
- Mark attendance
- Staff dashboard overview

### Customer Portal
- Personalized dashboard
- View assigned workout plans
- Access diet plans
- Track personal progress
- Todo / goal list
- View announcements

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8+ |
| Database | MySQL / MariaDB |
| ORM / DB Layer | PDO (PHP Data Objects) |
| Frontend | HTML5, CSS3, JavaScript |
| UI Framework | Bootstrap |
| Server | Apache (XAMPP / LAMP) |

---

## 👤 User Roles

| Role | Access Level |
|------|-------------|
| **Admin** | Full system access |
| **Staff** | Members, payments, attendance |
| **Customer** | Personal dashboard, plans, progress |

---

## 🗄️ Database Structure

The system uses **21 relational tables**:

| # | Table | Description |
|---|-------|-------------|
| 1 | `users` | All system users (admin, staff, customer) |
| 2 | `membership_plans` | Available gym membership tiers |
| 3 | `services` | Add-on services with monthly charges |
| 4 | `discounts` | Discount codes and usage tracking |
| 5 | `member_memberships` | Member-plan-service associations |
| 6 | `payments` | Payment records and receipts |
| 7 | `attendance` | Daily attendance logs |
| 8 | `trainers` | Trainer profiles |
| 9 | `trainer_assignments` | Member-trainer relationships |
| 10 | `classes` | Group fitness classes |
| 11 | `class_enrollments` | Class booking records |
| 12 | `workout_plans` | Workout plan definitions |
| 13 | `workout_exercises` | Exercises within workout plans |
| 14 | `diet_plans` | Assigned diet plans |
| 15 | `diet_items` | Food items within diet plans |
| 16 | `customer_progress` | Weight, body metrics over time |
| 17 | `todo_list` | Customer personal goals |
| 18 | `announcements` | Gym-wide announcements |
| 19 | `notifications` | User-specific notifications |
| 20 | `equipment` | Gym equipment inventory |
| 21 | `equipment_maintenance` | Equipment maintenance logs |

---

## ⚙️ Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB
- Apache server (XAMPP, WAMP, or LAMP recommended)

### Steps

**1. Clone the repository**
```bash
git clone https://github.com/sakin106/gym-management.git
```

**2. Move to your server directory**
```bash
# For XAMPP (Windows)
mv gym-management C:/xampp/htdocs/gym_management

# For XAMPP (Mac)
mv gym-management /Applications/XAMPP/htdocs/gym_management

# For LAMP (Linux)
mv gym-management /var/www/html/gym_management
```

**3. Import the database**
- Open **phpMyAdmin** → `http://localhost/phpmyadmin`
- Create a new database named `gym_db`
- Click **Import** and upload `gym_db.sql`
- Optionally import `gym_db_data.sql` for sample data

**4. Configure the database connection**

Edit `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gym_db');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // your MySQL password
```

**5. Run the application**

Open your browser and navigate to:
```
http://localhost/gym_management
```

---

## 🔐 Default Login Credentials

> ⚠️ Change these immediately after first login.

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@gym.com | admin123 |
| Staff | staff@gym.com | staff123 |
| Customer | customer@gym.com | customer123 |

*(Check `gym_db_data.sql` for seeded accounts)*

---

## 📁 Project Structure

```
gym_management/
│
├── admin/                  # Admin panel pages
│   ├── dashboard.php
│   ├── members.php
│   ├── payments.php
│   ├── trainers.php
│   ├── workout_plans.php
│   ├── diet_plans.php
│   ├── equipment.php
│   ├── reports.php
│   └── ...
│
├── staff/                  # Staff panel pages
│   ├── dashboard.php
│   ├── members.php
│   ├── payments.php
│   └── attendance.php
│
├── customer/               # Customer portal pages
│   ├── dashboard.php
│   ├── workout.php
│   ├── diet.php
│   ├── progress.php
│   └── profile.php
│
├── api/                    # AJAX API endpoints
│   ├── get_chart_data.php
│   ├── mark_attendance.php
│   ├── process_payment.php
│   └── send_notification.php
│
├── auth/                   # Authentication
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── config/
│   └── db.php              # Database connection & helpers
│
├── includes/               # Shared UI components
│   ├── header.php
│   ├── footer.php
│   ├── sidebar_admin.php
│   ├── sidebar_staff.php
│   └── sidebar_customer.php
│
├── assets/
│   ├── css/custom.css
│   └── js/custom.js
│
├── gym_db.sql              # Main database schema
├── gym_db_data.sql         # Sample seed data
└── index.php               # Entry point
```

---

## 🔒 Security Features

- PDO prepared statements (SQL injection prevention)
- CSRF token protection on all forms
- Role-based access control (RBAC)
- Session-based authentication
- Password hashing

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

## 🙌 Acknowledgements

Built as a **DBMS Lab Mini Project**. Contributions and feedback are welcome!

---

⭐ If you found this project helpful, consider giving it a star on GitHub!
