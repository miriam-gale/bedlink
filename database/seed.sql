SET FOREIGN_KEY_CHECKS = 0;

-- database/seed.sql (James) run AFTER schema.sql: phpMyAdmin > bedlink > Import[cite: 48, 112]
-- Safe to re-run: it empties the tables first[cite: 48, 112].

TRUNCATE audit_log;
TRUNCATE notifications;
TRUNCATE handovers;
TRUNCATE ambulance_locations;
TRUNCATE transfers;
TRUNCATE incidents;
TRUNCATE blood_stock;
TRUNCATE equipment_status;
TRUNCATE hospital_services;
TRUNCATE bed_updates;
TRUNCATE beds;
TRUNCATE wards;
TRUNCATE referrals;
TRUNCATE turnaways;
TRUNCATE donors;
TRUNCATE pregnancies;
TRUNCATE unidentified_patients;
TRUNCATE reviews;
TRUNCATE users;
TRUNCATE ward_types;
TRUNCATE hospitals;
TRUNCATE regions;

-- 1. The 16 regions of Ghana[cite: 49, 112]
INSERT INTO regions (region_id, name) VALUES
(1, 'Greater Accra'), (2, 'Ashanti'), (3, 'Central'), (4, 'Eastern'),
(5, 'Western'), (6, 'Western North'),
(7, 'Volta'), (8, 'Oti'), (9, 'Northern'), (10, 'Savannah'), (11, 'North East'),
(12, 'Upper East'),
(13, 'Upper West'), (14, 'Bono'), (15, 'Bono East'), (16, 'Ahafo');

-- 2. The 7 ward types[cite: 49, 112]
INSERT INTO ward_types (ward_type_id, code, name) VALUES
(1, 'emergency', 'Emergency'), (2, 'maternity', 'Maternity'), (3, 'icu', 'ICU'),
(4, 'general', 'General'),
(5, 'children', 'Children'), (6, 'nicu', 'Newborn ICU'), (7, 'labour', 'Labour');

-- 3. Hospitals: 11 in Greater Accra + 1 in Kumasi[cite: 49, 112]
INSERT INTO hospitals (hospital_id, name, region_id, town, phone, latitude, longitude, ownership, accepts_nhis, level, languages, is_approved) VALUES
(1, 'Korle Bu Teaching Hospital', 1, 'Korle Bu', '0302000001', 5.536700, -0.226500, 'government', 1, 'teaching', 'en,tw,gaa', 1),
(2, 'Greater Accra Regional Hospital (Ridge)', 1, 'Ridge', '0302000002', 5.562700, -0.198300, 'government', 1, 'regional', 'en,tw,gaa', 1),
(3, '37 Military Hospital', 1, 'Cantonments', '0302000003', 5.587500, -0.183600, 'government', 1, 'regional', 'en,tw', 1),
(4, 'La General Hospital', 1, 'La', '0302000004', 5.559600, -0.163100, 'government', 1, 'district', 'en,gaa', 1),
(5, 'Achimota Hospital', 1, 'Achimota', '0302000005', 5.622400, -0.226900, 'government', 1, 'district', 'en,tw', 1),
(6, 'Pentecost Hospital Madina', 1, 'Madina', '0302000006', 5.671200, -0.166200, 'mission', 1, 'district', 'en,tw', 1),
(7, 'Nyaho Medical Centre', 1, 'Airport Residential', '0302000007', 5.601200, -0.181000, 'private', 0, 'district', 'en', 1),
(8, 'Lister Hospital', 1, 'Cantonments', '0302000008', 5.642000, -0.143000, 'private', 0, 'district', 'en', 1),
(9, 'Tema General Hospital', 1, 'Tema', '0302000009', 5.669800, -0.016600, 'government', 1, 'district', 'en,gaa,ee', 1),
(10, 'Mamprobi Polyclinic', 1, 'Mamprobi', '0302000010', 5.537000, -0.242000, 'government', 1, 'clinic', 'en,gaa', 1),
(11, 'Ga East Municipal Hospital', 1, 'Kissieman', '0302000011', 5.688000, -0.208000, 'government', 1, 'district', 'en,tw,gaa', 1),
(12, 'Komfo Anokye Teaching Hospital', 2, 'Kumasi', '0322000012', 6.697000, -1.628000, 'government', 1, 'teaching', 'en,tw', 1);

-- Demo key for the records-system simulator (SIM-03): Korle Bu's key hash[cite: 49, 50]
UPDATE hospitals SET api_key_hash = '$2y$10$YGA2B9KrX9hdohrG.5tN10Nn6vX6y66rzmT2ZGuiTt5VhX0MSbVx2' WHERE hospital_id = 1;

-- 4. Wards: every hospital gets Emergency + General; larger ones get Maternity and ICU[cite: 50, 112]
INSERT INTO wards (hospital_id, ward_type_id, name)
SELECT h.hospital_id, wt.ward_type_id, wt.name
FROM hospitals h
JOIN ward_types wt ON wt.code IN ('emergency', 'general')
OR (wt.code = 'maternity' AND h.level IN ('teaching', 'regional', 'district') AND h.hospital_id % 2 = 0)
OR (wt.code = 'icu' AND h.level IN ('teaching', 'regional'));

-- 5. Beds: 8-20 per ward, mixed statuses, randomized recent update timestamps[cite: 50, 112]
INSERT INTO beds (ward_id, label, qr_code, status, updated_at)
SELECT w.ward_id,
       CONCAT('Bed', n.i),
       MD5(CONCAT('bedlink', w.ward_id, '-', n.i)),
       ELT(1 + FLOOR(RAND() * 6), 'free', 'free', 'occupied', 'occupied', 'occupied', 'cleaning'),
       NOW() - INTERVAL FLOOR(RAND() * 180) MINUTE
FROM wards w
JOIN (
    SELECT a.d + 10 * b.d + 1 AS i
    FROM (SELECT 0 AS d UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
    CROSS JOIN (SELECT 0 AS d UNION ALL SELECT 1) b
) n ON n.i <= 8 + (w.ward_id * 7) % 13;

-- 6. Services and equipment (what each hospital can do)[cite: 50, 112]
INSERT INTO hospital_services (hospital_id, service, available)
SELECT h.hospital_id, s.service, 1
FROM hospitals h
JOIN (
    SELECT 'theatre' AS service UNION ALL SELECT 'ct_scan' UNION ALL SELECT 'trauma_surgery'
    UNION ALL SELECT 'blood_bank' UNION ALL SELECT 'icu' UNION ALL SELECT 'obstetrics'
) s ON h.level IN ('teaching', 'regional') OR s.service IN ('theatre', 'obstetrics');

INSERT INTO equipment_status (hospital_id, kind, item, working, detail)
SELECT hospital_id, 'equipment', 'ct_scan', IF(hospital_id = 2, 0, 1), NULL -- Ridge's CT is "down" for the demo[cite: 50, 51, 112]
FROM hospitals WHERE level IN ('teaching', 'regional');

INSERT INTO equipment_status (hospital_id, kind, item, working, detail)
SELECT hospital_id, 'specialist', 'surgeon', 1, 'On call' FROM hospitals;

INSERT INTO equipment_status (hospital_id, kind, item, working, detail)
SELECT hospital_id, 'oxygen', 'cylinders', 1, '12 cylinders' FROM hospitals;

-- 7. Blood stock: all 8 types at every hospital with a blood bank[cite: 51, 112]
INSERT INTO blood_stock (hospital_id, blood_type, units, reserve_units)
SELECT hs.hospital_id, t.bt, FLOOR(RAND() * 15), 2
FROM hospital_services hs
JOIN (
    SELECT 'A+' AS bt UNION ALL SELECT 'A-' UNION ALL SELECT 'B+' UNION ALL SELECT 'B-'
    UNION ALL SELECT 'AB+' UNION ALL SELECT 'AB-' UNION ALL SELECT 'O+' UNION ALL SELECT 'O-'
) t WHERE hs.service = 'blood_bank';

-- 8. Test logins: one for each of the 13 roles, a 2nd nurse at Ridge (IDOR test), + crew1-crew20 (race test)[cite: 51, 112]
-- Every password is Test1234![cite: 51, 112]
SET @pw = '$2y$10$76G3C44oJDJUzQOI7PrZCO/hhFGzDe8Kq/iLEyg/HO5iJT1xkuJq2';

INSERT INTO users (full_name, email, phone, password_hash, role, hospital_id, region_id, unit_name, is_approved) VALUES
('Test User', 'user@test.com', '0240000001', @pw, 'registered_user', NULL, NULL, NULL, 1),
('Test Dispatcher', 'dispatcher@test.com', '0240000002', @pw, 'dispatcher', NULL, 1, NULL, 1),
('Test Driver', 'driver@test.com', '0240000003', @pw, 'community_driver', NULL, 1, NULL, 1),
('Nurse Korle Bu', 'nurse1@test.com', '0240000004', @pw, 'ward_nurse', 1, 1, NULL, 1),
('Nurse Ridge', 'nurse2@test.com', '0240000005', @pw, 'ward_nurse', 2, 1, NULL, 1),
('Dr Korle Bu', 'doctor@test.com', '0240000006', @pw, 'doctor', 1, 1, NULL, 1),
('Blood Bank KB', 'bloodbank@test.com', '0240000007', @pw, 'blood_bank', 1, 1, NULL, 1),
('Test Midwife', 'midwife@test.com', '0240000008', @pw, 'midwife', 1, 1, NULL, 1),
('Admin Korle Bu', 'hadmin@test.com', '0240000009', @pw, 'hospital_admin', 1, 1, NULL, 1),
('Coordinator GA', 'coordinator@test.com', '0240000010', @pw, 'coordinator', NULL, 1, NULL, 1),
('NBS Officer', 'nbs@test.com', '0240000011', @pw, 'nbs_officer', NULL, NULL, NULL, 1),
('Health Analyst', 'analyst@test.com', '0240000012', @pw, 'analyst', NULL, NULL, NULL, 1),
('System Admin', 'admin@test.com', '0240000013', @pw, 'system_admin', NULL, NULL, NULL, 1);

INSERT INTO users (full_name, email, phone, password_hash, role, unit_name, organisation, is_approved)
SELECT CONCAT('Crew', n.i), CONCAT('crew', n.i, '@test.com'), CONCAT('02500000', LPAD(n.i, 2, '0')),
       @pw, 'ambulance_crew', CONCAT('Unit', n.i), 'National Ambulance Service', 1
FROM (
    SELECT a.d + 10 * b.d + 1 AS i
    FROM (SELECT 0 AS d UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
    CROSS JOIN (SELECT 0 AS d UNION ALL SELECT 1) b
) n;

-- 9. Quick check (should show 12 hospitals, 16 regions, 33 users and a few hundred beds)[cite: 52]
SELECT 
    (SELECT COUNT(*) FROM hospitals) AS hospitals, 
    (SELECT COUNT(*) FROM regions) AS regions,
    (SELECT COUNT(*) FROM users) AS users, 
    (SELECT COUNT(*) FROM beds) AS beds;

SET FOREIGN_KEY_CHECKS = 1;