-- ============================================================
-- TechWire Blog Template — Database Installer
-- Import this file in phpMyAdmin (or via CLI: mysql -u root -p dbname < install.sql)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------
-- Table: users  (admin + authors)
-- ---------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','author') NOT NULL DEFAULT 'author',
    avatar VARCHAR(255) DEFAULT 'assets/images/avatar-default.png',
    bio TEXT,
    status ENUM('active','disabled') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- Table: categories
-- ---------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- Table: posts
-- ---------------------------------
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt VARCHAR(500),
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    category_id INT,
    author_id INT NOT NULL,
    status ENUM('draft','pending','published') NOT NULL DEFAULT 'draft',
    is_sponsored TINYINT(1) NOT NULL DEFAULT 0,
    post_type ENUM('standard','guest_post') NOT NULL DEFAULT 'standard',
    views INT NOT NULL DEFAULT 0,
    published_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FULLTEXT KEY ft_search (title, excerpt, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- Table: link_insertion_requests
-- (submissions from the Link Insertion page)
-- ---------------------------------
CREATE TABLE IF NOT EXISTS link_insertion_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    target_post_url VARCHAR(500),
    your_url VARCHAR(500) NOT NULL,
    anchor_text VARCHAR(255) NOT NULL,
    message TEXT,
    status ENUM('new','reviewing','accepted','rejected') NOT NULL DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- Table: guest_post_submissions
-- ---------------------------------
CREATE TABLE IF NOT EXISTS guest_post_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    website VARCHAR(255),
    proposed_title VARCHAR(255) NOT NULL,
    pitch TEXT NOT NULL,
    status ENUM('new','reviewing','accepted','rejected') NOT NULL DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- Table: contact_messages
-- ---------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------
-- Table: site_settings (lets buyer edit site name, rates, etc. from admin)
-- ---------------------------------
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA — demo content so the template looks alive on first run
-- ============================================================

-- Default admin login: admin@techwire.test / Admin@123
-- Default author login: author@techwire.test / Author@123
-- (Hashes below are bcrypt for the passwords above — change after install!)
INSERT INTO users (name, email, password, role, bio, status) VALUES
('Admin User', 'admin@techwire.test', '$2y$10$wSPFFIC8GGWHY3WT2MBfeep99nMwQy7X94YGdqTJRLmEhv7TBeYJ.', 'admin', 'Founder & editor-in-chief of TechWire.', 'active'),
('Sara Author', 'author@techwire.test', '$2y$10$HuMLAVN1w3Hyv4S/1dl0vuPOykAJ46b9valpiFPJJ5fVIxniwq4TC', 'author', 'Staff writer covering cloud and DevOps.', 'active');

INSERT INTO categories (name, slug, description) VALUES
('Artificial Intelligence', 'ai', 'News and guides on AI, machine learning, and LLMs.'),
('Web Development', 'web-development', 'Frontend, backend, and full-stack tutorials.'),
('Cybersecurity', 'cybersecurity', 'Security news, threats, and best practices.'),
('Gadgets', 'gadgets', 'Reviews and news on the latest tech hardware.'),
('Startups', 'startups', 'Startup news, funding rounds, and founder stories.');

INSERT INTO posts (title, slug, excerpt, content, featured_image, category_id, author_id, status, is_sponsored, post_type, views, published_at) VALUES
('The State of AI Coding Assistants in 2026', 'state-of-ai-coding-assistants-2026', 'A grounded look at how AI pair-programmers have changed daily developer workflows this year.', '<p>AI coding assistants have moved from novelty to default tooling for most professional developers. This piece breaks down what changed, what did not, and where the friction still lives.</p><p>Adoption is no longer the question - calibration is. Teams are learning when to trust generated code and when to slow down.</p>', 'assets/images/post-ai.jpg', 1, 1, 'published', 0, 'standard', 482, NOW()),
('A Practical Guide to Core Web Vitals in 2026', 'core-web-vitals-guide-2026', 'Everything that changed in Google ranking signals and how to actually fix the metrics that matter.', '<p>Core Web Vitals remain central to technical SEO. This guide walks through measuring, diagnosing, and fixing the three metrics that affect both ranking and real user experience.</p>', 'assets/images/post-webdev.jpg', 2, 2, 'published', 0, 'standard', 311, NOW()),
('Why Credential Stuffing Attacks Are Rising Again', 'credential-stuffing-attacks-rising', 'A breakdown of the latest wave of automated login attacks and how teams are responding.', '<p>Credential stuffing has resurged as leaked password databases continue to circulate. We look at the defensive patterns that are actually working in production.</p>', 'assets/images/post-security.jpg', 3, 1, 'published', 0, 'standard', 198, NOW()),
('Hands-On: The New Wave of AI-Powered Dev Boards', 'ai-powered-dev-boards-hands-on', 'We tested three new single-board computers built for on-device AI workloads.', '<p>On-device inference is finally practical on consumer hardware. Here is how three new boards performed on real workloads.</p>', 'assets/images/post-gadget.jpg', 4, 2, 'published', 0, 'standard', 256, NOW()),
('How a Two-Person Startup Reached 50,000 Users Without Ads', 'startup-50000-users-without-ads', 'A founder breaks down the organic-growth playbook that got them there.', '<p>No ad spend, no growth team - just relentless focus on one channel. Here is the breakdown.</p>', 'assets/images/post-startup.jpg', 5, 1, 'published', 1, 'guest_post', 142, NOW()),
('Choosing a Backend Framework in 2026: A Decision Guide', 'choosing-backend-framework-2026', 'A practical comparison for teams picking a backend stack this year.', '<p>Draft in progress - comparing performance, hiring pool, and long-term maintenance cost across today''s top backend frameworks.</p>', 'assets/images/post-webdev.jpg', 2, 2, 'draft', 0, 'standard', 0, NULL);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'TechWire'),
('site_tagline', 'Signal from the noise in tech'),
('contact_email', 'hello@techwire.test'),
('link_insertion_price', '$45 per link'),
('guest_post_price', '$60 per published guest post'),
('turnaround_time', '3-5 business days');
