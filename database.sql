-- =====================================================
-- Vehicle Record System - Database File
-- Import this in phpMyAdmin (XAMPP or InfinityFree).
--
-- XAMPP: create a database named `vehicle_record_db` first,
--        select it, then import this file.
-- InfinityFree: select the database you created in the
--        Control Panel (e.g. if0_12345678_vehicles), then import.
-- =====================================================

DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS vehicle_types;

-- Lookup table (one type -> many vehicles)
CREATE TABLE vehicle_types (
    type_id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    type_name VARCHAR(50)  NOT NULL,
    PRIMARY KEY (type_id),
    UNIQUE KEY uq_type_name (type_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Main table (12 fields)
CREATE TABLE vehicles (
    vehicle_id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    plate_number        VARCHAR(15)  NOT NULL,
    type_id             INT UNSIGNED NOT NULL,
    brand               VARCHAR(50)  NOT NULL,
    model               VARCHAR(50)  NOT NULL,
    year_model          SMALLINT UNSIGNED NOT NULL,
    color               VARCHAR(30)  NOT NULL,
    owner_name          VARCHAR(100) NOT NULL,
    contact_number      VARCHAR(20)  NOT NULL,
    registration_expiry DATE         NOT NULL,
    status              ENUM('Active','Inactive','Under Repair','Sold') NOT NULL DEFAULT 'Active',
    remarks             TEXT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (vehicle_id),
    UNIQUE KEY uq_plate (plate_number),
    KEY idx_status (status),
    CONSTRAINT fk_vehicle_type FOREIGN KEY (type_id)
        REFERENCES vehicle_types (type_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO vehicle_types (type_name) VALUES
('Car'), ('Motorcycle'), ('SUV'), ('Van'), ('Pickup Truck'), ('Truck'), ('Tricycle'), ('Bus');

INSERT INTO vehicles
(plate_number, type_id, brand, model, year_model, color, owner_name, contact_number, registration_expiry, status, remarks) VALUES
('ABC 1234', 1, 'Toyota',   'Vios',     2021, 'White',  'Juan Dela Cruz', '09171234567', '2027-03-15', 'Active',       'Family car'),
('XYZ 5678', 2, 'Honda',    'Click 125',2022, 'Red',    'Maria Santos',   '09181234567', '2026-12-05', 'Active',       NULL),
('DEF 4321', 3, 'Mitsubishi','Montero Sport',2019,'Black','Pedro Reyes',   '09191234567', '2026-09-30', 'Under Repair', 'Brake system check'),
('GHI 8765', 4, 'Toyota',   'Hiace',    2018, 'Silver', 'Ana Villanueva', '09201234567', '2026-11-28', 'Active',       'Passenger van'),
('JKL 2468', 5, 'Ford',     'Ranger',   2020, 'Blue',   'Carlos Garcia',  '09211234567', '2027-06-20', 'Active',       NULL),
('MNO 1357', 2, 'Yamaha',   'NMAX',     2023, 'Gray',   'Liza Ramos',     '09221234567', '2026-05-10', 'Inactive',     'Registration lapsed'),
('PQR 9753', 1, 'Honda',    'City',     2017, 'Maroon', 'Mark Bautista',  '09231234567', '2027-01-12', 'Sold',         'Sold October 2026'),
('STU 8642', 7, 'Bajaj',    'RE 205',   2016, 'Yellow', 'Rodel Mendoza',  '09241234567', '2026-12-20', 'Active',       'Tricycle for hire');
