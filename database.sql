-- ============================================
-- BrightPath Academy - Complete Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS brightpath_academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE brightpath_academy;

-- =====================
-- ADMINS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('superadmin', 'admin', 'faculty') DEFAULT 'admin',
    profile_pic VARCHAR(255) DEFAULT NULL,
    last_login DATETIME DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- STUDENTS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    class_level VARCHAR(20) DEFAULT NULL,
    parent_name VARCHAR(100) DEFAULT NULL,
    parent_phone VARCHAR(15) DEFAULT NULL,
    dob DATE DEFAULT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    address TEXT DEFAULT NULL,
    school_name VARCHAR(200) DEFAULT NULL,
    profile_pic VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- COURSES TABLE
-- =====================
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    short_desc VARCHAR(300),
    class_level VARCHAR(50),
    duration VARCHAR(50),
    fee DECIMAL(10,2) DEFAULT 0,
    batch_timing VARCHAR(300),
    instructor VARCHAR(100),
    instructor_qualification VARCHAR(200),
    seats INT DEFAULT 30,
    image VARCHAR(255) DEFAULT NULL,
    features TEXT,
    syllabus TEXT,
    is_popular BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- ENROLLMENTS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date DATE NOT NULL,
    batch_timing VARCHAR(200) DEFAULT NULL,
    payment_status ENUM('pending', 'paid', 'partial') DEFAULT 'pending',
    amount_paid DECIMAL(10,2) DEFAULT 0,
    fee_due DECIMAL(10,2) DEFAULT 0,
    status ENUM('active', 'completed', 'dropped') DEFAULT 'active',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- =====================
-- RESULTS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    exam_name VARCHAR(200) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    marks_obtained INT NOT NULL,
    total_marks INT NOT NULL,
    percentage DECIMAL(5,2) AS (ROUND((marks_obtained/total_marks)*100, 2)) STORED,
    grade VARCHAR(5),
    rank_in_class INT DEFAULT NULL,
    exam_date DATE NOT NULL,
    remarks VARCHAR(300) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- =====================
-- TOPPERS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS toppers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_name VARCHAR(100) NOT NULL,
    achievement VARCHAR(200) NOT NULL,
    exam_name VARCHAR(200),
    score VARCHAR(100),
    year VARCHAR(10),
    image VARCHAR(255) DEFAULT NULL,
    class_level VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- TESTIMONIALS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_name VARCHAR(100) NOT NULL,
    class_passed VARCHAR(50),
    achievement VARCHAR(200),
    message TEXT NOT NULL,
    rating INT DEFAULT 5,
    image VARCHAR(255) DEFAULT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- ANNOUNCEMENTS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('notice', 'event', 'result', 'holiday', 'exam', 'admission') DEFAULT 'notice',
    is_important BOOLEAN DEFAULT FALSE,
    expiry_date DATE DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- GALLERY TABLE
-- =====================
CREATE TABLE IF NOT EXISTS gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200),
    image VARCHAR(255) NOT NULL,
    category VARCHAR(100) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- CONTACTS TABLE
-- =====================
CREATE TABLE IF NOT EXISTS contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) DEFAULT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    reply TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- ADMISSION INQUIRIES
-- =====================
CREATE TABLE IF NOT EXISTS admission_inquiries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_name VARCHAR(100) NOT NULL,
    parent_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    class_level VARCHAR(20) NOT NULL,
    course_id INT DEFAULT NULL,
    school_name VARCHAR(200) DEFAULT NULL,
    message TEXT DEFAULT NULL,
    status ENUM('new', 'contacted', 'enrolled', 'rejected') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- NEWSLETTER TABLE
-- =====================
CREATE TABLE IF NOT EXISTS newsletter (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- SAMPLE DATA
-- ============================================

-- Default Admin (password: Admin@123)
INSERT INTO admins (name, email, password, role) VALUES
('Rahul Sharma', 'admin@brightpath.com', '$2y$10$I8FyAp.oB/a11EA/DBVN7ef/Qc9IY6Dd.xWAgzMtreFpqRv5Rz0/q', 'superadmin');

-- Sample Courses
INSERT INTO courses (title, slug, category, description, short_desc, class_level, duration, fee, batch_timing, instructor, instructor_qualification, seats, features, is_popular, status) VALUES
('Mathematics Foundation', 'mathematics-foundation', 'Mathematics', 'Comprehensive mathematics course for students of classes 6-8. Our curriculum builds strong fundamentals in algebra, geometry, number systems, and arithmetic. Regular tests and doubt clearing sessions ensure thorough understanding.', 'Build strong math foundations for Class 6-8', '6-8', '1 Year', 9000.00, 'Mon/Wed/Fri: 4:00 PM - 5:30 PM', 'Mr. Rajesh Kumar', 'M.Sc. Mathematics, IIT Delhi', 35, 'NCERT Complete Coverage,Weekly Tests,Study Material Included,Doubt Clearing Sessions,Online Support,Parent Progress Reports', FALSE, 'active'),

('Mathematics Advanced (9-10)', 'mathematics-advanced-9-10', 'Mathematics', 'Advanced level mathematics for Class 9 and 10 students following CBSE/ICSE curriculum. Covers quadratic equations, coordinate geometry, trigonometry, statistics, probability and all NCERT topics with board exam preparation.', 'Master Class 9-10 Math with board exam prep', '9-10', '1 Year', 12000.00, 'Mon/Wed/Fri: 5:30 PM - 7:00 PM', 'Mr. Rajesh Kumar', 'M.Sc. Mathematics, IIT Delhi', 30, 'Complete NCERT + NCERT Exemplar,Board Pattern Mock Tests,Previous Year Papers,Chapter-wise Assignments,Personalized Doubt Sessions,Online Tests', TRUE, 'active'),

('Physics (11-12 + JEE)', 'physics-11-12-jee', 'Physics', 'In-depth physics course for Class 11 & 12 students targeting board exams and JEE/NEET preparation. All topics from mechanics to modern physics with problem-solving techniques for competitive exams.', 'Physics mastery for Class 11-12 and JEE/NEET', '11-12', '2 Years', 18000.00, 'Tue/Thu/Sat: 4:00 PM - 6:00 PM', 'Dr. Amit Verma', 'Ph.D. Physics, BHU | Ex-IIT Faculty', 25, 'Complete NCERT + Advanced Topics,JEE/NEET Problem Banks,Weekly Mock Tests,Numerical Problem Sessions,Formula Sheets & Notes,Video Lecture Access', TRUE, 'active'),

('Chemistry (11-12 + JEE/NEET)', 'chemistry-11-12-jee-neet', 'Chemistry', 'Complete chemistry course covering physical, organic and inorganic chemistry for Class 11 & 12. Designed for both board exams and competitive entrance tests like JEE Main, JEE Advanced and NEET.', 'Complete Chemistry for boards + JEE/NEET', '11-12', '2 Years', 18000.00, 'Tue/Thu/Sat: 6:00 PM - 8:00 PM', 'Mrs. Priya Singh', 'M.Sc. Chemistry, Delhi University', 25, 'Physical + Organic + Inorganic,Reaction Mechanisms Deep Dive,JEE/NEET Previous Papers,Lab Practical Guidance,Mnemonics & Shortcuts,Rapid Revision Course', TRUE, 'active'),

('Biology (11-12 + NEET)', 'biology-11-12-neet', 'Biology', 'Comprehensive biology course for Class 11 & 12 students aspiring for NEET. Covers all chapters of botany and zoology with special focus on NEET exam pattern, MCQ practice and diagram-based questions.', 'Biology for Class 11-12 and NEET aspirants', '11-12', '2 Years', 16000.00, 'Mon/Wed/Fri: 6:00 PM - 7:30 PM', 'Dr. Sunita Rao', 'MBBS, Pursuing MD | NEET Expert', 25, 'Complete NCERT Botany & Zoology,NEET Pattern MCQs,Diagram Practice Sessions,Biology Flashcards,Monthly Full Syllabus Tests,NEET Rank Boosting Strategies', FALSE, 'active'),

('English Communication', 'english-communication', 'English', 'Spoken English, grammar, and writing skills enhancement for school students (Classes 6-12). Covers grammar rules, essay writing, letter writing, comprehension, and verbal communication skills.', 'Spoken English & writing skills for all classes', '6-12', '6 Months', 6000.00, 'Sat/Sun: 10:00 AM - 11:30 AM', 'Ms. Kavya Nair', 'MA English, CTEFL Certified', 30, 'Grammar Foundation to Advanced,Spoken English Practice,Creative Writing & Essays,Vocabulary Building,Public Speaking Skills,Personality Development', FALSE, 'active'),

('Computer Science (11-12)', 'computer-science-11-12', 'Computer Science', 'Complete computer science course for Class 11 & 12 students. Covers Python programming, data structures, databases, networking, and all CBSE CS board exam topics. Hands-on coding practice included.', 'Python, CS concepts for Class 11-12 boards', '11-12', '1 Year', 14000.00, 'Sat/Sun: 2:00 PM - 4:00 PM', 'Mr. Arjun Mehta', 'B.Tech CSE, IIT Bombay | 8 Years Exp.', 20, 'Python Programming from Scratch,Data Structures & Algorithms,DBMS with MySQL,Networking Concepts,Board Exam Pattern,Practical File Guidance,Live Coding Sessions', FALSE, 'active'),

('JEE Foundation (8-10)', 'jee-foundation-8-10', 'Competitive Exam', 'Early preparation course for JEE aspirants in classes 8-10. Builds strong conceptual understanding of Math, Physics and Chemistry to give students a head start for JEE preparation.', 'Early JEE preparation for Class 8-10 students', '8-10', '2 Years', 20000.00, 'Mon/Wed/Fri: 6:30 PM - 8:30 PM', 'Dr. Amit Verma', 'Ph.D. Physics | JEE Expert', 20, 'Math + Physics + Chemistry Combined,Olympiad Preparation,Logical Reasoning,IQ & Aptitude Building,Regular Assessments,Parent-Faculty Meetings', TRUE, 'active');

-- Sample Testimonials
INSERT INTO testimonials (student_name, class_passed, achievement, message, rating, is_featured, status) VALUES
('Ananya Sharma', 'Class 12 CBSE', 'Scored 96% in Boards, Cleared JEE Main', 'BrightPath Academy changed my life! The teachers here are incredibly dedicated. I used to struggle with Physics and Chemistry, but after joining this academy, both became my strongest subjects. I cleared JEE Main and scored 96% in boards. Forever grateful!', 5, TRUE, 'active'),
('Rohan Gupta', 'Class 10 CBSE', 'Scored 98% in Class 10 Boards', 'I joined BrightPath for Math and Science coaching in Class 9. The way Mr. Rajesh explains concepts is outstanding. I scored 98% in my Class 10 boards and my parents are so proud. Highly recommend this academy to every student.', 5, TRUE, 'active'),
('Priya Patel', 'Class 12 CBSE', 'NEET Qualified, Pursuing MBBS', 'The Biology and Chemistry coaching at BrightPath is top-notch. Dr. Sunita ma''am knows exactly what NEET needs. With her guidance and the mock tests here, I qualified NEET and got admission to a government medical college. Dreams do come true!', 5, TRUE, 'active'),
('Aditya Kumar', 'Class 10 ICSE', 'Scored 95% in ICSE Boards', 'BrightPath has brilliant teachers who make every concept crystal clear. The study material provided here is excellent and the regular tests kept me prepared. I scored 95% in ICSE and I owe a big part of my success to this academy.', 4, TRUE, 'active'),
('Neha Singh', 'Class 12 CBSE', 'Secured Rank in JEE Advanced', 'Joining BrightPath for JEE preparation was the best decision. The problem-solving approach taught here is amazing. Faculty is always available for doubt sessions even on weekends. I secured a good rank in JEE Advanced!', 5, TRUE, 'active'),
('Vikram Tiwari', 'Class 8', 'Won State Level Math Olympiad', 'I joined the foundation course here in Class 6. The way they build concepts from scratch helped me not just in school but I won the State Level Mathematics Olympiad! The academy truly nurtures talent.', 5, FALSE, 'active');

-- Sample Announcements
INSERT INTO announcements (title, content, type, is_important, expiry_date, status) VALUES
('Admission Open for 2025-26 Batch', 'We are excited to announce that admissions are now open for the academic year 2025-26. Early bird discount of 10% available for registrations before March 31st. Limited seats available. Contact us or fill the online admission form today!', 'admission', TRUE, '2025-03-31', 'active'),
('Class 10 & 12 Board Exam Preparation Camp', 'Special 15-day intensive board exam preparation camp starting March 15th for Class 10 and 12 students. Extra classes on weekends, full syllabus revision, and mock tests. Compulsory for all enrolled students.', 'exam', TRUE, '2025-03-30', 'active'),
('Monthly Test Results Announced', 'Results of the February monthly assessment test are now available in the student dashboard. Students can also collect their answer sheets from the office. Top performers will be awarded in the upcoming ceremony.', 'result', FALSE, NULL, 'active'),
('Holi Holiday Notice', 'The academy will remain closed on March 13th (Thursday) on account of Holi festival. Classes will resume normally from March 14th. Wishing all students and families a very happy and colorful Holi!', 'holiday', FALSE, '2025-03-14', 'active'),
('Free Demo Classes Available', 'New students can now register for FREE demo classes for any course. This is a great opportunity to experience our teaching methodology before enrollment. Register through the website or call our office.', 'notice', FALSE, '2025-03-31', 'active'),
('Annual Prize Distribution Ceremony', 'BrightPath Academy''s Annual Prize Distribution Ceremony will be held on March 25th at 5:00 PM at the Town Hall. All students and parents are cordially invited. Toppers and achievers will be felicitated.', 'event', TRUE, '2025-03-25', 'active');

-- Sample Toppers
INSERT INTO toppers (student_name, achievement, exam_name, score, year, class_level) VALUES
('Ananya Sharma', 'JEE Main Qualified + 96% in CBSE Boards', 'JEE Main 2024', '99.2 Percentile', '2024', 'Class 12'),
('Rohan Gupta', 'CBSE Class 10 Board Topper', 'CBSE Board 2024', '98%', '2024', 'Class 10'),
('Priya Patel', 'NEET 2024 Qualified', 'NEET UG 2024', 'AIR 1245', '2024', 'Class 12'),
('Aditya Kumar', 'ICSE Class 10 District Topper', 'ICSE Board 2024', '95.6%', '2024', 'Class 10'),
('Meera Joshi', 'Class 12 Science Topper - School', 'CBSE Board 2024', '97%', '2024', 'Class 12'),
('Yash Malhotra', 'JEE Advanced Qualified', 'JEE Advanced 2024', 'AIR 3420', '2024', 'Class 12');

-- Create indexes for performance
CREATE INDEX idx_students_student_id ON students(student_id);
CREATE INDEX idx_students_email ON students(email);
CREATE INDEX idx_enrollments_student ON enrollments(student_id);
CREATE INDEX idx_results_student ON results(student_id);
CREATE INDEX idx_announcements_status ON announcements(status);
CREATE INDEX idx_courses_status ON courses(status);
