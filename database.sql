-- Lurnixe Family Health Card System Database Schema
-- Generated for Production Deployment
-- Date: 2026-06-17 14:40:29

SET FOREIGN_KEY_CHECKS=0;

-- --------------------------------------------------------
-- Table structure for `admins`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin') NOT NULL DEFAULT 'admin',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `admins`
INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`, `status`, `created_at`) VALUES ('1', 'Super Admin', 'admin@lurnixehealth.com', '$2y$10$1cXGydw/.fs8eDyjooG5vuVjahqnqaPAju3P5zIRPAnJMknohF5oG', 'super_admin', 'active', '2026-06-15 21:07:36') ON DUPLICATE KEY UPDATE id=id;

-- --------------------------------------------------------
-- Table structure for `site_settings`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `site_settings`
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('1', 'site_name', 'LurnixeHealth', '2026-06-16 19:19:19') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('2', 'site_tagline', 'Your Premium Healthcare Partner', '2026-06-16 19:19:20') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('3', 'logo_path', 'assets/images/logo.png', '2026-06-16 19:19:20') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('4', 'favicon_path', 'favicon.ico', '2026-06-16 19:19:20') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('5', 'facebook_url', 'https://facebook.com/lurnixehealth', '2026-06-16 19:19:21') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('6', 'instagram_url', 'https://instagram.com/lurnixehealth', '2026-06-16 19:19:21') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('7', 'twitter_url', 'https://twitter.com/lurnixehealth', '2026-06-16 19:19:21') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('8', 'youtube_url', 'https://youtube.com/lurnixehealth', '2026-06-16 19:19:21') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('9', 'linkedin_url', 'https://linkedin.com/lurnixehealth', '2026-06-16 19:19:21') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('10', 'show_facebook', '1', '2026-06-16 19:19:21') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('11', 'show_instagram', '1', '2026-06-16 19:19:22') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('12', 'show_twitter', '1', '2026-06-16 19:19:22') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('13', 'show_youtube', '1', '2026-06-16 20:03:19') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('14', 'show_linkedin', '1', '2026-06-16 19:19:22') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('15', 'contact_phone', '+1 (555) 019-2834', '2026-06-16 19:19:22') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('16', 'contact_email', 'support@lurnixehealth.com', '2026-06-16 19:19:22') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('17', 'contact_address', '123 Medical Center Blvd, Suite 100, Health City, HC 12345', '2026-06-16 19:19:23') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('18', 'contact_whatsapp', '15550192834', '2026-06-16 19:19:23') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('19', 'footer_tagline', 'Providing quality healthcare solutions for families.', '2026-06-16 19:19:24') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('20', 'footer_copyright', '© 2026 LurnixeHealth. All rights reserved.', '2026-06-16 19:19:28') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('21', 'footer_description', 'LurnixeHealth is a certified healthcare platform providing secure family health cards, virtual consultations, and clinic access.', '2026-06-16 19:19:28') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('22', 'meta_title', 'LurnixeHealth - Family Health Card & Portal', '2026-06-16 19:19:28') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('23', 'meta_description', 'Manage your family health cards, contact doctor consultation clinics, and download verified medical history records online.', '2026-06-16 19:19:28') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES ('24', 'meta_keywords', 'health card, clinic consultations, family medical card, digital health records', '2026-06-16 19:19:29') ON DUPLICATE KEY UPDATE id=id;

-- --------------------------------------------------------
-- Table structure for `nav_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `nav_items`;
CREATE TABLE `nav_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `nav_items`
INSERT INTO `nav_items` (`id`, `label`, `url`, `parent_id`, `sort_order`, `is_active`) VALUES ('1', 'Home', 'index.php', NULL, '0', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `nav_items` (`id`, `label`, `url`, `parent_id`, `sort_order`, `is_active`) VALUES ('2', 'About Us', 'about.php', NULL, '1', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `nav_items` (`id`, `label`, `url`, `parent_id`, `sort_order`, `is_active`) VALUES ('3', 'Health Card', 'health-card.php', NULL, '2', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `nav_items` (`id`, `label`, `url`, `parent_id`, `sort_order`, `is_active`) VALUES ('4', 'Services', 'services.php', NULL, '3', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `nav_items` (`id`, `label`, `url`, `parent_id`, `sort_order`, `is_active`) VALUES ('5', 'Contact Us', 'contact.php', NULL, '4', '1') ON DUPLICATE KEY UPDATE id=id;

-- --------------------------------------------------------
-- Table structure for `stat_counters`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `stat_counters`;
CREATE TABLE `stat_counters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(20) NOT NULL,
  `label` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `stat_counters`
INSERT INTO `stat_counters` (`id`, `number`, `label`, `sort_order`, `is_active`) VALUES ('1', '10,000+', 'Happy Users', '0', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `stat_counters` (`id`, `number`, `label`, `sort_order`, `is_active`) VALUES ('2', '500+', 'Doctors', '1', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `stat_counters` (`id`, `number`, `label`, `sort_order`, `is_active`) VALUES ('3', '200+', 'Clinics', '2', '1') ON DUPLICATE KEY UPDATE id=id;
INSERT INTO `stat_counters` (`id`, `number`, `label`, `sort_order`, `is_active`) VALUES ('4', '50+', 'Hospitals', '3', '1') ON DUPLICATE KEY UPDATE id=id;

-- --------------------------------------------------------
-- Table structure for `contact_inquiries`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `contact_inquiries`;
CREATE TABLE `contact_inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `message` text NOT NULL,
  `status` varchar(20) DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply_subject` varchar(200) DEFAULT NULL,
  `reply_message` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `replied_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `members`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `members`;
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `mobile` varchar(15) NOT NULL,
  `alt_mobile` varchar(15) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `dob` date NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `blood_group` varchar(10) NOT NULL,
  `allergies` text DEFAULT NULL,
  `health_info` text DEFAULT NULL,
  `emergency_name` varchar(100) NOT NULL,
  `emergency_mobile` varchar(15) NOT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `validity_date` date NOT NULL,
  `status` enum('active','expired','suspended','deactivated') NOT NULL DEFAULT 'active',
  `payment_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_id` (`member_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `members_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `family_members`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `family_members`;
CREATE TABLE `family_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `relation` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `blood_group` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `family_members_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `activity_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `target_member_id` varchar(20) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `renewals`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `renewals`;
CREATE TABLE `renewals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` varchar(20) NOT NULL,
  `renewed_by` int(11) NOT NULL,
  `previous_validity` date NOT NULL,
  `new_validity` date NOT NULL,
  `renewal_duration_years` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `renewed_by` (`renewed_by`),
  CONSTRAINT `renewals_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  CONSTRAINT `renewals_ibfk_2` FOREIGN KEY (`renewed_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
