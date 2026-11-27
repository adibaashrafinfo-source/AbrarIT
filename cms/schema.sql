-- Abrar IT CMS — database schema (MySQL / MariaDB)
-- Import this once into your hosting's MySQL database (phpMyAdmin: Import tab).

CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS blog_posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  category VARCHAR(80) NOT NULL DEFAULT 'General',
  excerpt VARCHAR(500) NOT NULL DEFAULT '',
  content MEDIUMTEXT NOT NULL DEFAULT '',
  cover_gradient_start VARCHAR(20) NOT NULL DEFAULT '#F15922',
  cover_gradient_end VARCHAR(20) NOT NULL DEFAULT '#1A1715',
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS portfolio_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  category VARCHAR(80) NOT NULL DEFAULT 'Branding',
  description VARCHAR(500) NOT NULL DEFAULT '',
  thumb_label VARCHAR(40) NOT NULL DEFAULT '',
  project_url VARCHAR(255) NOT NULL DEFAULT '',
  cover_gradient_start VARCHAR(20) NOT NULL DEFAULT '#F15922',
  cover_gradient_end VARCHAR(20) NOT NULL DEFAULT '#1A1715',
  sort_order INT NOT NULL DEFAULT 0,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(170) NOT NULL UNIQUE,
  summary VARCHAR(500) NOT NULL DEFAULT '',
  starting_price VARCHAR(60) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  status ENUM('draft','published') NOT NULL DEFAULT 'published',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(40) NOT NULL DEFAULT '',
  service_interest VARCHAR(120) NOT NULL DEFAULT '',
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed starting services to match the live site (edit freely from the admin panel afterwards)
INSERT INTO services (name, slug, summary, starting_price, sort_order, status) VALUES
('Graphic Design', 'graphic-design', 'Logos, brand kits, social creatives and packaging that make your brand unforgettable.', 'Starting from ৳500', 1, 'published'),
('Digital Marketing', 'digital-marketing', 'Facebook, Instagram, Google & SEO campaigns engineered to drive real revenue.', 'Starting from ৳2,000', 2, 'published'),
('Website Design', 'web-design', 'High-converting, blazing-fast websites with premium UI/UX for every device.', 'Starting from ৳3,000', 3, 'published'),
('Software Solutions', 'software', 'Custom web apps, e-commerce platforms and business software built to scale.', 'Starting from ৳3,000', 4, 'published'),
('Motion Graphics', 'motion', 'Scroll-stopping animated reveals, explainers and brand motion content.', 'Starting from ৳1,500', 5, 'published'),
('Video Editing', 'video', 'Cinematic edits, reels and ad-ready videos that keep audiences watching.', 'Starting from ৳1,000', 6, 'published'),
('Content Writing', 'content', 'SEO-driven copy, blogs and website content that speaks and sells.', 'Starting from ৳300', 7, 'published'),
('Email & SMS Marketing', 'email', 'Automated campaigns and newsletters that nurture leads into loyal customers.', 'Starting from ৳1,500', 8, 'published');

-- Seed real client projects to match the live site (edit freely from the admin panel afterwards)
INSERT INTO portfolio_items (title, category, description, thumb_label, project_url, cover_gradient_start, cover_gradient_end, sort_order, status) VALUES
('Ramani Collection', 'E-commerce', 'Premium fashion & beauty e-commerce store built for a growing lifestyle brand.', 'Ramani', 'https://www.ramanicollection.com/', '#F15922', '#1A1715', 1, 'published'),
('Ruposhee', 'E-commerce', 'Beauty, skincare & fashion storefront with a clean, conversion-focused shopping experience.', 'Ruposhee', 'https://ruposhee.com/', '#1A1715', '#F15922', 2, 'published'),
('NextGen Properties BD', 'Real Estate', 'A premium land-selling platform showcasing plots and property listings across Bangladesh.', 'NextGen', 'https://nextgenproperties.bd/', '#F15922', '#FF8A4D', 3, 'published'),
('Shahada Properties Ltd.', 'Real Estate', 'A trust-driven property company website built to present land and housing developments.', 'Shahada', 'https://shahadaproperties.com/', '#C8401A', '#FF8A4D', 4, 'published'),
('Sylvan Wye Resort & Agro', 'Hospitality', 'Investment showcase for a 41.75-acre hill resort and eco-tourism estate in Bandarban.', 'Sylvan Wye', 'https://sylvanwyeresort.com/', '#1A1715', '#C8401A', 5, 'published'),
('Mohinur Business Solutions', 'Corporate', 'Corporate website for a tax, VAT, accounting and business advisory firm in Bangladesh.', 'Mohinur', 'https://mohinurbusinesssolutions.com/', '#FF8A4D', '#1A1715', 6, 'published'),
('Trax Designers', 'Corporate', 'Premium buying house site presenting sourcing, QC and compliance services to global brands.', 'Trax', 'https://traxdesigners.com/', '#8A3010', '#1A1715', 7, 'published'),
('Career Bridge BD', 'Recruitment', 'A job-search platform connecting candidates in Bangladesh with their next career move.', 'CareerBridge', 'https://careerbridgebd.com/', '#C8401A', '#F15922', 8, 'published'),
('BD Online Quran Academy', 'Education', 'Online Quran school site offering live 1-to-1 and group classes with qualified Hafiz teachers.', 'BDOQ', 'https://www.bdonlinequranacademy.com/', '#3A2A22', '#FF8A4D', 9, 'published');

-- IMPORTANT: create your own admin login after importing this file.
-- Do NOT insert a hardcoded password hash here. Instead, open:
--   https://yourdomain.com/cms/setup.php
-- once, create your admin account, then delete setup.php from the server.
