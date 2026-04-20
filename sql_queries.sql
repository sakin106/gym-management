-- ============================================
-- GYM MANAGEMENT SYSTEM — 12 SQL QUERIES
-- DBMS Lab Mini Project | EP1, EP2, EP4 Mapping
-- ============================================
USE gym_db;

-- ============================================
-- Q1. [INNER JOIN] Active members with service and plan details
-- EP1: Demonstrates JOIN operations and relational query design
-- ============================================
SELECT u.user_id, u.full_name, u.email, u.phone, u.gender,
       s.service_name, s.monthly_charge,
       mp.plan_name, mp.duration_months, mp.price,
       mm.start_date, mm.end_date, mm.status, mm.total_amount
FROM users u
INNER JOIN member_memberships mm ON u.user_id = mm.user_id
INNER JOIN membership_plans mp ON mm.plan_id = mp.plan_id
INNER JOIN services s ON mm.service_id = s.service_id
WHERE mm.status = 'active';

-- ============================================
-- Q2. [GROUP BY + COUNT] Total enrolled members per membership plan
-- EP4: Investigation — Which plan is most popular?
-- ============================================
SELECT mp.plan_name, mp.duration_months, mp.price,
       COUNT(mm.membership_id) AS total_members
FROM membership_plans mp
LEFT JOIN member_memberships mm ON mp.plan_id = mm.plan_id
GROUP BY mp.plan_id, mp.plan_name, mp.duration_months, mp.price
ORDER BY total_members DESC;

-- ============================================
-- Q3. [HAVING] Plans with more than 5 active members
-- EP1: Application of aggregate filtering with HAVING clause
-- ============================================
SELECT mp.plan_name, COUNT(mm.membership_id) AS active_count
FROM membership_plans mp
JOIN member_memberships mm ON mp.plan_id = mm.plan_id
WHERE mm.status = 'active'
GROUP BY mp.plan_id, mp.plan_name
HAVING active_count > 5;

-- ============================================
-- Q4. [LEFT JOIN + NULL] Members who never checked in
-- EP2: Identifying inactive members for engagement strategies
-- ============================================
SELECT u.user_id, u.full_name, u.email, u.phone, u.status
FROM users u
LEFT JOIN attendance a ON u.user_id = a.user_id
WHERE u.role = 'customer' AND a.attendance_id IS NULL;

-- ============================================
-- Q5. [RIGHT JOIN] All trainers including those with no assigned members
-- EP2: Analyzing trainer utilization for resource planning
-- ============================================
SELECT u.full_name AS trainer_name, t.specialization, t.status,
       COUNT(ta.assignment_id) AS assigned_members
FROM trainer_assignments ta
RIGHT JOIN trainers t ON ta.trainer_id = t.trainer_id
JOIN users u ON t.user_id = u.user_id
GROUP BY t.trainer_id, u.full_name, t.specialization, t.status;

-- ============================================
-- Q6. [SUBQUERY] Members whose payment is overdue
-- EP2: Identifying revenue collection issues
-- ============================================
SELECT u.user_id, u.full_name, u.email, u.phone, p.amount, p.payment_date
FROM users u
WHERE u.user_id IN (
    SELECT p.user_id FROM payments p WHERE p.status = 'overdue'
);

-- ============================================
-- Q7. [NESTED QUERY] Top 3 trainers by number of assigned members
-- EP4: Investigation — Which trainer has highest workload?
-- ============================================
SELECT trainer_name, specialization, member_count
FROM (
    SELECT u.full_name AS trainer_name, t.specialization,
           COUNT(ta.member_id) AS member_count
    FROM trainers t
    JOIN users u ON t.user_id = u.user_id
    LEFT JOIN trainer_assignments ta ON t.trainer_id = ta.trainer_id
    GROUP BY t.trainer_id, u.full_name, t.specialization
    ORDER BY member_count DESC
    LIMIT 3
) AS top_trainers;

-- ============================================
-- Q8. [SUM + GROUP BY + MONTH] Monthly revenue for current year
-- EP4: Investigation — What is the monthly revenue trend?
-- ============================================
SELECT MONTH(payment_date) AS month_num,
       MONTHNAME(payment_date) AS month_name,
       COUNT(*) AS total_payments,
       SUM(amount) AS total_revenue
FROM payments
WHERE status = 'paid' AND YEAR(payment_date) = YEAR(CURDATE())
GROUP BY MONTH(payment_date), MONTHNAME(payment_date)
ORDER BY month_num;

-- ============================================
-- Q9. [SUBQUERY + HAVING] Classes over 80% capacity
-- EP2: Capacity planning and class scheduling optimization
-- ============================================
SELECT c.class_name, c.schedule_day, c.capacity,
       (SELECT COUNT(*) FROM class_enrollments ce
        WHERE ce.class_id = c.class_id AND ce.status = 'enrolled') AS enrolled,
       ROUND((SELECT COUNT(*) FROM class_enrollments ce
              WHERE ce.class_id = c.class_id AND ce.status = 'enrolled') / c.capacity * 100, 1) AS fill_pct
FROM classes c
HAVING fill_pct > 80;

-- ============================================
-- Q10. [JOIN + GROUP BY + DATE] Attendance count per member for current month
-- EP4: Investigation — Average member attendance per month
-- ============================================
SELECT u.full_name, COUNT(a.attendance_id) AS days_attended,
       ROUND(AVG(a.working_hours), 2) AS avg_hours
FROM attendance a
JOIN users u ON a.user_id = u.user_id
WHERE MONTH(a.date) = MONTH(CURDATE()) AND YEAR(a.date) = YEAR(CURDATE())
GROUP BY u.user_id, u.full_name
ORDER BY days_attended DESC;

-- ============================================
-- Q11. [SUM + JOIN] Equipment purchase cost vs maintenance cost
-- EP1: Financial analysis using aggregate functions
-- ============================================
SELECT
    (SELECT COALESCE(SUM(total_cost), 0) FROM equipment) AS total_purchase_cost,
    (SELECT COALESCE(SUM(cost), 0) FROM equipment_maintenance) AS total_maintenance_cost,
    (SELECT COALESCE(SUM(total_cost), 0) FROM equipment) +
    (SELECT COALESCE(SUM(cost), 0) FROM equipment_maintenance) AS grand_total_expenses;

-- ============================================
-- Q12. [AVG + JOIN] Average weight loss percentage per service
-- EP4: Investigation — Which service drives most member progress?
-- ============================================
SELECT s.service_name,
       COUNT(DISTINCT cp.user_id) AS members_tracked,
       ROUND(AVG(cp.progress_pct), 2) AS avg_progress_pct
FROM customer_progress cp
JOIN member_memberships mm ON cp.user_id = mm.user_id
JOIN services s ON mm.service_id = s.service_id
GROUP BY s.service_id, s.service_name
ORDER BY avg_progress_pct DESC;
