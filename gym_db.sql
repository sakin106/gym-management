-- ============================================
-- GYM MANAGEMENT SYSTEM — DATABASE SCHEMA
-- Database: gym_db | Charset: utf8mb4
-- ============================================

CREATE DATABASE IF NOT EXISTS gym_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE gym_db;

-- TABLE 1: users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    gender ENUM('male','female','other'),
    role ENUM('admin','staff','customer') DEFAULT 'customer',
    status ENUM('active','pending','expired','frozen') DEFAULT 'pending',
    profile_pic VARCHAR(255),
    address TEXT,
    dob DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLE 2: membership_plans
CREATE TABLE membership_plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(100) NOT NULL,
    duration_months INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    max_freeze_days INT DEFAULT 30,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLE 3: services
CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    monthly_charge DECIMAL(10,2) NOT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLE 4: discounts
CREATE TABLE discounts (
    discount_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    percentage DECIMAL(5,2) NOT NULL CHECK (percentage BETWEEN 0 AND 100),
    valid_from DATE NOT NULL,
    valid_until DATE NOT NULL,
    max_uses INT DEFAULT 1,
    used_count INT DEFAULT 0,
    status ENUM('active','expired') DEFAULT 'active'
);

-- TABLE 5: member_memberships
CREATE TABLE member_memberships (
    membership_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    service_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active','expired','frozen','pending','cancelled') DEFAULT 'pending',
    freeze_start DATE,
    freeze_end DATE,
    discount_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    registered_by ENUM('self','admin') DEFAULT 'self',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES membership_plans(plan_id),
    FOREIGN KEY (service_id) REFERENCES services(service_id),
    FOREIGN KEY (discount_id) REFERENCES discounts(discount_id)
);

-- TABLE 6: payments
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    membership_id INT,
    user_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    method ENUM('cash','card','mobile') DEFAULT 'cash',
    status ENUM('paid','pending','overdue') DEFAULT 'pending',
    receipt_no VARCHAR(50) UNIQUE NOT NULL,
    processed_by INT,
    discount_applied DECIMAL(10,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (membership_id) REFERENCES member_memberships(membership_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (processed_by) REFERENCES users(user_id)
);

-- TABLE 7: attendance
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    check_in DATETIME NOT NULL,
    check_out DATETIME,
    date DATE NOT NULL,
    working_hours DECIMAL(4,2),
    marked_by INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (marked_by) REFERENCES users(user_id)
);

-- TABLE 8: trainers
CREATE TABLE trainers (
    trainer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    specialization VARCHAR(100) NOT NULL,
    hire_date DATE,
    salary DECIMAL(10,2),
    bio TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- TABLE 9: trainer_assignments
CREATE TABLE trainer_assignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_id INT,
    member_id INT,
    assigned_date DATE NOT NULL,
    end_date DATE,
    notes TEXT,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- TABLE 10: classes
CREATE TABLE classes (
    class_id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    trainer_id INT,
    schedule_day ENUM('Mon','Tue','Wed','Thu','Fri','Sat','Sun'),
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    capacity INT NOT NULL,
    room VARCHAR(50),
    status ENUM('active','cancelled') DEFAULT 'active',
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id)
);

-- TABLE 11: class_enrollments
CREATE TABLE class_enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    user_id INT,
    enrolled_date DATE NOT NULL,
    status ENUM('enrolled','dropped') DEFAULT 'enrolled',
    UNIQUE KEY unique_enrollment (class_id, user_id),
    FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- TABLE 12: workout_plans
CREATE TABLE workout_plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    trainer_id INT,
    plan_name VARCHAR(100) NOT NULL,
    goal TEXT,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id)
);

-- TABLE 13: workout_exercises
CREATE TABLE workout_exercises (
    exercise_id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT,
    exercise_name VARCHAR(100) NOT NULL,
    sets INT,
    reps INT,
    duration_minutes INT,
    day_of_week ENUM('Mon','Tue','Wed','Thu','Fri','Sat','Sun'),
    notes TEXT,
    FOREIGN KEY (plan_id) REFERENCES workout_plans(plan_id) ON DELETE CASCADE
);

-- TABLE 14: diet_plans
CREATE TABLE diet_plans (
    diet_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    trainer_id INT,
    plan_name VARCHAR(100) NOT NULL,
    calorie_target INT,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id)
);

-- TABLE 15: diet_items
CREATE TABLE diet_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    diet_id INT,
    meal_type ENUM('breakfast','lunch','dinner','snack'),
    food_name VARCHAR(100) NOT NULL,
    quantity VARCHAR(50),
    calories INT,
    protein DECIMAL(5,2),
    carbs DECIMAL(5,2),
    fat DECIMAL(5,2),
    FOREIGN KEY (diet_id) REFERENCES diet_plans(diet_id) ON DELETE CASCADE
);

-- TABLE 16: customer_progress
CREATE TABLE customer_progress (
    progress_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    recorded_date DATE NOT NULL,
    initial_weight DECIMAL(5,2) NOT NULL,
    current_weight DECIMAL(5,2) NOT NULL,
    initial_body_type VARCHAR(50),
    current_body_type VARCHAR(50),
    progress_pct DECIMAL(5,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- TABLE 17: todo_list
CREATE TABLE todo_list (
    todo_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    task TEXT NOT NULL,
    due_date DATE,
    status ENUM('pending','done') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- TABLE 18: announcements
CREATE TABLE announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    posted_by INT,
    applied_date DATE NOT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(user_id)
);

-- TABLE 19: notifications
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT NOT NULL,
    type ENUM('alert','info','payment_due'),
    is_read TINYINT(1) DEFAULT 0,
    sent_by INT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (sent_by) REFERENCES users(user_id)
);

-- TABLE 20: equipment
CREATE TABLE equipment (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    quantity INT NOT NULL DEFAULT 1,
    purchase_date DATE,
    vendor_name VARCHAR(100),
    vendor_contact VARCHAR(50),
    unit_cost DECIMAL(10,2),
    total_cost DECIMAL(10,2),
    condition_status ENUM('good','fair','poor') DEFAULT 'good',
    zone VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLE 21: equipment_maintenance
CREATE TABLE equipment_maintenance (
    maintenance_id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT,
    maintenance_date DATE NOT NULL,
    description TEXT,
    cost DECIMAL(10,2),
    performed_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id) ON DELETE CASCADE
);
