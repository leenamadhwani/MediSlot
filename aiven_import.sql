-- =====================================================================
-- MediSlot :: Doctor Appointment Booking System
-- Import this file in phpMyAdmin (or run via `mysql -u root -p < database.sql`)
-- =====================================================================



-- ---------------------------------------------------------------------
-- Table: users  (patients + admin login)
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    role ENUM('patient','admin') NOT NULL DEFAULT 'patient',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: doctors
-- ---------------------------------------------------------------------
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    qualification VARCHAR(150) DEFAULT NULL,
    experience_years INT DEFAULT 0,
    fee DECIMAL(8,2) DEFAULT 0.00,
    photo VARCHAR(255) DEFAULT NULL,
    available_days VARCHAR(100) DEFAULT 'Mon-Sat',
    slot_start TIME DEFAULT '09:00:00',
    slot_end TIME DEFAULT '17:00:00',
    bio TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Table: appointments
-- ---------------------------------------------------------------------
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Seed data
-- ---------------------------------------------------------------------

-- Default admin login -> email: admin@medislot.com | password: admin123
INSERT INTO users (name, email, password, phone, role) VALUES
('System Admin', 'admin@medislot.com', '$2y$10$Vd2Vg5MPz3aQe0eYQ8x9UejzB9y9nJZfN8t6BOAmM9x1qk4EoW.zW', '9999999999', 'admin');
-- NOTE: the hash above is generated with PHP password_hash('admin123', PASSWORD_DEFAULT)
-- If it does not match on your PHP build, just re-register an admin manually (see README step 7).

INSERT INTO doctors (name, specialization, qualification, experience_years, fee, available_days, slot_start, slot_end, bio) VALUES
('Dr. Ananya Sharma', 'Cardiologist', 'MBBS, MD (Cardiology)', 12, 800.00, 'Mon-Sat', '09:00:00', '14:00:00', 'Specialist in heart disease prevention, hypertension and interventional cardiology.'),
('Dr. Rohan Mehta', 'Dermatologist', 'MBBS, MD (Skin & VD)', 8, 600.00, 'Mon-Fri', '10:00:00', '17:00:00', 'Expert in acne, skin allergies, hair loss and cosmetic dermatology.'),
('Dr. Priya Nair', 'Pediatrician', 'MBBS, MD (Pediatrics)', 10, 500.00, 'Mon-Sat', '09:30:00', '15:30:00', 'Dedicated to child healthcare, vaccinations and growth monitoring.'),
('Dr. Karan Kapoor', 'Orthopedic', 'MBBS, MS (Ortho)', 15, 900.00, 'Tue-Sun', '11:00:00', '18:00:00', 'Focused on joint replacement, sports injuries and spine care.'),
('Dr. Sneha Iyer', 'Dentist', 'BDS, MDS', 6, 400.00, 'Mon-Sat', '09:00:00', '16:00:00', 'Provides painless dental care, root canal and cosmetic dentistry.'),
('Dr. Vikram Singh', 'Neurologist', 'MBBS, DM (Neurology)', 14, 1000.00, 'Mon-Fri', '10:00:00', '16:00:00', 'Treats migraine, epilepsy, stroke and other neurological disorders.');
