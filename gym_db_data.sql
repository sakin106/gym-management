-- ============================================
-- SAMPLE DATA — 20+ Records Per Table
-- Password for all users: password123
-- Hashed: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================
USE gym_db;

-- USERS (25 records: 1 admin, 2 staff, 22 customers)
INSERT INTO users (full_name, email, password, phone, gender, role, status, dob, address) VALUES
('Admin User','admin@gym.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','9876543210','male','admin','active','1985-03-15','123 Admin St'),
('Sarah Johnson','staff@gym.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','9876543211','female','staff','active','1990-07-22','456 Staff Ave'),
('Mike Wilson','staff2@gym.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','9876543212','male','staff','active','1988-11-10','789 Staff Blvd'),
('John Doe','john@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567890','male','customer','active','1995-01-15','10 Main St'),
('Jane Smith','jane@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567891','female','customer','active','1998-05-20','11 Oak Ave'),
('Robert Brown','robert@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567892','male','customer','active','1992-08-30','12 Pine Rd'),
('Emily Davis','emily@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567893','female','customer','active','1997-03-12','13 Elm St'),
('Michael Lee','michael@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567894','male','customer','active','1993-11-25','14 Cedar Ln'),
('Amanda Clark','amanda@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567895','female','customer','active','1996-07-08','15 Birch Dr'),
('Daniel Martinez','daniel@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567896','male','customer','active','1994-09-17','16 Maple St'),
('Jessica Taylor','jessica@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567897','female','customer','active','1999-02-28','17 Walnut Ave'),
('David Anderson','david@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567898','male','customer','active','1991-06-05','18 Spruce Rd'),
('Sophia Thomas','sophia@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567899','female','customer','active','2000-12-01','19 Ash Blvd'),
('Chris Jackson','chris@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567800','male','customer','active','1990-04-14','20 Ivy Ln'),
('Olivia White','olivia@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567801','female','customer','active','1997-10-22','21 Fern St'),
('James Harris','james@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567802','male','customer','active','1993-01-30','22 Rose Ave'),
('Mia Robinson','mia@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567803','female','customer','pending','1998-08-18','23 Lily Rd'),
('Ethan Lewis','ethan@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567804','male','customer','pending','1995-05-09','24 Daisy Dr'),
('Ava Walker','ava@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567805','female','customer','active','1996-11-27','25 Tulip Ln'),
('Noah Hall','noah@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567806','male','customer','active','1994-03-03','26 Orchid St'),
('Isabella Allen','isabella@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567807','female','customer','expired','1992-07-19','27 Peony Ave'),
('Liam Young','liam@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567808','male','customer','frozen','1997-09-14','28 Clover Rd'),
('Emma King','emma@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567809','female','customer','active','1999-12-25','29 Sage Blvd'),
('Lucas Wright','lucas@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567810','male','customer','active','1991-02-11','30 Mint Ln'),
('Harper Scott','harper@email.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1234567811','female','customer','active','1998-06-07','31 Thyme St');

-- MEMBERSHIP PLANS (6 records)
INSERT INTO membership_plans (plan_name, duration_months, price, description, max_freeze_days) VALUES
('Basic Monthly',1,29.99,'Basic gym access - monthly',7),
('Basic Quarterly',3,79.99,'Basic gym access - quarterly',15),
('Standard Monthly',1,49.99,'Standard access with classes',10),
('Standard Quarterly',3,129.99,'Standard access - quarterly',20),
('Premium Monthly',1,79.99,'Full access with personal trainer',15),
('Premium Yearly',12,799.99,'Full access - yearly with all perks',60);

-- SERVICES (6 records)
INSERT INTO services (service_name, description, monthly_charge) VALUES
('Cardio Training','Treadmill, cycling, elliptical access',15.00),
('Weight Training','Free weights and machine access',20.00),
('Yoga','Group yoga sessions with instructor',25.00),
('Swimming','Olympic pool access',30.00),
('CrossFit','High-intensity functional training',35.00),
('Personal Training','One-on-one trainer sessions',50.00);

-- DISCOUNTS (6 records)
INSERT INTO discounts (code, percentage, valid_from, valid_until, max_uses, used_count) VALUES
('WELCOME10',10.00,'2026-01-01','2026-12-31',100,5),
('SUMMER20',20.00,'2026-06-01','2026-08-31',50,0),
('STUDENT15',15.00,'2026-01-01','2026-12-31',200,12),
('NEWYEAR25',25.00,'2026-01-01','2026-01-31',30,28),
('REFER10',10.00,'2026-01-01','2026-12-31',500,45),
('VIP30',30.00,'2026-03-01','2026-06-30',10,2);

-- MEMBER MEMBERSHIPS (20 records)
INSERT INTO member_memberships (user_id, plan_id, service_id, start_date, end_date, status, total_amount, registered_by) VALUES
(4,5,6,'2026-01-01','2026-02-01','active',129.99,'admin'),
(5,3,3,'2026-02-01','2026-03-01','active',74.99,'self'),
(6,6,2,'2025-06-01','2026-06-01','active',1039.99,'admin'),
(7,1,1,'2026-03-01','2026-04-01','active',44.99,'self'),
(8,4,5,'2026-01-15','2026-04-15','active',234.99,'admin'),
(9,5,4,'2026-02-10','2026-03-10','active',109.99,'self'),
(10,2,2,'2026-01-01','2026-04-01','active',139.99,'admin'),
(11,3,3,'2026-03-01','2026-04-01','active',74.99,'self'),
(12,6,1,'2025-04-01','2026-04-01','active',979.99,'admin'),
(13,1,6,'2026-03-15','2026-04-15','active',79.99,'self'),
(14,5,5,'2026-02-01','2026-03-01','active',114.99,'admin'),
(15,4,4,'2026-01-01','2026-04-01','active',219.99,'self'),
(16,3,2,'2026-03-01','2026-04-01','active',69.99,'admin'),
(19,2,1,'2026-02-01','2026-05-01','active',124.99,'self'),
(20,5,3,'2026-01-15','2026-02-15','active',104.99,'admin'),
(23,1,4,'2026-03-20','2026-04-20','active',59.99,'self'),
(24,4,6,'2026-01-01','2026-04-01','active',279.99,'admin'),
(25,3,5,'2026-02-15','2026-03-15','active',84.99,'self'),
(17,1,1,'2026-03-25','2026-04-25','pending',44.99,'self'),
(18,2,2,'2026-03-28','2026-06-28','pending',139.99,'self');

-- PAYMENTS (20 records)
INSERT INTO payments (membership_id, user_id, amount, payment_date, method, status, receipt_no, processed_by, discount_applied) VALUES
(1,4,129.99,'2026-01-01','card','paid','RCP-20260101-0001',1,0),
(2,5,74.99,'2026-02-01','cash','paid','RCP-20260201-0001',2,0),
(3,6,1039.99,'2025-06-01','card','paid','RCP-20250601-0001',1,0),
(4,7,44.99,'2026-03-01','mobile','paid','RCP-20260301-0001',2,0),
(5,8,234.99,'2026-01-15','card','paid','RCP-20260115-0001',1,0),
(6,9,109.99,'2026-02-10','cash','paid','RCP-20260210-0001',1,0),
(7,10,125.99,'2026-01-01','card','paid','RCP-20260101-0002',1,14.00),
(8,11,74.99,'2026-03-01','mobile','paid','RCP-20260301-0002',2,0),
(9,12,979.99,'2025-04-01','card','paid','RCP-20250401-0001',1,0),
(10,13,79.99,'2026-03-15','cash','paid','RCP-20260315-0001',2,0),
(11,14,114.99,'2026-02-01','card','paid','RCP-20260201-0002',1,0),
(12,15,219.99,'2026-01-01','mobile','paid','RCP-20260101-0003',1,0),
(13,16,69.99,'2026-03-01','cash','paid','RCP-20260301-0003',2,0),
(14,19,124.99,'2026-02-01','card','paid','RCP-20260201-0003',1,0),
(15,20,104.99,'2026-01-15','cash','paid','RCP-20260115-0002',1,0),
(16,23,59.99,'2026-03-20','mobile','paid','RCP-20260320-0001',2,0),
(17,24,279.99,'2026-01-01','card','paid','RCP-20260101-0004',1,0),
(18,25,84.99,'2026-02-15','cash','paid','RCP-20260215-0001',2,0),
(19,17,44.99,'2026-03-25','cash','pending','RCP-20260325-0001',NULL,0),
(20,18,139.99,'2026-03-28','card','overdue','RCP-20260328-0001',NULL,0);

-- TRAINERS (5 records)
INSERT INTO trainers (user_id, specialization, hire_date, salary, bio, status) VALUES
(2,'Cardio, HIIT','2024-01-15',3500.00,'Certified cardio and HIIT trainer with 5 years experience','active'),
(3,'Weight Training, CrossFit','2023-06-01',4000.00,'Strength training specialist with CrossFit L2 certification','active'),
(4,'Yoga, Flexibility','2025-01-01',3000.00,'200-hour certified yoga instructor','active'),
(8,'Swimming','2024-06-15',3200.00,'Former competitive swimmer and certified swim coach','active'),
(14,'Personal Training','2025-03-01',4500.00,'NASM certified personal trainer','active');

-- TRAINER ASSIGNMENTS (20 records)
INSERT INTO trainer_assignments (trainer_id, member_id, assigned_date, end_date, notes) VALUES
(1,4,'2026-01-01','2026-04-01','Cardio focus program'),
(2,5,'2026-02-01','2026-05-01','Strength building'),
(3,6,'2025-06-01',NULL,'Ongoing yoga training'),
(1,7,'2026-03-01','2026-06-01','Beginner cardio'),
(2,8,'2026-01-15','2026-04-15','CrossFit intro'),
(4,9,'2026-02-10','2026-05-10','Swim training'),
(5,10,'2026-01-01','2026-04-01','Personal training'),
(1,11,'2026-03-01',NULL,'HIIT program'),
(3,12,'2025-04-01','2026-04-01','Advanced yoga'),
(2,13,'2026-03-15',NULL,'Weight training basics'),
(5,14,'2026-02-01','2026-05-01','Custom fitness plan'),
(4,15,'2026-01-01','2026-04-01','Swim coaching'),
(1,16,'2026-03-01',NULL,'Cardio conditioning'),
(3,19,'2026-02-01','2026-05-01','Flexibility program'),
(2,20,'2026-01-15','2026-04-15','Muscle building'),
(5,23,'2026-03-20',NULL,'General fitness'),
(1,24,'2026-01-01','2026-04-01','Endurance training'),
(4,25,'2026-02-15','2026-05-15','Water aerobics'),
(2,4,'2026-03-01',NULL,'Added CrossFit'),
(3,5,'2026-03-01',NULL,'Added yoga sessions');

-- ATTENDANCE (25 records)
INSERT INTO attendance (user_id, check_in, check_out, date, working_hours, marked_by) VALUES
(4,'2026-04-01 06:00:00','2026-04-01 08:00:00','2026-04-01',2.00,2),
(5,'2026-04-01 07:30:00','2026-04-01 09:00:00','2026-04-01',1.50,2),
(6,'2026-04-01 08:00:00','2026-04-01 10:30:00','2026-04-01',2.50,2),
(7,'2026-04-01 09:00:00','2026-04-01 10:00:00','2026-04-01',1.00,3),
(8,'2026-04-01 06:30:00','2026-04-01 08:30:00','2026-04-01',2.00,3),
(9,'2026-04-01 17:00:00','2026-04-01 19:00:00','2026-04-01',2.00,2),
(10,'2026-04-01 18:00:00','2026-04-01 20:00:00','2026-04-01',2.00,2),
(4,'2026-04-02 06:00:00','2026-04-02 08:15:00','2026-04-02',2.25,2),
(5,'2026-04-02 07:00:00','2026-04-02 09:00:00','2026-04-02',2.00,2),
(6,'2026-04-02 08:00:00','2026-04-02 10:00:00','2026-04-02',2.00,3),
(11,'2026-04-02 06:30:00','2026-04-02 08:00:00','2026-04-02',1.50,2),
(12,'2026-04-02 17:00:00','2026-04-02 19:30:00','2026-04-02',2.50,3),
(4,'2026-03-31 06:00:00','2026-03-31 08:00:00','2026-03-31',2.00,2),
(5,'2026-03-31 07:00:00','2026-03-31 08:30:00','2026-03-31',1.50,2),
(8,'2026-03-31 06:30:00','2026-03-31 09:00:00','2026-03-31',2.50,3),
(13,'2026-03-30 09:00:00','2026-03-30 11:00:00','2026-03-30',2.00,2),
(14,'2026-03-30 17:00:00','2026-03-30 19:00:00','2026-03-30',2.00,2),
(15,'2026-03-29 06:00:00','2026-03-29 07:30:00','2026-03-29',1.50,3),
(16,'2026-03-29 08:00:00','2026-03-29 10:00:00','2026-03-29',2.00,2),
(19,'2026-03-28 07:00:00','2026-03-28 09:00:00','2026-03-28',2.00,2),
(20,'2026-03-28 17:00:00','2026-03-28 19:30:00','2026-03-28',2.50,3),
(23,'2026-03-27 06:30:00','2026-03-27 08:30:00','2026-03-27',2.00,2),
(24,'2026-03-27 08:00:00','2026-03-27 10:00:00','2026-03-27',2.00,2),
(25,'2026-03-26 07:00:00','2026-03-26 09:00:00','2026-03-26',2.00,3),
(4,'2026-03-25 06:00:00','2026-03-25 08:00:00','2026-03-25',2.00,2);
