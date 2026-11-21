-- database/schema.sql (Hariel) - the 22 tables. Import FIRST, then James's seed.sql.
SET FOREIGN_KEY_CHECKS=1;

-- 1. regions
CREATE TABLE regions (
  region_id  TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(60) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. hospitals
CREATE TABLE hospitals (
  hospital_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(120) NOT NULL,
  region_id       TINYINT UNSIGNED NOT NULL,
  town            VARCHAR(80)  NOT NULL,
  address         VARCHAR(200) NULL,
  phone           VARCHAR(20)  NULL,
  latitude        DECIMAL(9,6) NOT NULL,
  longitude       DECIMAL(9,6) NOT NULL,
  ownership       ENUM('government','mission','private') NOT NULL,
  accepts_nhis    TINYINT(1) NOT NULL DEFAULT 1,
  level           ENUM('chps','clinic','district','regional','teaching') NOT NULL,
  languages       VARCHAR(60)  NOT NULL DEFAULT 'en',   -- e.g. 'en,tw,ga'
  insurers        VARCHAR(200) NULL,
  is_approved     TINYINT(1) NOT NULL DEFAULT 0,
  is_active       TINYINT(1) NOT NULL DEFAULT 1,
  is_diverting    TINYINT(1) NOT NULL DEFAULT 0,
  divert_reason   VARCHAR(100) NULL,
  divert_until    DATETIME NULL,
  api_key_hash    VARCHAR(255) NULL,                    -- hospital records-system key
  screen_key_hash VARCHAR(255) NULL,                    -- TV screen key
  created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_hosp_region FOREIGN KEY (region_id) REFERENCES regions(region_id) ON DELETE RESTRICT,
  INDEX idx_hosp_location (latitude, longitude),
  INDEX idx_hosp_filter   (is_approved, is_diverting, region_id)
) ENGINE=InnoDB;

-- 3. users
CREATE TABLE users (
  user_id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name            VARCHAR(100) NOT NULL,
  email                VARCHAR(120) NULL UNIQUE,         -- public accounts may use phone only
  phone                VARCHAR(20)  NOT NULL UNIQUE,
  password_hash        VARCHAR(255) NOT NULL,
  role                 ENUM('registered_user','ambulance_crew','dispatcher','community_driver',
                            'ward_nurse','doctor','blood_bank','midwife','hospital_admin',
                            'coordinator','nbs_officer','analyst','system_admin') NOT NULL,
  hospital_id          INT UNSIGNED NULL,                -- hospital roles only
  region_id            TINYINT UNSIGNED NULL,            -- coordinators only
  organisation         VARCHAR(120) NULL,                -- e.g. 'National Ambulance Service'
  unit_name            VARCHAR(40)  NULL,                -- crews, e.g. 'Unit 14'
  is_approved          TINYINT(1) NOT NULL DEFAULT 0,
  is_active            TINYINT(1) NOT NULL DEFAULT 1,
  must_change_password TINYINT(1) NOT NULL DEFAULT 0,
  failed_logins        TINYINT UNSIGNED NOT NULL DEFAULT 0,
  locked_until         DATETIME NULL,
  otp_hash             VARCHAR(255) NULL,                -- 2-step login code, hashed
  otp_expires_at       DATETIME NULL,
  language             VARCHAR(3) NOT NULL DEFAULT 'en',
  emergency_contacts   JSON NULL,                        -- [{"name":"...","phone":"..."}], max 3
  blood_group          ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NULL,
  conditions           VARCHAR(255) NULL,
  health_consent_at    DATETIME NULL,                    -- NULL = no health info may be stored
  helper_user_id       INT UNSIGNED NULL,
  last_login_at        DATETIME NULL,
  created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_user_hosp   FOREIGN KEY (hospital_id)    REFERENCES hospitals(hospital_id) ON DELETE RESTRICT,
  CONSTRAINT fk_user_region FOREIGN KEY (region_id)      REFERENCES regions(region_id)     ON DELETE RESTRICT,
  CONSTRAINT fk_user_helper FOREIGN KEY (helper_user_id) REFERENCES users(user_id)         ON DELETE SET NULL,
  CONSTRAINT chk_hosp_roles CHECK (role NOT IN ('ward_nurse','doctor','blood_bank','hospital_admin')
                                   OR hospital_id IS NOT NULL),
  INDEX idx_users_role (role, is_approved),
  INDEX idx_users_hosp (hospital_id)
) ENGINE=InnoDB;

-- 4. ward_types
CREATE TABLE ward_types (
  ward_type_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code         VARCHAR(20) NOT NULL UNIQUE,   -- 'emergency','maternity','icu','general','children','nicu','labour'
  name         VARCHAR(40) NOT NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5. wards
CREATE TABLE wards (
  ward_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  hospital_id  INT UNSIGNED NOT NULL,
  ward_type_id TINYINT UNSIGNED NOT NULL,
  name         VARCHAR(60) NOT NULL,
  phone        VARCHAR(20) NULL,
  is_active    TINYINT(1) NOT NULL DEFAULT 1,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_ward_hosp FOREIGN KEY (hospital_id)  REFERENCES hospitals(hospital_id)   ON DELETE CASCADE,
  CONSTRAINT fk_ward_type FOREIGN KEY (ward_type_id) REFERENCES ward_types(ward_type_id) ON DELETE RESTRICT,
  INDEX idx_ward_hosp_type (hospital_id, ward_type_id)
) ENGINE=InnoDB;

-- 6. beds
CREATE TABLE beds (
  bed_id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ward_id               INT UNSIGNED NOT NULL,
  label                 VARCHAR(20) NOT NULL,              -- 'Bed 4'
  qr_code               CHAR(32) NOT NULL UNIQUE,
  status                ENUM('free','occupied','cleaning','held') NOT NULL DEFAULT 'free',
  is_surge              TINYINT(1) NOT NULL DEFAULT 0,     -- seen only by coordinators
  is_active             TINYINT(1) NOT NULL DEFAULT 1,     -- 'removed' beds are switched off
  expected_discharge_at DATETIME NULL,
  updated_by            INT UNSIGNED NULL,
  created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_bed_ward FOREIGN KEY (ward_id)    REFERENCES wards(ward_id) ON DELETE CASCADE,
  CONSTRAINT fk_bed_user FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL,
  UNIQUE KEY uq_bed_label (ward_id, label),
  INDEX idx_bed_status (ward_id, status, is_surge, is_active)
) ENGINE=InnoDB;

-- 7. bed_updates (history of every status change)
CREATE TABLE bed_updates (
  update_id  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bed_id     INT UNSIGNED NOT NULL,
  old_status ENUM('free','occupied','cleaning','held') NOT NULL,
  new_status ENUM('free','occupied','cleaning','held') NOT NULL,
  source     ENUM('board','qr','shift','api','sms','hold','system') NOT NULL,
  updated_by INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_bu_bed  FOREIGN KEY (bed_id)     REFERENCES beds(bed_id)   ON DELETE CASCADE,
  CONSTRAINT fk_bu_user FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL,
  INDEX idx_bu_bed_time (bed_id, created_at)
) ENGINE=InnoDB;

-- 8. hospital_services
CREATE TABLE hospital_services (
  hospital_id INT UNSIGNED NOT NULL,
  service     VARCHAR(40) NOT NULL,      -- 'ct_scan','trauma_surgery','blood_bank','icu','nicu','obstetrics','theatre'
  available   TINYINT(1) NOT NULL DEFAULT 1,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (hospital_id, service),
  CONSTRAINT fk_hs_hosp FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id) ON DELETE CASCADE,
  INDEX idx_hs_service (service, available)
) ENGINE=InnoDB;

-- 9. equipment_status (equipment, specialists on duty, oxygen)
CREATE TABLE equipment_status (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  hospital_id INT UNSIGNED NOT NULL,
  kind        ENUM('equipment','specialist','oxygen') NOT NULL,
  item        VARCHAR(40) NOT NULL,      -- 'ct_scan','ventilator' / 'surgeon','obstetrician' / 'cylinders'
  working     TINYINT(1) NOT NULL DEFAULT 1,  -- for specialists: 1 = on duty
  detail      VARCHAR(100) NULL,         -- e.g. '12 cylinders' or 'Dr. Mensah'
  updated_by  INT UNSIGNED NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_eq_hosp FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id) ON DELETE CASCADE,
  CONSTRAINT fk_eq_user FOREIGN KEY (updated_by)  REFERENCES users(user_id)         ON DELETE SET NULL,
  UNIQUE KEY uq_eq_item (hospital_id, kind, item)
) ENGINE=InnoDB;

-- 10. incidents (Get help, bystander reports, major incidents)
CREATE TABLE incidents (
  incident_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type             ENUM('get_help','bystander','major') NOT NULL,
  reporter_user_id INT UNSIGNED NULL,
  reporter_phone   VARCHAR(20)  NULL,
  reporter_name    VARCHAR(100) NULL,
  reporter_ip      VARBINARY(16) NULL,          -- for the 5-per-hour limit
  latitude         DECIMAL(9,6) NOT NULL,
  longitude        DECIMAL(9,6) NOT NULL,
  people_hurt      TINYINT UNSIGNED NULL,
  answers          JSON NULL,                   -- {"awake":"no","bleeding":"yes"}
  status           ENUM('open','assigned','closed') NOT NULL DEFAULT 'open',
  assigned_crew_id INT UNSIGNED NULL,
  merged_into      INT UNSIGNED NULL,
  report_count     SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  escalated_at     DATETIME NULL,
  closed_reason    VARCHAR(200) NULL,
  created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_inc_reporter FOREIGN KEY (reporter_user_id) REFERENCES users(user_id)         ON DELETE SET NULL,
  CONSTRAINT fk_inc_crew     FOREIGN KEY (assigned_crew_id) REFERENCES users(user_id)         ON DELETE SET NULL,
  CONSTRAINT fk_inc_merge    FOREIGN KEY (merged_into)      REFERENCES incidents(incident_id) ON DELETE SET NULL,
  INDEX idx_inc_open (status, created_at),
  INDEX idx_inc_location (latitude, longitude)
) ENGINE=InnoDB;

-- 11. transfers (every hold and trip)
CREATE TABLE transfers (
  transfer_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type             ENUM('ambulance','referral','time_critical','mother_baby','community_ride','surge') NOT NULL,
  from_user_id     INT UNSIGNED NOT NULL,       -- the crew who held it
  driver_user_id   INT UNSIGNED NULL,
  to_hospital_id   INT UNSIGNED NOT NULL,
  bed_id           INT UNSIGNED NULL,
  blood_type       ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NULL,
  blood_units      TINYINT UNSIGNED NOT NULL DEFAULT 0,
  incident_id      INT UNSIGNED NULL,
  stage            ENUM('pending','accepted','in_transit','arrived',
                        'rejected','expired','cancelled','turned_away') NOT NULL DEFAULT 'pending',
  hold_until       DATETIME NOT NULL,
  reject_reason    VARCHAR(200) NULL,
  cancel_reason    VARCHAR(200) NULL,
  accepted_at      DATETIME NULL,
  in_transit_at    DATETIME NULL,
  arrived_at       DATETIME NULL,
  track_token_hash CHAR(64) NULL UNIQUE,
  created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tr_from   FOREIGN KEY (from_user_id)   REFERENCES users(user_id)         ON DELETE RESTRICT,
  CONSTRAINT fk_tr_driver FOREIGN KEY (driver_user_id) REFERENCES users(user_id)         ON DELETE SET NULL,
  CONSTRAINT fk_tr_hosp   FOREIGN KEY (to_hospital_id) REFERENCES hospitals(hospital_id) ON DELETE RESTRICT,
  CONSTRAINT fk_tr_bed    FOREIGN KEY (bed_id)         REFERENCES beds(bed_id)           ON DELETE RESTRICT,
  CONSTRAINT fk_tr_inc    FOREIGN KEY (incident_id)    REFERENCES incidents(incident_id) ON DELETE SET NULL,
  INDEX idx_tr_hosp_stage (to_hospital_id, stage),
  INDEX idx_tr_expiry     (stage, hold_until),
  INDEX idx_tr_user_stage (from_user_id, stage)
) ENGINE=InnoDB;

-- 12. referrals
CREATE TABLE referrals (
  referral_id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  from_user_id        INT UNSIGNED NOT NULL,
  from_hospital_id    INT UNSIGNED NULL,
  to_hospital_id      INT UNSIGNED NOT NULL,
  ward_type_id        TINYINT UNSIGNED NOT NULL,
  needs               JSON NULL,
  is_time_critical    TINYINT(1) NOT NULL DEFAULT 0,
  patient_note        VARCHAR(255) NULL,         -- clinical only, never a name or ID number
  status              ENUM('pending','accepted','declined','expired','escalated') NOT NULL DEFAULT 'pending',
  decline_reason      VARCHAR(200) NULL,
  coordinator_user_id INT UNSIGNED NULL,
  transfer_id         INT UNSIGNED NULL UNIQUE,
  created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_ref_from  FOREIGN KEY (from_user_id)        REFERENCES users(user_id)          ON DELETE RESTRICT,
  CONSTRAINT fk_ref_fromh FOREIGN KEY (from_hospital_id)    REFERENCES hospitals(hospital_id)  ON DELETE SET NULL,
  CONSTRAINT fk_ref_to    FOREIGN KEY (to_hospital_id)      REFERENCES hospitals(hospital_id)  ON DELETE RESTRICT,
  CONSTRAINT fk_ref_ward  FOREIGN KEY (ward_type_id)        REFERENCES ward_types(ward_type_id) ON DELETE RESTRICT,
  CONSTRAINT fk_ref_coord FOREIGN KEY (coordinator_user_id) REFERENCES users(user_id)          ON DELETE SET NULL,
  CONSTRAINT fk_ref_tr    FOREIGN KEY (transfer_id)         REFERENCES transfers(transfer_id)  ON DELETE SET NULL,
  INDEX idx_ref_to_status (to_hospital_id, status)
) ENGINE=InnoDB;

-- 13. handovers (patient details sent ahead + the ticks)
CREATE TABLE handovers (
  handover_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  transfer_id     INT UNSIGNED NOT NULL UNIQUE,
  triage          ENUM('red','yellow','green') NOT NULL,
  vitals          JSON NOT NULL,      -- {"pulse":118,"bp_sys":90,...}; null = not measured
  treatment_given VARCHAR(255) NOT NULL,
  details         TEXT NULL,
  voice_note_path VARCHAR(255) NULL,
  transcript      TEXT NULL,
  sent_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  seen_at         DATETIME NULL,
  seen_by         INT UNSIGNED NULL,
  claimed_by      INT UNSIGNED NULL,  -- the "I've got it" person
  ready_at        DATETIME NULL,
  alarm_at        DATETIME NULL,      -- set at 3 min unclaimed
  escalated_at    DATETIME NULL,      -- set at 5 min: coordinator told
  created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_ho_tr    FOREIGN KEY (transfer_id) REFERENCES transfers(transfer_id) ON DELETE CASCADE,
  CONSTRAINT fk_ho_seen  FOREIGN KEY (seen_by)     REFERENCES users(user_id)         ON DELETE SET NULL,
  CONSTRAINT fk_ho_claim FOREIGN KEY (claimed_by)  REFERENCES users(user_id)         ON DELETE SET NULL,
  INDEX idx_ho_unclaimed (claimed_by, sent_at)
) ENGINE=InnoDB;

-- 14. ambulance_locations (GPS points)
CREATE TABLE ambulance_locations (
  location_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NOT NULL,
  transfer_id INT UNSIGNED NULL,
  latitude    DECIMAL(9,6) NOT NULL,
  longitude   DECIMAL(9,6) NOT NULL,
  accuracy_m  SMALLINT UNSIGNED NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_loc_user FOREIGN KEY (user_id)     REFERENCES users(user_id)         ON DELETE CASCADE,
  CONSTRAINT fk_loc_tr   FOREIGN KEY (transfer_id) REFERENCES transfers(transfer_id) ON DELETE CASCADE,
  INDEX idx_loc_trip (transfer_id, created_at),
  INDEX idx_loc_user (user_id, created_at)
) ENGINE=InnoDB;

-- 15. turnaways
CREATE TABLE turnaways (
  turnaway_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  transfer_id  INT UNSIGNED NULL,
  hospital_id  INT UNSIGNED NOT NULL,
  ward_type_id TINYINT UNSIGNED NULL,
  reason       ENUM('no_bed','no_staff','no_equipment','nhis','other') NOT NULL,
  note         VARCHAR(255) NULL,
  reported_by  INT UNSIGNED NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_ta_tr   FOREIGN KEY (transfer_id)  REFERENCES transfers(transfer_id)  ON DELETE SET NULL,
  CONSTRAINT fk_ta_hosp FOREIGN KEY (hospital_id)  REFERENCES hospitals(hospital_id)  ON DELETE RESTRICT,
  CONSTRAINT fk_ta_ward FOREIGN KEY (ward_type_id) REFERENCES ward_types(ward_type_id) ON DELETE SET NULL,
  CONSTRAINT fk_ta_user FOREIGN KEY (reported_by)  REFERENCES users(user_id)          ON DELETE SET NULL,
  INDEX idx_ta_hosp_time (hospital_id, created_at)
) ENGINE=InnoDB;

-- 16. blood_stock
CREATE TABLE blood_stock (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  hospital_id   INT UNSIGNED NOT NULL,
  blood_type    ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  units         SMALLINT UNSIGNED NOT NULL DEFAULT 0,  -- UNSIGNED: can never go below 0
  reserve_units SMALLINT UNSIGNED NOT NULL DEFAULT 2,  -- safety buffer that holds may not touch
  updated_by    INT UNSIGNED NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_bs_hosp FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id) ON DELETE CASCADE,
  CONSTRAINT fk_bs_user FOREIGN KEY (updated_by)  REFERENCES users(user_id)         ON DELETE SET NULL,
  UNIQUE KEY uq_bs (hospital_id, blood_type)
) ENGINE=InnoDB;

-- 17. donors
CREATE TABLE donors (
  donor_id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id            INT UNSIGNED NOT NULL UNIQUE,
  blood_type         ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  type_verified      TINYINT(1) NOT NULL DEFAULT 0,
  area               VARCHAR(80) NOT NULL,
  latitude           DECIMAL(9,6) NULL,
  longitude          DECIMAL(9,6) NULL,
  last_donation_date DATE NULL,
  last_appeal_at     DATETIME NULL,
  consent_given_at   DATETIME NOT NULL,
  opted_out          TINYINT(1) NOT NULL DEFAULT 0,
  created_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_don_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  INDEX idx_don_type (blood_type, opted_out)
) ENGINE=InnoDB;

-- 18. pregnancies (no name stored)
CREATE TABLE pregnancies (
  id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  midwife_id         INT UNSIGNED NULL,
  due_date           DATE NOT NULL,
  area               VARCHAR(80) NOT NULL,
  risk_level         ENUM('low','medium','high') NOT NULL,
  chosen_hospital_id INT UNSIGNED NULL,
  consent_given_at   DATETIME NOT NULL,
  created_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_preg_mid  FOREIGN KEY (midwife_id)         REFERENCES users(user_id)         ON DELETE SET NULL,
  CONSTRAINT fk_preg_hosp FOREIGN KEY (chosen_hospital_id) REFERENCES hospitals(hospital_id) ON DELETE SET NULL,
  INDEX idx_preg_due (due_date)
) ENGINE=InnoDB;

-- 19. unidentified_patients (Find a relative)
CREATE TABLE unidentified_patients (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  hospital_id   INT UNSIGNED NOT NULL,
  sex           ENUM('male','female','unknown') NOT NULL,
  age_range     VARCHAR(10) NOT NULL,
  area          VARCHAR(80) NOT NULL,
  found_at      DATETIME NOT NULL,
  secret_hash   VARCHAR(255) NOT NULL,
  wrong_tries   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  is_locked     TINYINT(1) NOT NULL DEFAULT 0,
  identified    TINYINT(1) NOT NULL DEFAULT 0,
  identified_at DATETIME NULL,
  delete_after  DATETIME NOT NULL,
  created_by    INT UNSIGNED NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_up_hosp FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id) ON DELETE CASCADE,
  CONSTRAINT fk_up_user FOREIGN KEY (created_by)  REFERENCES users(user_id)         ON DELETE SET NULL,
  INDEX idx_up_search (area, found_at),
  INDEX idx_up_delete (delete_after)
) ENGINE=InnoDB;

-- 20. reviews
CREATE TABLE reviews (
  review_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  hospital_id    INT UNSIGNED NOT NULL,
  code_hash      CHAR(64) NOT NULL UNIQUE,
  used_at        DATETIME NULL,
  friendliness   TINYINT UNSIGNED NULL CHECK (friendliness BETWEEN 1 AND 5),
  waiting_time   TINYINT UNSIGNED NULL CHECK (waiting_time BETWEEN 1 AND 5),
  cleanliness    TINYINT UNSIGNED NULL CHECK (cleanliness BETWEEN 1 AND 5),
  communication  TINYINT UNSIGNED NULL CHECK (communication BETWEEN 1 AND 5),
  comment        VARCHAR(500) NULL,
  hospital_reply VARCHAR(500) NULL,
  replied_by     INT UNSIGNED NULL,
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_rev_hosp FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id) ON DELETE CASCADE,
  CONSTRAINT fk_rev_user FOREIGN KEY (replied_by)  REFERENCES users(user_id)         ON DELETE SET NULL,
  INDEX idx_rev_hosp (hospital_id, used_at)
) ENGINE=InnoDB;

-- 21. notifications (in-app alerts + the simulated SMS inbox/outbox)
CREATE TABLE notifications (
  notification_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  recipient_user_id     INT UNSIGNED NULL,
  recipient_hospital_id INT UNSIGNED NULL,     -- alerts for "whoever is on shift"
  phone                 VARCHAR(20) NULL,
  channel               ENUM('in_app','sms','email') NOT NULL,
  direction             ENUM('out','in') NOT NULL DEFAULT 'out',
  type                  VARCHAR(40) NOT NULL,
  payload               JSON NULL,
  transfer_id           INT UNSIGNED NULL,
  delivered             TINYINT(1) NOT NULL DEFAULT 0,
  seen_at               DATETIME NULL,
  created_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_no_user FOREIGN KEY (recipient_user_id)     REFERENCES users(user_id)         ON DELETE CASCADE,
  CONSTRAINT fk_no_hosp FOREIGN KEY (recipient_hospital_id) REFERENCES hospitals(hospital_id) ON DELETE CASCADE,
  CONSTRAINT fk_no_tr   FOREIGN KEY (transfer_id)           REFERENCES transfers(transfer_id) ON DELETE SET NULL,
  CONSTRAINT chk_no_target CHECK (recipient_user_id IS NOT NULL OR recipient_hospital_id IS NOT NULL
                                  OR phone IS NOT NULL),
  INDEX idx_no_user  (recipient_user_id, seen_at),
  INDEX idx_no_hosp  (recipient_hospital_id, seen_at),
  INDEX idx_no_phone (phone, created_at)
) ENGINE=InnoDB;

-- 22. audit_log (every change; undo)
CREATE TABLE audit_log (
  log_id     BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED NULL,
  action     VARCHAR(60) NOT NULL,      -- 'bed_status','hold','divert_on','undo'...
  table_name VARCHAR(40) NOT NULL,
  record_id  INT UNSIGNED NOT NULL,
  old_value  TEXT NULL,
  new_value  TEXT NULL,
  payload    JSON NULL,
  status     ENUM('applied','held','rejected','undone') NOT NULL DEFAULT 'applied',
  undo_of    BIGINT UNSIGNED NULL,
  ip         VARBINARY(16) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_al_user FOREIGN KEY (user_id) REFERENCES users(user_id)     ON DELETE SET NULL,
  CONSTRAINT fk_al_undo FOREIGN KEY (undo_of) REFERENCES audit_log(log_id)  ON DELETE SET NULL,
  INDEX idx_al_record (table_name, record_id),
  INDEX idx_al_time   (created_at),
  INDEX idx_al_status (status),
  INDEX idx_al_user   (user_id, created_at)
) ENGINE=InnoDB;
