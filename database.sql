-- ============================================================
-- KILIMO SMART TANZANIA — Database Schema
-- MySQL / MariaDB
-- ============================================================



-- ── USERS ────────────────────────────────────────────────────
CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120)        NOT NULL,
  email         VARCHAR(180)        NOT NULL UNIQUE,
  phone         VARCHAR(30)         NOT NULL,
  password_hash VARCHAR(255)        NOT NULL,
  role          ENUM('admin','farmer') NOT NULL DEFAULT 'farmer',
  region        VARCHAR(80),
  avatar        VARCHAR(10)         DEFAULT '🧑‍🌾',
  title         VARCHAR(120),
  subscription  ENUM('Free','Premium') NOT NULL DEFAULT 'Free',
  status        ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login    DATETIME,
  INDEX idx_email (email),
  INDEX idx_role  (role)
);

-- ── CROPS ────────────────────────────────────────────────────
CREATE TABLE crops (
  id      VARCHAR(30) PRIMARY KEY,
  name    VARCHAR(80)  NOT NULL,
  name_sw VARCHAR(80),
  emoji   VARCHAR(6),
  color   VARCHAR(10),
  unit    VARCHAR(10)  NOT NULL DEFAULT 'kg'
);

-- ── REGIONS ──────────────────────────────────────────────────
CREATE TABLE regions (
  id   VARCHAR(30) PRIMARY KEY,
  name VARCHAR(80)  NOT NULL,
  zone VARCHAR(80)
);

-- ── MARKET PRICES ────────────────────────────────────────────
CREATE TABLE market_prices (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  crop_id     VARCHAR(30)    NOT NULL,
  region_id   VARCHAR(30)    NOT NULL,
  price       DECIMAL(10,2)  NOT NULL,
  change_pct  DECIMAL(6,2)   NOT NULL DEFAULT 0,
  trend       ENUM('up','down','flat') NOT NULL DEFAULT 'flat',
  recorded_at DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (crop_id)   REFERENCES crops(id),
  FOREIGN KEY (region_id) REFERENCES regions(id),
  INDEX idx_crop_region (crop_id, region_id),
  INDEX idx_recorded    (recorded_at)
);

-- ── STORAGE HUBS ─────────────────────────────────────────────
CREATE TABLE storage_hubs (
  id              VARCHAR(20) PRIMARY KEY,
  name            VARCHAR(150) NOT NULL,
  region_id       VARCHAR(30)  NOT NULL,
  location        VARCHAR(200),
  capacity        INT          NOT NULL,
  occupied        INT          NOT NULL DEFAULT 0,
  temperature     DECIMAL(4,1),
  humidity        INT,
  status          ENUM('active','full','maintenance') NOT NULL DEFAULT 'active',
  price_per_tonne DECIMAL(10,2) NOT NULL,
  phone           VARCHAR(30),
  manager         VARCHAR(100),
  crops_stored    VARCHAR(255),
  FOREIGN KEY (region_id) REFERENCES regions(id)
);

-- ── STORAGE BOOKINGS ─────────────────────────────────────────
CREATE TABLE storage_bookings (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT            NOT NULL,
  hub_id      VARCHAR(20)    NOT NULL,
  crop_id     VARCHAR(30)    NOT NULL,
  tonnes      DECIMAL(8,2)   NOT NULL,
  months      INT            NOT NULL DEFAULT 1,
  total_cost  DECIMAL(12,2)  NOT NULL,
  status      ENUM('pending','confirmed','active','completed','cancelled') NOT NULL DEFAULT 'pending',
  start_date  DATE,
  end_date    DATE,
  created_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (hub_id)  REFERENCES storage_hubs(id),
  FOREIGN KEY (crop_id) REFERENCES crops(id)
);

-- ── LOAN PRODUCTS ────────────────────────────────────────────
CREATE TABLE loan_products (
  id               VARCHAR(30) PRIMARY KEY,
  name             VARCHAR(120) NOT NULL,
  description      TEXT,
  min_amount       DECIMAL(12,2) NOT NULL,
  max_amount       DECIMAL(12,2) NOT NULL,
  interest_rate    DECIMAL(5,2)  NOT NULL,
  tenure_months    INT           NOT NULL,
  repayment_type   VARCHAR(80),
  eligibility      TEXT,
  emoji            VARCHAR(6),
  active           TINYINT(1) NOT NULL DEFAULT 1
);

-- ── LOAN APPLICATIONS ────────────────────────────────────────
CREATE TABLE loan_applications (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT            NOT NULL,
  product_id   VARCHAR(30)    NOT NULL,
  amount       DECIMAL(12,2)  NOT NULL,
  purpose      TEXT,
  farm_size    VARCHAR(50),
  crop_season  VARCHAR(80),
  guarantor    VARCHAR(120),
  status       ENUM('pending','reviewing','approved','rejected','disbursed','closed') NOT NULL DEFAULT 'pending',
  approved_by  INT,
  note         TEXT,
  applied_at   DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME       ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)     REFERENCES users(id),
  FOREIGN KEY (product_id)  REFERENCES loan_products(id),
  FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- ── LOAN REPAYMENTS ──────────────────────────────────────────
CREATE TABLE loan_repayments (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  application_id INT           NOT NULL,
  amount_paid    DECIMAL(12,2) NOT NULL,
  method         VARCHAR(50),
  transaction_ref VARCHAR(100),
  paid_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (application_id) REFERENCES loan_applications(id)
);

-- ── TRANSACTIONS ─────────────────────────────────────────────
CREATE TABLE transactions (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT           NOT NULL,
  type            ENUM('Subscription','Storage Fee','Loan Repayment','Loan Disbursement','Commission') NOT NULL,
  amount          DECIMAL(12,2) NOT NULL,
  method          VARCHAR(50),
  reference       VARCHAR(100),
  status          ENUM('pending','completed','failed') NOT NULL DEFAULT 'pending',
  transaction_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_user   (user_id),
  INDEX idx_status (status),
  INDEX idx_date   (transaction_at)
);

-- ── ADVISORY TIPS ────────────────────────────────────────────
CREATE TABLE advisory_tips (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  crop_id     VARCHAR(30),
  title       VARCHAR(200)  NOT NULL,
  body        TEXT          NOT NULL,
  category    VARCHAR(60),
  region      VARCHAR(100),
  author_id   INT,
  emoji       VARCHAR(6),
  published   TINYINT(1) NOT NULL DEFAULT 1,
  created_at  DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (crop_id)   REFERENCES crops(id),
  FOREIGN KEY (author_id) REFERENCES users(id)
);

-- ── NOTIFICATIONS ────────────────────────────────────────────
CREATE TABLE notifications (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT         NOT NULL,
  type       ENUM('price','weather','loan','storage','system') NOT NULL,
  title      VARCHAR(200) NOT NULL,
  body       TEXT,
  is_read    TINYINT(1)  NOT NULL DEFAULT 0,
  created_at DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  INDEX idx_user_read (user_id, is_read)
);

-- ── WEATHER ALERTS ───────────────────────────────────────────
CREATE TABLE weather_alerts (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  region_id   VARCHAR(30)  NOT NULL,
  type        ENUM('warning','info','danger') NOT NULL DEFAULT 'info',
  message     TEXT         NOT NULL,
  active      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at  DATETIME,
  FOREIGN KEY (region_id) REFERENCES regions(id)
);

-- ── AUDIT LOG ────────────────────────────────────────────────
CREATE TABLE audit_log (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT,
  action     VARCHAR(100) NOT NULL,
  entity     VARCHAR(60),
  entity_id  VARCHAR(30),
  details    JSON,
  ip_address VARCHAR(45),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user   (user_id),
  INDEX idx_action (action)
);

-- ============================================================
-- SEED DATA
-- ============================================================

-- Crops
INSERT INTO crops (id, name, name_sw, emoji, color, unit) VALUES
('maize',        'Maize',        'Mahindi',        '🌽', '#e8b830', 'kg'),
('rice',         'Rice',         'Mchele',         '🌾', '#87d494', 'kg'),
('beans',        'Beans',        'Maharagwe',      '🫘', '#c97b3a', 'kg'),
('cassava',      'Cassava',      'Muhogo',         '🥔', '#d4a0c0', 'kg'),
('groundnuts',   'Groundnuts',   'Karanga',        '🥜', '#e8946a', 'kg'),
('sunflower',    'Sunflower',    'Alizeti',        '🌻', '#ffd700', 'kg'),
('sorghum',      'Sorghum',      'Mtama',          '🌿', '#90ee90', 'kg'),
('sweet_potato', 'Sweet Potato', 'Viazi Vitamu',   '🍠', '#ff7f50', 'kg');

-- Regions
INSERT INTO regions (id, name, zone) VALUES
('dar',      'Dar es Salaam', 'Coastal'),
('morogoro', 'Morogoro',      'Central'),
('iringa',   'Iringa',        'Southern Highlands'),
('dodoma',   'Dodoma',        'Central'),
('arusha',   'Arusha',        'Northern'),
('mbeya',    'Mbeya',         'Southern Highlands'),
('mwanza',   'Mwanza',        'Lake Zone'),
('tanga',    'Tanga',         'Coastal');

-- Market prices (current)
INSERT INTO market_prices (crop_id, region_id, price, change_pct, trend) VALUES
('maize','dar',850,2.4,'up'),('maize','morogoro',680,-1.2,'down'),('maize','iringa',600,0.8,'up'),
('maize','dodoma',720,0,'flat'),('maize','arusha',790,3.1,'up'),('maize','mbeya',640,-0.5,'down'),
('maize','mwanza',740,1.8,'up'),('maize','tanga',820,1.1,'up'),
('rice','dar',2200,1.5,'up'),('rice','morogoro',1850,0.3,'up'),('rice','iringa',2100,-2.1,'down'),
('rice','dodoma',2050,0,'flat'),('rice','arusha',2300,2.8,'up'),('rice','mbeya',1980,-0.8,'down'),
('rice','mwanza',2150,1.2,'up'),('rice','tanga',2080,0.6,'up'),
('beans','dar',3200,4.2,'up'),('beans','morogoro',2800,1.8,'up'),('beans','iringa',2650,-1.5,'down'),
('beans','dodoma',2900,0,'flat'),('beans','arusha',3100,3.5,'up'),('beans','mbeya',2750,-0.9,'down'),
('beans','mwanza',2950,2.2,'up'),('beans','tanga',3050,1.3,'up'),
('cassava','dar',450,1.0,'up'),('cassava','morogoro',320,0,'flat'),('cassava','iringa',300,-2.0,'down'),
('cassava','dodoma',380,0.5,'up'),('cassava','arusha',490,1.8,'up'),('cassava','mbeya',350,-1.1,'down'),
('cassava','mwanza',410,0.7,'up'),('cassava','tanga',470,2.3,'up'),
('groundnuts','dar',4200,5.1,'up'),('groundnuts','morogoro',3800,2.4,'up'),('groundnuts','iringa',3600,-1.8,'down'),
('groundnuts','dodoma',3950,0,'flat'),('groundnuts','arusha',4100,3.8,'up'),('groundnuts','mbeya',3700,-0.7,'down'),
('groundnuts','mwanza',3850,1.5,'up'),('groundnuts','tanga',4050,2.1,'up'),
('sunflower','dar',1900,2.8,'up'),('sunflower','morogoro',1650,0.4,'up'),('sunflower','iringa',1580,-1.0,'down'),
('sunflower','dodoma',1720,0,'flat'),('sunflower','arusha',1850,3.2,'up'),('sunflower','mbeya',1600,-0.5,'down'),
('sunflower','mwanza',1780,1.9,'up'),('sunflower','tanga',1870,1.4,'up'),
('sorghum','dar',780,1.2,'up'),('sorghum','morogoro',620,-0.8,'down'),('sorghum','iringa',590,0,'flat'),
('sorghum','dodoma',660,1.5,'up'),('sorghum','arusha',740,2.1,'up'),('sorghum','mbeya',610,-1.3,'down'),
('sorghum','mwanza',680,0.9,'up'),('sorghum','tanga',760,1.8,'up'),
('sweet_potato','dar',1100,3.5,'up'),('sweet_potato','morogoro',880,1.0,'up'),('sweet_potato','iringa',820,-2.5,'down'),
('sweet_potato','dodoma',950,0,'flat'),('sweet_potato','arusha',1050,2.8,'up'),('sweet_potato','mbeya',860,-1.0,'down'),
('sweet_potato','mwanza',990,1.6,'up'),('sweet_potato','tanga',1080,2.2,'up');

-- Storage hubs
INSERT INTO storage_hubs (id, name, region_id, location, capacity, occupied, temperature, humidity, status, price_per_tonne, phone, manager, crops_stored) VALUES
('hub-001','Kilimo Hub Morogoro Central','morogoro','Morogoro Town, Kwamatope Area',500,312,23,52,'active',15000,'+255 754 001 001','Juma Kassim','Maize,Beans,Sorghum'),
('hub-002','Kilimo Hub Iringa Highlands','iringa','Iringa Municipality, Gangilonga',300,198,20,48,'active',13500,'+255 754 002 002','Anna Mwakipesile','Maize,Beans,Groundnuts,Sunflower'),
('hub-003','Kilimo Hub Dodoma East','dodoma','Dodoma City, Nala Zone',400,400,25,45,'full',14000,'+255 754 003 003','Fatuma Ngowi','Maize,Sorghum,Groundnuts'),
('hub-004','Kilimo Hub Mbeya South','mbeya','Mbeya City, Uyole Agricultural Area',600,280,21,50,'active',12500,'+255 754 004 004','Peter Silayo','Maize,Beans,Sweet Potato,Rice');

-- Loan products
INSERT INTO loan_products (id, name, description, min_amount, max_amount, interest_rate, tenure_months, repayment_type, eligibility, emoji) VALUES
('loan-input',    'Agricultural Input Loan',       'For seeds, fertilisers, and farm inputs',           500000,  2000000, 12, 12, 'Monthly',            'Active Kilimo Smart farmer, 3+ months on platform', '🌱'),
('loan-equipment','Farm Equipment Loan',            'For irrigation, tools, and machinery',              1000000, 5000000, 14, 24, 'Monthly',            'Verified farmer, group guarantor required',          '🚜'),
('loan-storage',  'Post-Harvest Storage Loan',      'Cover storage costs to avoid distress selling',     200000,  800000,  10,  6, 'Bullet (end of season)', 'Active storage hub booking required',            '🏪');

-- Users (team + sample farmers)
INSERT INTO users (name, email, phone, password_hash, role, region, avatar, title, subscription, status) VALUES
('Yohana Samwel Machuma',  'yohana@kilimosmart.tz',  '+255 754 100 001', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '👨‍💼', 'Chief Executive Officer',           'Premium', 'active'),
('William Patrick Msafiri','william@kilimosmart.tz', '+255 754 100 002', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '👨‍💻', 'Chief Technology Officer',           'Premium', 'active'),
('Enimelda Raphael',       'enimelda@kilimosmart.tz','+255 754 100 003', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '👩‍🌾', 'Digital Extension Officer',          'Premium', 'active'),
('Karol Vicent',           'karol@kilimosmart.tz',   '+255 754 100 004', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '🌿', 'Agri-Tech Expert (Board)',           'Premium', 'active'),
('Priscar Laurence Mrope', 'priscar@kilimosmart.tz', '+255 754 100 005', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '📊', 'Cashier & Accountant',               'Premium', 'active'),
('Saidi Bahati Ally',      'saidi@kilimosmart.tz',   '+255 754 100 006', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '🚜', 'Operations & Field Manager',         'Premium', 'active'),
('Amani Amon',             'amani@kilimosmart.tz',   '+255 754 100 007', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '👥', 'HR & Training Coordinator',          'Premium', 'active'),
('Maliki Moshi Luwungo',   'maliki@kilimosmart.tz',  '+255 754 100 008', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '⚖️', 'Monitoring & Evaluation Officer',    'Premium', 'active'),
('Maulida Nuru Hoseni',    'maulida@kilimosmart.tz', '+255 754 100 009', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '💰', 'Finance & Investment Manager',       'Premium', 'active'),
('Mwaija Halfani Mnyachi', 'mwaija@kilimosmart.tz',  '+255 754 100 010', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '🤝', 'Community & Partnership Ambassador', 'Premium', 'active'),
('Coletha Deodatus Lwala', 'coletha@kilimosmart.tz', '+255 754 100 011', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'admin', 'Dar es Salaam', '📣', 'Marketing & Logistics Officer',      'Premium', 'active'),
('Amina Juma',             'amina@farmer.tz',        '+255 712 111 222', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'farmer','Morogoro',      '👩‍🌾', 'Mkulima', 'Premium', 'active'),
('Demo Farmer',            'demo@kilimosmart.tz',    '+255 754 999 000', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMaJqRMB15AZ5GR6q2VvmgZN6G', 'farmer','Arusha',         '🧑‍🌾', 'Mkulima', 'Free',    'active');

-- Note: All passwords above hash to "admin123" / "farmer123" / "demo"
-- For production, generate fresh hashes with password_hash($pass, PASSWORD_BCRYPT)

-- Advisory tips
INSERT INTO advisory_tips (crop_id, title, body, category, region, author_id, emoji) VALUES
('maize',       'Planting Time Optimisation',       'Plant maize at the onset of long rains (March–April). Ensure soil temperature is above 18°C. Space rows 75cm apart, seeds 25cm apart for optimal yield.',                                         'Planting',     'All Regions',           3, '🌽'),
('beans',       'Post-Harvest Storage Tips',         'Dry beans to below 13% moisture before storage. Use hermetic bags (PICS bags) to prevent weevil infestation. Store in cool, dry conditions (below 25°C).',                                     'Post-Harvest', 'Southern Highlands',    3, '🫘'),
('rice',        'Water Management in Paddy Rice',    'Maintain 5cm water depth during tillering stage. Drain fields 2 weeks before harvest to allow soil to firm up. This improves grain quality and makes harvest easier.',                           'Irrigation',   'Mbeya, Morogoro',       3, '🌾'),
('groundnuts',  'Preventing Aflatoxin Contamination','Harvest groundnuts before rains if possible. Dry quickly on raised platforms in the sun. Never store wet groundnuts — aflatoxin contamination reduces market value significantly.',               'Post-Harvest', 'All Regions',           3, '🥜');

-- Weather alerts
INSERT INTO weather_alerts (region_id, type, message, active) VALUES
('dar',    'warning', 'Heavy rainfall expected Tue–Wed. Protect harvested crops.',            1),
('iringa', 'info',    'Cold nights expected Thu–Fri. Good conditions for drying beans.',      1);
