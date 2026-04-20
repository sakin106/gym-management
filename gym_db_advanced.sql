-- ============================================
-- TRIGGERS, PROCEDURES, VIEW, ROLES & SAMPLE DATA
-- Append to gym_db.sql or run after schema
-- ============================================
USE gym_db;

-- ========== TRIGGERS ==========

-- TRIGGER 1: Auto-expire membership
DELIMITER //
CREATE TRIGGER trg_auto_expire_membership
BEFORE UPDATE ON member_memberships
FOR EACH ROW
BEGIN
    IF NEW.end_date < CURDATE() AND NEW.status != 'cancelled' THEN
        SET NEW.status = 'expired';
    END IF;
END//
DELIMITER ;

-- TRIGGER 2: Check class capacity
DELIMITER //
CREATE TRIGGER trg_check_class_capacity
BEFORE INSERT ON class_enrollments
FOR EACH ROW
BEGIN
    DECLARE current_count INT;
    DECLARE max_cap INT;
    SELECT COUNT(*) INTO current_count FROM class_enrollments WHERE class_id = NEW.class_id AND status = 'enrolled';
    SELECT capacity INTO max_cap FROM classes WHERE class_id = NEW.class_id;
    IF current_count >= max_cap THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Class is full. Cannot enroll.';
    END IF;
END//
DELIMITER ;

-- TRIGGER 3: Auto-calculate progress percentage
DELIMITER //
CREATE TRIGGER trg_calculate_progress
BEFORE INSERT ON customer_progress
FOR EACH ROW
BEGIN
    IF NEW.initial_weight > 0 THEN
        SET NEW.progress_pct = ((NEW.initial_weight - NEW.current_weight) / NEW.initial_weight) * 100;
    ELSE
        SET NEW.progress_pct = 0;
    END IF;
END//
DELIMITER ;

-- ========== STORED PROCEDURES ==========

-- PROCEDURE 1: RenewMembership
DELIMITER //
CREATE PROCEDURE RenewMembership(
    IN p_user_id INT, IN p_plan_id INT, IN p_service_id INT, IN p_discount_id INT
)
BEGIN
    DECLARE v_months INT; DECLARE v_price DECIMAL(10,2);
    DECLARE v_charge DECIMAL(10,2); DECLARE v_total DECIMAL(10,2);
    DECLARE v_disc_pct DECIMAL(5,2) DEFAULT 0;
    DECLARE v_start DATE; DECLARE v_end DATE;

    SELECT duration_months, price INTO v_months, v_price FROM membership_plans WHERE plan_id = p_plan_id;
    SELECT monthly_charge INTO v_charge FROM services WHERE service_id = p_service_id;

    SET v_total = v_price + (v_charge * v_months);

    IF p_discount_id IS NOT NULL AND p_discount_id > 0 THEN
        SELECT percentage INTO v_disc_pct FROM discounts WHERE discount_id = p_discount_id AND status = 'active' AND valid_until >= CURDATE() AND used_count < max_uses;
        SET v_total = v_total - (v_total * v_disc_pct / 100);
        UPDATE discounts SET used_count = used_count + 1 WHERE discount_id = p_discount_id;
    END IF;

    SET v_start = CURDATE();
    SET v_end = DATE_ADD(CURDATE(), INTERVAL v_months MONTH);

    INSERT INTO member_memberships (user_id, plan_id, service_id, start_date, end_date, status, discount_id, total_amount, registered_by)
    VALUES (p_user_id, p_plan_id, p_service_id, v_start, v_end, 'pending', IF(p_discount_id>0, p_discount_id, NULL), v_total, 'admin');

    INSERT INTO payments (membership_id, user_id, amount, payment_date, method, status, receipt_no, processed_by)
    VALUES (LAST_INSERT_ID(), p_user_id, v_total, CURDATE(), 'cash', 'pending', CONCAT('RCP-', DATE_FORMAT(NOW(),'%Y%m%d'), '-', LPAD(FLOOR(RAND()*9999),4,'0')), 1);

    UPDATE users SET status = 'active' WHERE user_id = p_user_id;
END//
DELIMITER ;

-- PROCEDURE 2: FreezeMembership
DELIMITER //
CREATE PROCEDURE FreezeMembership(
    IN p_membership_id INT, IN p_freeze_start DATE, IN p_freeze_end DATE
)
BEGIN
    DECLARE v_freeze_days INT; DECLARE v_max_freeze INT;
    SET v_freeze_days = DATEDIFF(p_freeze_end, p_freeze_start);

    SELECT mp.max_freeze_days INTO v_max_freeze FROM member_memberships mm JOIN membership_plans mp ON mm.plan_id = mp.plan_id WHERE mm.membership_id = p_membership_id;

    IF v_freeze_days > v_max_freeze THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Freeze period exceeds maximum allowed days.';
    END IF;

    UPDATE member_memberships SET status = 'frozen', freeze_start = p_freeze_start, freeze_end = p_freeze_end, end_date = DATE_ADD(end_date, INTERVAL v_freeze_days DAY)
    WHERE membership_id = p_membership_id;
END//
DELIMITER ;

-- ========== VIEW ==========
CREATE OR REPLACE VIEW active_members_view AS
SELECT u.user_id, u.full_name, u.email, u.phone, u.gender,
       s.service_name, s.monthly_charge,
       mp.plan_name, mp.duration_months, mp.price,
       mm.start_date, mm.end_date, mm.status, mm.total_amount
FROM users u
JOIN member_memberships mm ON u.user_id = mm.user_id
JOIN membership_plans mp ON mm.plan_id = mp.plan_id
JOIN services s ON mm.service_id = s.service_id
WHERE mm.status = 'active';

-- ========== ROLES & PRIVILEGES ==========
-- (Run these as root user)
-- CREATE USER IF NOT EXISTS 'gym_admin'@'localhost' IDENTIFIED BY 'admin_pass';
-- GRANT ALL PRIVILEGES ON gym_db.* TO 'gym_admin'@'localhost';
-- CREATE USER IF NOT EXISTS 'gym_staff'@'localhost' IDENTIFIED BY 'staff_pass';
-- GRANT SELECT, INSERT, UPDATE ON gym_db.attendance TO 'gym_staff'@'localhost';
-- GRANT SELECT, INSERT, UPDATE ON gym_db.payments TO 'gym_staff'@'localhost';
-- GRANT SELECT ON gym_db.users TO 'gym_staff'@'localhost';
-- GRANT SELECT ON gym_db.active_members_view TO 'gym_staff'@'localhost';
-- CREATE USER IF NOT EXISTS 'gym_customer'@'localhost' IDENTIFIED BY 'customer_pass';
-- GRANT SELECT ON gym_db.active_members_view TO 'gym_customer'@'localhost';
-- GRANT SELECT ON gym_db.announcements TO 'gym_customer'@'localhost';
-- GRANT SELECT ON gym_db.notifications TO 'gym_customer'@'localhost';
-- FLUSH PRIVILEGES;
