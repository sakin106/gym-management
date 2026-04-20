-- ============================================
-- SAMPLE DATA PART 2 — Remaining Tables
-- ============================================
USE gym_db;

-- CLASSES (8 records)
INSERT INTO classes (class_name, trainer_id, schedule_day, start_time, end_time, capacity, room, status) VALUES
('Morning Yoga','3','Mon','06:00:00','07:00:00',20,'Room A','active'),
('HIIT Blast','1','Tue','07:00:00','08:00:00',15,'Room B','active'),
('CrossFit Basics','2','Wed','08:00:00','09:00:00',12,'CrossFit Zone','active'),
('Swim Cardio','4','Thu','06:30:00','07:30:00',10,'Pool Area','active'),
('Evening Yoga','3','Fri','18:00:00','19:00:00',20,'Room A','active'),
('Zumba Dance','1','Sat','09:00:00','10:00:00',25,'Room C','active'),
('Strength Circuit','2','Mon','17:00:00','18:00:00',15,'Weight Zone','active'),
('Aerobics','5','Wed','18:00:00','19:00:00',20,'Room C','active');

-- CLASS ENROLLMENTS (20 records)
INSERT INTO class_enrollments (class_id, user_id, enrolled_date, status) VALUES
(1,4,'2026-01-15','enrolled'),(1,5,'2026-01-20','enrolled'),(1,7,'2026-02-01','enrolled'),
(1,9,'2026-02-10','enrolled'),(1,11,'2026-03-01','enrolled'),(2,4,'2026-01-15','enrolled'),
(2,6,'2026-02-01','enrolled'),(2,8,'2026-01-20','enrolled'),(2,10,'2026-02-15','enrolled'),
(3,5,'2026-02-01','enrolled'),(3,8,'2026-01-20','enrolled'),(3,14,'2026-02-15','enrolled'),
(4,9,'2026-02-10','enrolled'),(4,15,'2026-01-15','enrolled'),(5,7,'2026-03-01','enrolled'),
(5,12,'2026-02-01','enrolled'),(6,4,'2026-01-15','enrolled'),(6,13,'2026-03-15','enrolled'),
(7,6,'2026-02-01','enrolled'),(8,10,'2026-02-15','enrolled');

-- WORKOUT PLANS (6 records)
INSERT INTO workout_plans (member_id, trainer_id, plan_name, goal, start_date, end_date) VALUES
(4,1,'Cardio Burn 30','Lose 5kg in 30 days with cardio','2026-01-01','2026-02-01'),
(5,2,'Strength Builder','Build upper body strength','2026-02-01','2026-05-01'),
(6,3,'Yoga Flexibility','Improve flexibility and balance','2025-06-01',NULL),
(8,2,'CrossFit Starter','Introduction to CrossFit movements','2026-01-15','2026-04-15'),
(10,5,'Total Fitness','Overall fitness improvement','2026-01-01','2026-04-01'),
(14,5,'Muscle Gain','Gain 3kg lean muscle mass','2026-02-01','2026-05-01');

-- WORKOUT EXERCISES (25 records)
INSERT INTO workout_exercises (plan_id, exercise_name, sets, reps, duration_minutes, day_of_week, notes) VALUES
(1,'Treadmill Run',1,1,30,'Mon','Moderate pace'),(1,'Cycling',1,1,20,'Mon','High resistance'),
(1,'Jump Rope',3,50,NULL,'Wed','30 sec rest between'),(1,'Rowing Machine',1,1,25,'Wed',NULL),
(1,'Elliptical',1,1,30,'Fri','Incline intervals'),(2,'Bench Press',4,10,NULL,'Mon','Increase weight weekly'),
(2,'Shoulder Press',3,12,NULL,'Mon',NULL),(2,'Deadlift',4,8,NULL,'Wed','Focus on form'),
(2,'Barbell Row',3,10,NULL,'Wed',NULL),(2,'Pull-ups',3,8,NULL,'Fri','Use assisted if needed'),
(2,'Bicep Curls',3,12,NULL,'Fri',NULL),(3,'Sun Salutation',1,10,NULL,'Mon','Flow sequence'),
(3,'Warrior Poses',1,1,15,'Mon',NULL),(3,'Tree Pose',3,1,2,'Wed','Hold 30 seconds'),
(3,'Downward Dog',3,1,2,'Wed',NULL),(3,'Pigeon Pose',2,1,3,'Fri','Deep stretch'),
(4,'Box Jumps',3,10,NULL,'Tue',NULL),(4,'Kettlebell Swings',3,15,NULL,'Tue',NULL),
(4,'Wall Balls',3,12,NULL,'Thu',NULL),(4,'Burpees',3,10,NULL,'Thu','Full extension'),
(5,'Treadmill',1,1,20,'Mon','Warm up'),(5,'Squats',4,12,NULL,'Mon',NULL),
(5,'Lunges',3,10,NULL,'Wed','Each leg'),(5,'Plank',3,1,1,'Wed','Hold 60 seconds'),
(6,'Leg Press',4,10,NULL,'Mon','Heavy weight');

-- DIET PLANS (5 records)
INSERT INTO diet_plans (member_id, trainer_id, plan_name, calorie_target, start_date, end_date) VALUES
(4,1,'Fat Loss Diet',1800,'2026-01-01','2026-02-01'),
(5,2,'Muscle Gain Diet',2800,'2026-02-01','2026-05-01'),
(8,2,'CrossFit Nutrition',2500,'2026-01-15','2026-04-15'),
(10,5,'Balanced Diet',2200,'2026-01-01','2026-04-01'),
(14,5,'High Protein',2600,'2026-02-01','2026-05-01');

-- DIET ITEMS (20 records)
INSERT INTO diet_items (diet_id, meal_type, food_name, quantity, calories, protein, carbs, fat) VALUES
(1,'breakfast','Oatmeal with berries','1 bowl',300,10.00,50.00,6.00),
(1,'lunch','Grilled chicken salad','1 plate',450,35.00,20.00,15.00),
(1,'dinner','Baked salmon with veggies','200g',500,40.00,15.00,20.00),
(1,'snack','Greek yogurt','1 cup',150,12.00,18.00,3.00),
(2,'breakfast','Eggs and toast','4 eggs + 2 toast',550,30.00,40.00,25.00),
(2,'lunch','Brown rice with chicken','1 plate',700,45.00,80.00,12.00),
(2,'dinner','Steak with sweet potato','250g',750,50.00,45.00,30.00),
(2,'snack','Protein shake','1 scoop',200,25.00,10.00,5.00),
(3,'breakfast','Smoothie bowl','1 bowl',400,15.00,60.00,10.00),
(3,'lunch','Turkey wrap','1 wrap',500,30.00,45.00,18.00),
(3,'dinner','Grilled fish with quinoa','200g',550,38.00,40.00,16.00),
(3,'snack','Mixed nuts','50g',300,8.00,12.00,25.00),
(4,'breakfast','Avocado toast','2 slices',350,10.00,35.00,18.00),
(4,'lunch','Pasta with lean beef','1 plate',600,35.00,70.00,15.00),
(4,'dinner','Chicken stir-fry','1 plate',500,30.00,40.00,15.00),
(4,'snack','Apple with peanut butter','1 apple',250,6.00,30.00,12.00),
(5,'breakfast','Egg white omelette','6 whites',250,30.00,5.00,8.00),
(5,'lunch','Chicken breast with rice','300g',650,50.00,60.00,10.00),
(5,'dinner','Lean beef with broccoli','250g',550,45.00,15.00,20.00),
(5,'snack','Cottage cheese','1 cup',200,28.00,8.00,5.00);

-- CUSTOMER PROGRESS (20 records)
INSERT INTO customer_progress (user_id, recorded_date, initial_weight, current_weight, initial_body_type, current_body_type, progress_pct, notes) VALUES
(4,'2026-01-15',85.00,82.50,'Overweight','Average',2.94,'Good start'),
(4,'2026-02-15',85.00,80.00,'Overweight','Average',5.88,'On track'),
(4,'2026-03-15',85.00,78.00,'Overweight','Fit',8.24,'Great progress'),
(5,'2026-02-15',60.00,62.00,'Slim','Athletic',-3.33,'Gaining muscle'),
(5,'2026-03-15',60.00,63.50,'Slim','Athletic',-5.83,'Good gains'),
(6,'2026-01-01',75.00,73.00,'Average','Fit',2.67,'Steady'),
(6,'2026-03-01',75.00,71.50,'Average','Fit',4.67,'Improving'),
(7,'2026-03-15',90.00,88.00,'Obese','Overweight',2.22,'Starting out'),
(8,'2026-02-01',78.00,76.00,'Average','Athletic',2.56,'CrossFit helping'),
(8,'2026-03-01',78.00,74.50,'Average','Athletic',4.49,'Leaning out'),
(9,'2026-03-01',70.00,68.50,'Average','Fit',2.14,'Swim training'),
(10,'2026-02-01',95.00,91.00,'Overweight','Average',4.21,'Good effort'),
(10,'2026-03-01',95.00,88.00,'Overweight','Average',7.37,'Excellent'),
(11,'2026-03-15',65.00,63.00,'Slim','Fit',3.08,'Toning up'),
(12,'2026-03-01',88.00,85.00,'Overweight','Average',3.41,'Steady loss'),
(13,'2026-03-20',72.00,71.00,'Average','Fit',1.39,'Just started'),
(14,'2026-03-01',80.00,82.00,'Athletic','Muscular',-2.50,'Muscle gain'),
(15,'2026-02-15',68.00,66.00,'Average','Fit',2.94,'Swimming helping'),
(16,'2026-03-15',92.00,89.00,'Overweight','Average',3.26,'Progressing'),
(19,'2026-03-01',74.00,72.50,'Average','Fit',2.03,'Yoga flexibility');

-- TODO LIST (20 records)
INSERT INTO todo_list (user_id, task, due_date, status) VALUES
(4,'Complete 30-day cardio challenge','2026-04-15','pending'),
(4,'Buy new running shoes','2026-04-10','done'),
(4,'Schedule body composition test','2026-04-20','pending'),
(5,'Practice deadlift form','2026-04-05','pending'),
(5,'Meet trainer for progress review','2026-04-08','pending'),
(6,'Attend yoga retreat registration','2026-04-12','pending'),
(7,'Start meal prep routine','2026-04-03','pending'),
(8,'Register for CrossFit competition','2026-04-18','pending'),
(8,'Buy CrossFit gloves','2026-04-05','done'),
(9,'Sign up for swim meet','2026-04-20','pending'),
(10,'Track daily calorie intake','2026-04-01','pending'),
(10,'Buy resistance bands','2026-04-07','done'),
(11,'Try morning yoga class','2026-04-06','pending'),
(12,'Research protein supplements','2026-04-10','pending'),
(13,'Complete first 5K run','2026-04-25','pending'),
(14,'Update workout journal','2026-04-04','pending'),
(15,'Practice butterfly stroke','2026-04-15','pending'),
(16,'Reduce sugar intake this week','2026-04-09','pending'),
(19,'Hold headstand for 30 seconds','2026-04-30','pending'),
(20,'Measure body fat percentage','2026-04-12','pending');

-- ANNOUNCEMENTS (8 records)
INSERT INTO announcements (title, message, posted_by, applied_date, status) VALUES
('Gym Hours Update','Starting April 5th, the gym will open at 5:00 AM on weekdays. Weekend hours remain 7:00 AM to 9:00 PM.',1,'2026-04-01','active'),
('New Zumba Class','We are excited to announce a new Saturday Zumba class at 9:00 AM in Room C! Sign up at the front desk.',1,'2026-03-28','active'),
('Equipment Maintenance Notice','The weight training zone will be closed for maintenance on April 10th from 2:00 PM to 6:00 PM.',1,'2026-03-25','active'),
('Summer Membership Discount','Get 20% off on quarterly and yearly plans using code SUMMER20. Valid June 1 - August 31.',1,'2026-03-20','active'),
('Annual Fitness Challenge','Join our annual fitness challenge starting May 1st. Prizes for top 3 performers!',1,'2026-03-15','active'),
('Holiday Closure','The gym will be closed on April 14th for the holiday. Happy holidays!',1,'2026-04-01','active'),
('New Personal Trainers','Welcome our two new certified personal trainers joining the team this month!',1,'2026-03-10','inactive'),
('Pool Maintenance Complete','The swimming pool has been cleaned and is now open for regular hours.',1,'2026-03-05','active');

-- NOTIFICATIONS (20 records)
INSERT INTO notifications (user_id, message, type, is_read, sent_by) VALUES
(4,'Welcome to GymPro! Your membership is now active.','info',1,1),
(4,'Your cardio trainer has been assigned. Check your workout plans.','info',1,1),
(4,'Great progress! You have lost 7kg so far. Keep it up!','info',0,1),
(5,'Your strength training program has been updated.','info',1,1),
(5,'New yoga class available on Fridays. Consider enrolling!','info',0,1),
(6,'Your yearly membership will expire in 60 days.','alert',0,1),
(7,'Welcome! Please complete your profile information.','info',1,1),
(8,'CrossFit class schedule has been updated.','info',1,1),
(9,'Your swimming schedule has been posted.','info',0,1),
(10,'Payment reminder: Your next payment is due soon.','payment_due',0,1),
(11,'New announcement: Check gym hours update.','info',0,2),
(12,'Your yoga plan has been extended.','info',1,1),
(13,'Welcome to GymPro! Start exploring your dashboard.','info',0,1),
(14,'Your muscle gain diet plan is ready. Check diet section.','info',0,1),
(15,'Swim meet registration is open. Check announcements.','info',0,2),
(16,'Your trainer has updated your workout plan.','info',1,1),
(17,'Your registration is pending approval.','alert',0,1),
(18,'Your registration is pending approval.','alert',0,1),
(19,'Your flexibility program starts next week.','info',0,1),
(20,'Your membership has expired. Please renew.','payment_due',0,1);

-- EQUIPMENT (20 records)
INSERT INTO equipment (name, description, category, quantity, purchase_date, vendor_name, vendor_contact, unit_cost, total_cost, condition_status, zone) VALUES
('Treadmill Pro X500','Commercial treadmill with incline','Cardio',8,'2024-01-15','FitEquip Co','555-0101',2500.00,20000.00,'good','Cardio Zone'),
('Stationary Bike V3','Adjustable resistance cycling','Cardio',6,'2024-01-15','FitEquip Co','555-0101',1800.00,10800.00,'good','Cardio Zone'),
('Elliptical E200','Low-impact cardio machine','Cardio',4,'2024-03-01','GymGear Ltd','555-0102',2200.00,8800.00,'good','Cardio Zone'),
('Rowing Machine R1','Air resistance rower','Cardio',3,'2024-06-15','FitEquip Co','555-0101',1500.00,4500.00,'fair','Cardio Zone'),
('Flat Bench','Adjustable weight bench','Strength',10,'2023-06-01','IronWorks','555-0103',400.00,4000.00,'good','Weight Zone'),
('Squat Rack','Power squat rack with safety','Strength',4,'2023-06-01','IronWorks','555-0103',1200.00,4800.00,'good','Weight Zone'),
('Dumbbell Set','5-50 lb dumbbell set','Strength',2,'2023-06-01','IronWorks','555-0103',3000.00,6000.00,'good','Weight Zone'),
('Cable Machine','Dual pulley cable system','Strength',3,'2024-01-01','GymGear Ltd','555-0102',2800.00,8400.00,'good','Weight Zone'),
('Leg Press','45-degree leg press','Strength',2,'2024-03-01','IronWorks','555-0103',2000.00,4000.00,'fair','Weight Zone'),
('Smith Machine','Guided barbell system','Strength',2,'2023-12-01','IronWorks','555-0103',3500.00,7000.00,'good','Weight Zone'),
('Kettlebell Set','10-50 lb set','CrossFit',3,'2024-06-01','CrossFitSupply','555-0104',800.00,2400.00,'good','CrossFit Zone'),
('Battle Ropes','50ft heavy ropes','CrossFit',4,'2024-06-01','CrossFitSupply','555-0104',150.00,600.00,'good','CrossFit Zone'),
('Pull-up Bar Station','Multi-grip pull-up station','CrossFit',2,'2024-06-01','CrossFitSupply','555-0104',600.00,1200.00,'good','CrossFit Zone'),
('Yoga Mats','Premium non-slip mats','Yoga',30,'2024-01-01','YogaWorld','555-0105',30.00,900.00,'good','Yoga Studio'),
('Yoga Blocks','Foam support blocks','Yoga',40,'2024-01-01','YogaWorld','555-0105',12.00,480.00,'good','Yoga Studio'),
('Resistance Bands Set','Light to heavy bands','Accessories',20,'2024-03-01','FitEquip Co','555-0101',25.00,500.00,'good','General'),
('Medicine Balls','5-20 lb set','Accessories',10,'2024-06-01','GymGear Ltd','555-0102',50.00,500.00,'fair','General'),
('TRX Suspension','TRX training system','Functional',6,'2024-09-01','CrossFitSupply','555-0104',200.00,1200.00,'good','CrossFit Zone'),
('Foam Rollers','High-density foam rollers','Recovery',15,'2024-01-01','YogaWorld','555-0105',20.00,300.00,'good','Stretching Area'),
('Boxing Bag','70lb heavy bag with stand','Cardio',3,'2025-01-01','FitEquip Co','555-0101',350.00,1050.00,'good','Cardio Zone');

-- EQUIPMENT MAINTENANCE (12 records)
INSERT INTO equipment_maintenance (equipment_id, maintenance_date, description, cost, performed_by) VALUES
(1,'2026-01-15','Belt replacement and lubrication',150.00,'TechFix Services'),
(1,'2026-03-15','Routine inspection and calibration',75.00,'In-house maintenance'),
(2,'2026-02-01','Pedal bearing replacement',100.00,'TechFix Services'),
(3,'2026-01-20','Display panel repair',200.00,'GymGear Support'),
(4,'2026-03-01','Chain and seat adjustment',50.00,'In-house maintenance'),
(5,'2026-02-15','Upholstery repair on 2 benches',120.00,'FurnitureFix'),
(6,'2025-12-01','Safety pin replacement',80.00,'IronWorks Support'),
(9,'2026-01-10','Hydraulic cylinder service',250.00,'TechFix Services'),
(10,'2026-02-20','Cable replacement',180.00,'IronWorks Support'),
(14,'2026-03-01','Deep cleaning - 30 mats',60.00,'In-house maintenance'),
(17,'2026-03-10','Replaced 3 damaged medicine balls',150.00,'GymGear Support'),
(20,'2026-02-15','Bag re-stuffing and chain check',90.00,'In-house maintenance');
