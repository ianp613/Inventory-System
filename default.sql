-- Date: January 13, 2026
-- Time: 10:30:44 AM
-- DB Name: inventory_system


CREATE TABLE `cctv_camera` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `lid` varchar(255) NOT NULL,
  `camera_id` varchar(255) NOT NULL,
  `camera_type` varchar(255) NOT NULL,
  `camera_subtype` varchar(255) NOT NULL,
  `camera_ip_address` varchar(255) NOT NULL,
  `camera_port_no` varchar(255) NOT NULL,
  `camera_username` varchar(255) NOT NULL,
  `camera_password` varchar(255) NOT NULL,
  `camera_angle` varchar(255) NOT NULL,
  `camera_location` varchar(255) NOT NULL,
  `camera_brand` varchar(255) NOT NULL,
  `camera_model_no` varchar(255) NOT NULL,
  `camera_barcode` varchar(255) NOT NULL,
  `camera_status` varchar(255) NOT NULL,
  `camera_remarks` varchar(255) NOT NULL,
  `cx` varchar(255) NOT NULL,
  `cy` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `cctv_location` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `map_location` varchar(255) NOT NULL,
  `floorplan` varchar(255) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `camera_size` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `consumable_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(1000) NOT NULL,
  `uid` varchar(1000) NOT NULL,
  `cid` varchar(1000) NOT NULL,
  `date` varchar(1000) NOT NULL,
  `time` varchar(1000) NOT NULL,
  `quantity_deduction` varchar(1000) NOT NULL,
  `remarks` varchar(1000) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `consumables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `measurement` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `stock` varchar(255) NOT NULL,
  `restock_point` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `equipment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `equipment_entry` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `eid` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `model_no` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `specifications` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `ip_address` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nid` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `subnet` varchar(255) NOT NULL,
  `hostname` varchar(255) NOT NULL,
  `site` varchar(255) NOT NULL,
  `server` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `webmgmtpt` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=691 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ip_address` VALUES('255', '2', '192.168.7.1', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:47', '2025-11-26 13:51:12');
INSERT INTO `ip_address` VALUES('256', '2', '192.168.7.2', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:47', '2025-11-26 13:40:47');
INSERT INTO `ip_address` VALUES('257', '2', '192.168.7.3', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:47', '2025-11-26 13:40:47');
INSERT INTO `ip_address` VALUES('258', '2', '192.168.7.4', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:47', '2025-11-26 13:40:47');
INSERT INTO `ip_address` VALUES('259', '2', '192.168.7.5', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:47', '2025-11-26 13:40:47');
INSERT INTO `ip_address` VALUES('260', '2', '192.168.7.6', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:47', '2025-11-26 13:40:47');
INSERT INTO `ip_address` VALUES('261', '2', '192.168.7.7', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:47', '2025-11-26 13:40:47');
INSERT INTO `ip_address` VALUES('262', '2', '192.168.7.8', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:47', '2025-11-26 13:40:47');
INSERT INTO `ip_address` VALUES('263', '2', '192.168.7.9', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:47', '2025-11-26 13:40:47');
INSERT INTO `ip_address` VALUES('264', '2', '192.168.7.10', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('265', '2', '192.168.7.11', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('266', '2', '192.168.7.12', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('267', '2', '192.168.7.13', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('268', '2', '192.168.7.14', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('269', '2', '192.168.7.15', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('270', '2', '192.168.7.16', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('271', '2', '192.168.7.17', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('272', '2', '192.168.7.18', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('273', '2', '192.168.7.19', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('274', '2', '192.168.7.20', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('275', '2', '192.168.7.21', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('276', '2', '192.168.7.22', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('277', '2', '192.168.7.23', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('278', '2', '192.168.7.24', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('279', '2', '192.168.7.25', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('280', '2', '192.168.7.26', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('281', '2', '192.168.7.27', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('282', '2', '192.168.7.28', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('283', '2', '192.168.7.29', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('284', '2', '192.168.7.30', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('285', '2', '192.168.7.31', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('286', '2', '192.168.7.32', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('287', '2', '192.168.7.33', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('288', '2', '192.168.7.34', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('289', '2', '192.168.7.35', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('290', '2', '192.168.7.36', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('291', '2', '192.168.7.37', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('292', '2', '192.168.7.38', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('293', '2', '192.168.7.39', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('294', '2', '192.168.7.40', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('295', '2', '192.168.7.41', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('296', '2', '192.168.7.42', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('297', '2', '192.168.7.43', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('298', '2', '192.168.7.44', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('299', '2', '192.168.7.45', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('300', '2', '192.168.7.46', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('301', '2', '192.168.7.47', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('302', '2', '192.168.7.48', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:48', '2025-11-26 13:40:48');
INSERT INTO `ip_address` VALUES('303', '2', '192.168.7.49', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('304', '2', '192.168.7.50', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('305', '2', '192.168.7.51', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('306', '2', '192.168.7.52', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('307', '2', '192.168.7.53', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('308', '2', '192.168.7.54', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('309', '2', '192.168.7.55', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('310', '2', '192.168.7.56', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('311', '2', '192.168.7.57', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('312', '2', '192.168.7.58', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('313', '2', '192.168.7.59', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('314', '2', '192.168.7.60', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('315', '2', '192.168.7.61', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('316', '2', '192.168.7.62', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('317', '2', '192.168.7.63', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('318', '2', '192.168.7.64', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('319', '2', '192.168.7.65', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('320', '2', '192.168.7.66', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('321', '2', '192.168.7.67', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('322', '2', '192.168.7.68', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('323', '2', '192.168.7.69', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('324', '2', '192.168.7.70', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('325', '2', '192.168.7.71', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('326', '2', '192.168.7.72', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('327', '2', '192.168.7.73', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('328', '2', '192.168.7.74', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('329', '2', '192.168.7.75', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('330', '2', '192.168.7.76', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('331', '2', '192.168.7.77', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('332', '2', '192.168.7.78', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('333', '2', '192.168.7.79', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('334', '2', '192.168.7.80', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('335', '2', '192.168.7.81', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('336', '2', '192.168.7.82', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:49', '2025-11-26 13:40:49');
INSERT INTO `ip_address` VALUES('337', '2', '192.168.7.83', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('338', '2', '192.168.7.84', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('339', '2', '192.168.7.85', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('340', '2', '192.168.7.86', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('341', '2', '192.168.7.87', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('342', '2', '192.168.7.88', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('343', '2', '192.168.7.89', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('344', '2', '192.168.7.90', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('345', '2', '192.168.7.91', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('346', '2', '192.168.7.92', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('347', '2', '192.168.7.93', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('348', '2', '192.168.7.94', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('349', '2', '192.168.7.95', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('350', '2', '192.168.7.96', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('351', '2', '192.168.7.97', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('352', '2', '192.168.7.98', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('353', '2', '192.168.7.99', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('354', '2', '192.168.7.100', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('355', '2', '192.168.7.101', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('356', '2', '192.168.7.102', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('357', '2', '192.168.7.103', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('358', '2', '192.168.7.104', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('359', '2', '192.168.7.105', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('360', '2', '192.168.7.106', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('361', '2', '192.168.7.107', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('362', '2', '192.168.7.108', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('363', '2', '192.168.7.109', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('364', '2', '192.168.7.110', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('365', '2', '192.168.7.111', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('366', '2', '192.168.7.112', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('367', '2', '192.168.7.113', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('368', '2', '192.168.7.114', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('369', '2', '192.168.7.115', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('370', '2', '192.168.7.116', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('371', '2', '192.168.7.117', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('372', '2', '192.168.7.118', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('373', '2', '192.168.7.119', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('374', '2', '192.168.7.120', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('375', '2', '192.168.7.121', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('376', '2', '192.168.7.122', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('377', '2', '192.168.7.123', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:50', '2025-11-26 13:40:50');
INSERT INTO `ip_address` VALUES('378', '2', '192.168.7.124', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('379', '2', '192.168.7.125', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('380', '2', '192.168.7.126', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('381', '2', '192.168.7.127', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('382', '2', '192.168.7.128', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('383', '2', '192.168.7.129', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('384', '2', '192.168.7.130', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('385', '2', '192.168.7.131', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('386', '2', '192.168.7.132', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('387', '2', '192.168.7.133', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('388', '2', '192.168.7.134', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('389', '2', '192.168.7.135', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('390', '2', '192.168.7.136', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('391', '2', '192.168.7.137', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('392', '2', '192.168.7.138', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('393', '2', '192.168.7.139', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('394', '2', '192.168.7.140', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('395', '2', '192.168.7.141', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('396', '2', '192.168.7.142', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('397', '2', '192.168.7.143', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('398', '2', '192.168.7.144', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('399', '2', '192.168.7.145', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('400', '2', '192.168.7.146', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('401', '2', '192.168.7.147', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('402', '2', '192.168.7.148', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('403', '2', '192.168.7.149', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('404', '2', '192.168.7.150', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('405', '2', '192.168.7.151', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('406', '2', '192.168.7.152', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('407', '2', '192.168.7.153', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('408', '2', '192.168.7.154', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('409', '2', '192.168.7.155', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('410', '2', '192.168.7.156', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('411', '2', '192.168.7.157', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('412', '2', '192.168.7.158', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('413', '2', '192.168.7.159', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('414', '2', '192.168.7.160', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('415', '2', '192.168.7.161', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('416', '2', '192.168.7.162', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('417', '2', '192.168.7.163', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('418', '2', '192.168.7.164', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('419', '2', '192.168.7.165', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('420', '2', '192.168.7.166', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:51', '2025-11-26 13:40:51');
INSERT INTO `ip_address` VALUES('421', '2', '192.168.7.167', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('422', '2', '192.168.7.168', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('423', '2', '192.168.7.169', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('424', '2', '192.168.7.170', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('425', '2', '192.168.7.171', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('426', '2', '192.168.7.172', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('427', '2', '192.168.7.173', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('428', '2', '192.168.7.174', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('429', '2', '192.168.7.175', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('430', '2', '192.168.7.176', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('431', '2', '192.168.7.177', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('432', '2', '192.168.7.178', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('433', '2', '192.168.7.179', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('434', '2', '192.168.7.180', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('435', '2', '192.168.7.181', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('436', '2', '192.168.7.182', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('437', '2', '192.168.7.183', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('438', '2', '192.168.7.184', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('439', '2', '192.168.7.185', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('440', '2', '192.168.7.186', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('441', '2', '192.168.7.187', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('442', '2', '192.168.7.188', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('443', '2', '192.168.7.189', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('444', '2', '192.168.7.190', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('445', '2', '192.168.7.191', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('446', '2', '192.168.7.192', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('447', '2', '192.168.7.193', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('448', '2', '192.168.7.194', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('449', '2', '192.168.7.195', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('450', '2', '192.168.7.196', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('451', '2', '192.168.7.197', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('452', '2', '192.168.7.198', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('453', '2', '192.168.7.199', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('454', '2', '192.168.7.200', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('455', '2', '192.168.7.201', '255.255.255.0', 'Lemuel PC', 'Main 2nd flr. Techroom', '-', 'DOWN', 'ASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-12-24 15:43:07');
INSERT INTO `ip_address` VALUES('456', '2', '192.168.7.202', '255.255.255.0', 'Ramirr PC', 'Main 2nd flr. Techroom', '-', 'DOWN', 'ASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 14:00:31');
INSERT INTO `ip_address` VALUES('457', '2', '192.168.7.203', '255.255.255.0', 'Joseph PC', 'Main 2nd flr. Techroom', '-', 'UP', 'ASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 14:00:59');
INSERT INTO `ip_address` VALUES('458', '2', '192.168.7.204', '255.255.255.0', 'TP-Link Smart SW', 'Main 2nd flr. Techroom', '-', 'UP', 'ASSIGNED', '-', '-', 'freight', 'core32828324', '2025-11-26 13:40:52', '2025-11-26 14:01:39');
INSERT INTO `ip_address` VALUES('459', '2', '192.168.7.205', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('460', '2', '192.168.7.206', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('461', '2', '192.168.7.207', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('462', '2', '192.168.7.208', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('463', '2', '192.168.7.209', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('464', '2', '192.168.7.210', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('465', '2', '192.168.7.211', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:52', '2025-11-26 13:40:52');
INSERT INTO `ip_address` VALUES('466', '2', '192.168.7.212', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('467', '2', '192.168.7.213', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('468', '2', '192.168.7.214', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('469', '2', '192.168.7.215', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('470', '2', '192.168.7.216', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('471', '2', '192.168.7.217', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('472', '2', '192.168.7.218', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('473', '2', '192.168.7.219', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('474', '2', '192.168.7.220', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('475', '2', '192.168.7.221', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('476', '2', '192.168.7.222', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('477', '2', '192.168.7.223', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('478', '2', '192.168.7.224', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('479', '2', '192.168.7.225', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('480', '2', '192.168.7.226', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('481', '2', '192.168.7.227', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('482', '2', '192.168.7.228', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('483', '2', '192.168.7.229', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('484', '2', '192.168.7.230', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('485', '2', '192.168.7.231', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('486', '2', '192.168.7.232', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('487', '2', '192.168.7.233', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('488', '2', '192.168.7.234', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:53', '2025-11-26 13:40:53');
INSERT INTO `ip_address` VALUES('489', '2', '192.168.7.235', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('490', '2', '192.168.7.236', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('491', '2', '192.168.7.237', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('492', '2', '192.168.7.238', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('493', '2', '192.168.7.239', '255.255.255.0', 'Annex 2nd floor Mesh-B', '2ND FLOOR ROW 4 HANGING CAB. AREA', '-', 'UP', 'ASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 14:17:11');
INSERT INTO `ip_address` VALUES('494', '2', '192.168.7.240', '255.255.255.0', 'CBS 250 Series SW', 'Annex02 Server Room', '-', 'UP', 'ASSIGNED', '-', '-', 'freight', 'Core32828324!!', '2025-11-26 13:40:54', '2025-11-26 14:13:06');
INSERT INTO `ip_address` VALUES('495', '2', '192.168.7.241', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('496', '2', '192.168.7.242', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('497', '2', '192.168.7.243', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('498', '2', '192.168.7.244', '255.255.255.0', 'UPSG Mesh', '2ND FLOOR UPSG PROD. AREA', '-', 'UP', 'ASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 14:18:21');
INSERT INTO `ip_address` VALUES('499', '2', '192.168.7.245', '255.255.255.0', 'Pawing Free Wifi SW', 'Main 2nd flr. Server Room', '-', 'UP', 'ASSIGNED', '-', '-', 'tech', 'CmoS23826867', '2025-11-26 13:40:54', '2025-11-26 14:20:11');
INSERT INTO `ip_address` VALUES('500', '2', '192.168.7.246', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('501', '2', '192.168.7.247', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('502', '2', '192.168.7.248', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('503', '2', '192.168.7.249', '255.255.255.0', 'ESTES Mesh', 'Mesh AP Annex 1st floor production (Estes)', '-', 'UP', 'ASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 14:22:12');
INSERT INTO `ip_address` VALUES('504', '2', '192.168.7.250', '255.255.255.0', 'Powerbeam AP', 'Annex01 Server Room', '-', 'UP', 'ASSIGNED', '-', '-', 'fposi', 'Myfposi123!!', '2025-11-26 13:40:54', '2025-11-26 14:23:54');
INSERT INTO `ip_address` VALUES('505', '2', '192.168.7.251', '255.255.255.0', 'Powerbeam Station', 'Main Bldg. 1st flr. Server Room', '-', 'UP', 'ASSIGNED', '-', '-', 'fposi', 'Core32828324!!', '2025-11-26 13:40:54', '2025-11-26 14:25:00');
INSERT INTO `ip_address` VALUES('506', '2', '192.168.7.252', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('507', '2', '192.168.7.253', '255.255.255.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2025-11-26 13:40:54', '2025-11-26 13:40:54');
INSERT INTO `ip_address` VALUES('508', '2', '192.168.7.254', '255.255.255.0', 'Annex Free Wifi Router', 'Annex01 Server Room', '-', 'UP', 'ASSIGNED', '-', '-', 'freight', 'CmoS23826867', '2025-11-26 13:40:54', '2025-11-26 14:26:05');
INSERT INTO `ip_address` VALUES('512', '2', '192.168.7.1', '255.255.255', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:01:15', '2026-01-12 09:01:15');
INSERT INTO `ip_address` VALUES('513', '2', '192.168.7.2', '255.255.255', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:01:15', '2026-01-12 09:01:15');
INSERT INTO `ip_address` VALUES('514', '2', '192.168.15.1', '255.255.254.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:02:20', '2026-01-12 09:02:20');
INSERT INTO `ip_address` VALUES('515', '2', '192.168.15.2', '255.255.254.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:02:20', '2026-01-12 09:02:20');
INSERT INTO `ip_address` VALUES('516', '2', '192.168.15.3', '255.255.254.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:02:20', '2026-01-12 09:02:20');
INSERT INTO `ip_address` VALUES('517', '2', '192.168.15.4', '255.255.254.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:02:20', '2026-01-12 09:02:20');
INSERT INTO `ip_address` VALUES('518', '2', '192.168.15.5', '255.255.254.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:02:20', '2026-01-12 09:02:20');
INSERT INTO `ip_address` VALUES('519', '2', '192.168.15.6', '255.255.254.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:02:20', '2026-01-12 09:02:20');
INSERT INTO `ip_address` VALUES('520', '2', '192.168.15.7', '255.255.254.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:02:20', '2026-01-12 09:02:20');
INSERT INTO `ip_address` VALUES('521', '2', '192.168.15.8', '255.255.254.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:02:20', '2026-01-12 09:02:20');
INSERT INTO `ip_address` VALUES('522', '2', '192.168.15.9', '255.255.254.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:02:20', '2026-01-12 09:02:20');
INSERT INTO `ip_address` VALUES('523', '2', '192.168.15.10', '255.255.254.0', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-', '2026-01-12 09:02:20', '2026-01-12 09:02:20');


CREATE TABLE `ip_network` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `rid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `from` varchar(255) NOT NULL,
  `to` varchar(255) NOT NULL,
  `subnet` varchar(255) NOT NULL,
  `router` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ip_network` VALUES('2', '1', '2', '1', '7 Network', '192.168.7.1', '192.168.7.254', '255.255.255.0', '', '2025-11-26 13:40:47', '2025-11-26 13:59:54');


CREATE TABLE `isp` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `isp_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `wan_ip` varchar(255) NOT NULL,
  `subnet` varchar(255) NOT NULL,
  `gateway` varchar(255) NOT NULL,
  `dns1` varchar(255) NOT NULL,
  `dns2` varchar(255) NOT NULL,
  `webmgmtpt` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `isp` VALUES('1', '1', '2', 'PLDT Inc.', 'Annex Free Wifi', '119.92.162.132', '255.255.255.224', '119.92.162.129', '58.71.1.37', '58.71.2.7', '7777', '2025-11-26 13:20:57', '2025-11-26 13:21:14');
INSERT INTO `isp` VALUES('2', '1', '2', 'Converge ICT Solutions Inc.', 'Planning Wifi Converge', '113.19.12.55', '255.255.255.224', '113.19.12.33', '161.49.49.49', '161.49.48.48', '18585', '2025-11-26 13:23:01', '2025-11-26 13:32:39');
INSERT INTO `isp` VALUES('3', '1', '2', 'Globe Telecom, Inc.', 'Planning Wifi Globe', '203.177.127.133', '255.255.224.0', '203.177.127.129', '202.126.40.5', '222.127.143.5', '18585', '2025-11-26 13:23:47', '2025-11-26 13:32:50');
INSERT INTO `isp` VALUES('4', '1', '2', 'Converge ICT Solutions Inc.', 'Main Bldg. Free Wifi Converge', '113.19.12.53', '255.255.255.224', '113.19.12.33', '161.49.49.49', '161.49.48.48', '1515', '2025-11-26 13:24:52', '2025-11-26 13:33:04');
INSERT INTO `isp` VALUES('5', '1', '2', 'Globe Telecom, Inc.', 'Main Bldg. Free Wifi Globe', '203.177.127.150', '255.255.255.224', '203.177.127.129', '202.126.40.5', '222.127.143.5', '1515', '2025-11-26 13:32:14', '2025-11-26 13:32:14');
INSERT INTO `isp` VALUES('6', '1', '2', 'PLDT Inc.', 'Pawing Free Wifi', '119.92.162.154', '255.255.255.224', '119.92.162.129', '58.71.1.37', '58.71.2.7', '2222', '2025-11-26 13:34:12', '2025-11-26 13:34:12');
INSERT INTO `isp` VALUES('7', '1', '2', 'Globe Telecom, Inc.', 'Palo Free Wifi', '222.127.142.186', '255.255.255.248', '222.127.142.185', '202.126.40.5', '222.127.143.5', '3333', '2025-11-26 13:34:55', '2025-11-26 13:34:55');
INSERT INTO `isp` VALUES('8', '1', '2', 'Converge ICT Solutions Inc.', 'Annex Pantry Wifi', '113.19.12.54', '255.255.255.224', '113.19.12.33', '161.49.49.49', '161.49.48.48', '-', '2025-11-26 13:35:59', '2025-11-26 13:35:59');
INSERT INTO `isp` VALUES('9', '1', '2', 'Globe Telecom, Inc.', 'Palo Unifi', '222.127.142.187', '255.255.255.248', '222.127.142.185', '202.126.40.5', '222.127.143.5', '-', '2025-11-26 13:36:59', '2025-11-26 13:36:59');


CREATE TABLE `logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `log` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `mac_address` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `wid` varchar(255) NOT NULL,
  `mac` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `device` varchar(255) NOT NULL,
  `project` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` VALUES('1', '2025_11_11_439826_user');
INSERT INTO `migrations` VALUES('2', '2025_11_11_519036_user_group');
INSERT INTO `migrations` VALUES('3', '2025_11_11_698673_equipment');
INSERT INTO `migrations` VALUES('4', '2025_11_11_798170_equipment_entry');
INSERT INTO `migrations` VALUES('5', '2025_11_11_874325_ip_network');
INSERT INTO `migrations` VALUES('6', '2025_11_11_983576_ip_address');
INSERT INTO `migrations` VALUES('7', '2025_11_11_058243_routers');
INSERT INTO `migrations` VALUES('8', '2025_11_11_635279_isp');
INSERT INTO `migrations` VALUES('9', '2025_11_11_727920_cctv_location');
INSERT INTO `migrations` VALUES('10', '2025_11_11_812210_cctv_camera');
INSERT INTO `migrations` VALUES('11', '2025_11_11_911728_settings');
INSERT INTO `migrations` VALUES('12', '2025_11_11_987767_logs');
INSERT INTO `migrations` VALUES('13', '2025_11_11_080392_mac_address');
INSERT INTO `migrations` VALUES('14', '2025_11_11_180123_wifi');
INSERT INTO `migrations` VALUES('15', '2025_11_11_256122_consumables');
INSERT INTO `migrations` VALUES('16', '2025_11_11_987803_voucher');
INSERT INTO `migrations` VALUES('17', '2026_06_01_339123_consumable_logs');


CREATE TABLE `routers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `subnet` varchar(255) NOT NULL,
  `wan1` varchar(255) NOT NULL,
  `wan2` varchar(255) NOT NULL,
  `active` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `routers` VALUES('1', '1', '2', 'Annex Free Wifi Router', '192.168.7.254', '255.255.254.0', '1', '-', '1', '2025-11-26 13:42:36', '2025-11-26 13:43:24');
INSERT INTO `routers` VALUES('2', '1', '2', 'Main Bldg. Free Wifi Router', '192.168.15.254', '255.255.254.0', '4', '5', '4', '2025-11-26 13:44:45', '2025-11-26 13:46:46');
INSERT INTO `routers` VALUES('3', '1', '2', 'Planning Router', '192.168.10.254', '255.255.255.0', '2', '3', '3', '2025-11-26 13:46:28', '2025-11-26 13:46:36');
INSERT INTO `routers` VALUES('4', '1', '2', 'Pawing Free Wifi Router', '192.168.22.254', '255.255.255.0', '6', '-', '6', '2025-11-26 13:47:23', '2025-11-26 13:47:27');
INSERT INTO `routers` VALUES('5', '1', '2', 'Palo Free Wifi Router', '192.168.35.254', '255.255.255.0', '7', '-', '7', '2025-11-26 13:47:58', '2025-11-26 13:48:07');
INSERT INTO `routers` VALUES('6', '1', '2', 'Annex Pantry Wifi Router', '192.168.37.254', '255.255.255.0', '8', '-', '8', '2025-11-26 13:48:52', '2025-11-26 13:48:57');


CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `sound` varchar(255) NOT NULL,
  `theme` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` VALUES('1', '_*', '2', '1', '0', '2026-01-13 10:29:43', '2026-01-13 10:29:43');


CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL,
  `email` varchar(1000) NOT NULL,
  `privileges` varchar(1000) NOT NULL,
  `passkey` varchar(1000) NOT NULL,
  `username` varchar(1000) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` VALUES('1', 'Paul Ian Dumdum', 'paulian.dumdum@gmail.com', 'Administrator', '8875', '703F_administrator', '$argon2id$v=19$m=65536,t=4,p=1$Y25PQ3ZzcXIxZTFFN2tJMg$HDKtuOVmW9VPJAzykq8rhtQFhEI6l9Jpz3HsIEYWLz4', '2025-11-11 17:04:27', '2026-01-13 09:18:43');
INSERT INTO `user` VALUES('2', 'Administrator', 'freewifiteam.leyte@gmail.com', 'Administrator', '3941', 'administrator', '$argon2id$v=19$m=65536,t=4,p=1$UWd0bHdaelBFQS9iMnBoZA$eejgkqQanF0n/UtfTx0sWnXBqm9aDVFVCn/Rq5HYTJE', '2025-11-12 08:48:26', '2026-01-13 10:05:53');
INSERT INTO `user` VALUES('3', 'Dacatimbang, Joseph F.', 'otepfabi@gmail.com', 'Supervisor', '2623', '114P', '$argon2id$v=19$m=65536,t=4,p=1$NEd6dk95T0w5U2hwUDdBOA$4yqztPURXhCi7aB0pnayJPFhypi2i3bGUUXl7kx7ynM', '2025-11-26 13:20:01', '2026-01-13 09:18:43');
INSERT INTO `user` VALUES('6', 'Abad, Kurt Aaron A.', 'freewifi.tech@datacapture2.com.ph', 'User', '8341', '039O', '$argon2id$v=19$m=65536,t=4,p=1$aHBWalJqVzVoMlluTnZjVw$QggcIRt8iNiam+r0HJRxjda1J1+L+ZlpzSoHemQNnik', '2026-01-06 13:35:20', '2026-01-13 10:13:53');
INSERT INTO `user` VALUES('7', 'Abas, Jairus A.', '-', 'User', '4297', '831P', '$argon2id$v=19$m=65536,t=4,p=1$LlJBd2FQNUN5SHBQS3ZqaQ$zgcjtRefzPPAJehiA2gGW9v747V8L2aYuYKr2G/x34o', '2026-01-06 13:36:11', '2026-01-13 09:18:44');
INSERT INTO `user` VALUES('8', 'Aguilar, Ramirr S.', '-', 'Supervisor', '8223', '255P', '$argon2id$v=19$m=65536,t=4,p=1$d3Z1czFpY0ROTEluUXFLTQ$JK7rnGgY1OcTFlfanBIsvTdcsA/+tL1eP1u1oH1svzg', '2026-01-06 13:36:34', '2026-01-13 09:18:44');
INSERT INTO `user` VALUES('9', 'Almaden. Stephen C.', '-', 'User', '7317', '849F', '$argon2id$v=19$m=65536,t=4,p=1$ZkFQazNHdUFpdm5NNXBZYQ$XV9iSMA01G0jjrCeru+XN/J4LhVxsuY7yQ3r/WppKDI', '2026-01-06 13:37:58', '2026-01-13 09:18:44');
INSERT INTO `user` VALUES('10', 'Apostol, Adam N.', '-', 'User', '5914', '113P', '$argon2id$v=19$m=65536,t=4,p=1$eTNZVmdyaElnbk1RRUhXLg$wnEFkvAselAib8K0cRH2DKXGQIGSQE0jGLcXAanHkxw', '2026-01-06 13:38:23', '2026-01-13 09:18:45');
INSERT INTO `user` VALUES('11', 'Batilaran, Christian N.', '-', 'User', '7647', 'I781', '$argon2id$v=19$m=65536,t=4,p=1$bk1XUUIuSzhJOERCODBrdw$HS+6WeJBH0h+pPT/zfab2s3pfojqy74Y1Ktslg0xrGc', '2026-01-06 13:39:10', '2026-01-13 10:21:25');
INSERT INTO `user` VALUES('12', 'Bohol, Edward', '-', 'User', '4747', '826F', '$argon2id$v=19$m=65536,t=4,p=1$emFtLnFNYnE3VzR6WVVwYg$MEE1TUlOSgZ3ZiJ8wWP5v8eLp5C1aeEQk3dsQlVqi+4', '2026-01-06 13:39:40', '2026-01-13 09:18:45');
INSERT INTO `user` VALUES('13', 'Capacite, John Rhyl C.', '-', 'User', '8342', '040O', '$argon2id$v=19$m=65536,t=4,p=1$SW5QQlI1RDI4NzEzVThKSA$xImkaw25HtWrXiET5zRALhQ1ovuztyjC5+h7QKuRt6A', '2026-01-06 13:40:40', '2026-01-13 09:18:46');
INSERT INTO `user` VALUES('14', 'Castil, Rea Mae G.', '-', 'User', '2444', '666S', '$argon2id$v=19$m=65536,t=4,p=1$a0pzT0hyT2xJLkhaWUswNw$YWWVUmXZl+pBesOEqEeTD2uybQyEQ+sa1RrLo13DOYQ', '2026-01-06 13:41:04', '2026-01-13 09:18:46');
INSERT INTO `user` VALUES('15', 'Cerniaz, Fredrick M.', '-', 'Supervisor', '5862', '777F', '$argon2id$v=19$m=65536,t=4,p=1$a0FGME1ET3R4NVNjMmRMdA$QJZn+ZQGUJpDnl/Ke99HjFvxi1s2teep+uNqplmkOYQ', '2026-01-06 13:41:40', '2026-01-13 09:18:46');
INSERT INTO `user` VALUES('16', 'Chiquillo, Nel Patrick J.', '-', 'User', '3147', '850F', '$argon2id$v=19$m=65536,t=4,p=1$UU1zSGZqaHhQeU9Uc3FZYQ$uGj5QmvbwjCYbBA8zpx5ePVdhcHNn82R9MCDGyLteO0', '2026-01-06 13:42:54', '2026-01-13 09:18:46');
INSERT INTO `user` VALUES('17', 'Collantes, Arjehn Paul M.', '-', 'User', '9934', '042P', '$argon2id$v=19$m=65536,t=4,p=1$bThYWmJHRzNJU2luRHk2dQ$/RavM7Ndlh8R/K3xakyqI/BtHtdKf6C39NrCgGbeCXo', '2026-01-06 13:43:30', '2026-01-13 09:18:47');
INSERT INTO `user` VALUES('18', 'Cueso, Emmanuel Juluis E.', '-', 'User', '2316', 'I783', '$argon2id$v=19$m=65536,t=4,p=1$ZWV2T3c3Q05CVGd2QnBGRg$sshwO/nO4zH+qxDSBs2Z+2mrkf8V7OYo94wSrdOzLi8', '2026-01-06 13:44:00', '2026-01-13 09:18:47');
INSERT INTO `user` VALUES('19', 'Daga, Marven A.', '-', 'User', '4524', '851F', '$argon2id$v=19$m=65536,t=4,p=1$eHR5cVRHRVdrZVBQT2ZPNg$OSqtkFCBNVznLSmNrfhkQCmBhYW61BVeJugmn4ebCEI', '2026-01-06 13:45:12', '2026-01-13 09:18:47');
INSERT INTO `user` VALUES('20', 'Delos Reyes, Regie L.', '-', 'User', '1932', '931F', '$argon2id$v=19$m=65536,t=4,p=1$UUZGdzF3U2lQVUlFYU4zWA$yH3hisjQjmhxNU0ZNQIJ4pIlkxqmluPL61EXI2jbgWQ', '2026-01-06 13:45:57', '2026-01-13 09:18:48');
INSERT INTO `user` VALUES('21', 'Digman, Fairod L.', '-', 'User', '6187', '947O', '$argon2id$v=19$m=65536,t=4,p=1$dnAualBJeWlUNnlrVjgwWQ$BoyIBE06Hg9MDONZDdhmh34m2Su+1SVuvofuJzIrCno', '2026-01-06 13:46:28', '2026-01-13 09:18:48');
INSERT INTO `user` VALUES('22', 'Dumdum, Paul Ian A.', '-', 'User', '3353', '703F', '$argon2id$v=19$m=65536,t=4,p=1$MGs4WEoxZGlGRW5mMW0wbQ$EK7fWOSmyEagR+/p8zS+RyX4KKSSTkZB4WBRa5HnMag', '2026-01-06 13:49:02', '2026-01-13 09:18:48');
INSERT INTO `user` VALUES('23', 'Flores, Sherwin G.', '-', 'User', '6167', '523F', '$argon2id$v=19$m=65536,t=4,p=1$RHN5N2QyRVBsN2pHWUJlUg$goZ/S0L+OiWGM4CGHRMmwofaXYvJEave/amGUZRARSk', '2026-01-06 13:50:12', '2026-01-13 09:18:48');
INSERT INTO `user` VALUES('24', 'Fuentes, Keven Charles P.', '-', 'User', '8426', '650S', '$argon2id$v=19$m=65536,t=4,p=1$LzRYaHFIWmhZLzZMUDZPTA$MIFkjk8XrevO2xu28Hv9KtlEyFUMrQODn38bz1owciQ', '2026-01-06 13:50:44', '2026-01-13 09:18:49');
INSERT INTO `user` VALUES('25', 'Germanes, Igmedio A.', '-', 'User', '4952', 'P889', '$argon2id$v=19$m=65536,t=4,p=1$bWlCNlFGbFNvRk8wejV5Qg$lGduSXfRo0wAKnTKbwuBWOBo7xQatlZI3zxy38CcYE0', '2026-01-06 13:53:16', '2026-01-13 09:18:49');
INSERT INTO `user` VALUES('26', 'Glory, Cejie L.', '-', 'User', '3819', 'O113', '$argon2id$v=19$m=65536,t=4,p=1$eWZ4ZWFYV2x2ejNLY3VuWA$+zqu0CH1syP6uRKh6u/CaPdBvRpTXhZbF+RCWpv70xA', '2026-01-06 13:53:41', '2026-01-13 09:18:49');
INSERT INTO `user` VALUES('27', 'Guantero, Darwin P.', '-', 'User', '1178', '997F', '$argon2id$v=19$m=65536,t=4,p=1$M2hUOG0zcWJzWVFjLk5GNQ$wdsum/i6Fx0Hlt0FwIa/9+6Sz//WLfs1qufDHi1R4Qw', '2026-01-06 13:54:17', '2026-01-13 09:18:49');
INSERT INTO `user` VALUES('28', 'Ladera, Jonnel M.', '-', 'User', '3454', '665P', '$argon2id$v=19$m=65536,t=4,p=1$RWxETnlVOWNwbGRNS0ZNRA$wT7kYRb+tZLBk4teUUNJWXxgJ8C3PRyRltbqO0CWkxQ', '2026-01-06 13:56:06', '2026-01-13 09:18:50');
INSERT INTO `user` VALUES('29', 'Malinao, Jude Michael C.', '-', 'User', '7657', '140O', '$argon2id$v=19$m=65536,t=4,p=1$YVJzMm10NlJxMFpyRWoxVA$sGS1Mc5aYZftJg+cC6uwmAgnh6px75qFK6G4jool0jU', '2026-01-06 13:56:31', '2026-01-13 09:18:50');
INSERT INTO `user` VALUES('30', 'Margallo, Marc Zandro S.', '-', 'User', '3141', '667S', '$argon2id$v=19$m=65536,t=4,p=1$a0ZBRnRmRnNhdUV3elNVMg$4404EmSrgEnmnfocDl+YhtTX0nMhDy45ab0trnzoANk', '2026-01-06 13:56:51', '2026-01-13 09:18:50');
INSERT INTO `user` VALUES('31', 'Megio, Alex Angelo R.', '-', 'Supervisor', '7229', '308F', '$argon2id$v=19$m=65536,t=4,p=1$NWZPd3FEaGViSTlpWmRnaw$qQ1aLOpOxhx/Ps8SeGsV0uayLMOKoFHKUdrKU1V9OX8', '2026-01-06 13:57:44', '2026-01-13 09:18:51');
INSERT INTO `user` VALUES('32', 'Modesto, Carlo V.', '-', 'User', '6581', 'O915', '$argon2id$v=19$m=65536,t=4,p=1$eFFJRUxWMkdjVElUTzVzVg$AfKlS6oqa/ZDafKGd1LWWjWW58PhAQGUwDNFuzenFE0', '2026-01-06 13:59:04', '2026-01-13 09:18:51');
INSERT INTO `user` VALUES('33', 'Monge, Jude M.', '-', 'User', '8568', '702F', '$argon2id$v=19$m=65536,t=4,p=1$LktPNkV2U09UWVp5MjRyMg$n3rFN/qj2ok0zvr2GzPqPHVQn1ryo+kvXFIIk4Ni0e8', '2026-01-06 13:59:24', '2026-01-13 09:18:51');
INSERT INTO `user` VALUES('34', 'Muñoz, Gelene C.', '-', 'User', '3133', '668S', '$argon2id$v=19$m=65536,t=4,p=1$b2ZyOC9oT01HSFI3SDlwaA$DSLZ5tKIwEgcZzOzgPb6KySio2HpN5ArcLawbpSt70c', '2026-01-06 14:00:24', '2026-01-13 09:18:51');
INSERT INTO `user` VALUES('35', 'Quiminales, Aljon A.', '-', 'User', '5732', 'O533', '$argon2id$v=19$m=65536,t=4,p=1$WU5oaW5xd29IckFmMWdWTw$xq4xc/tIv5wRTykpzTxvRbeVK9F25npSHTVEQuD+CII', '2026-01-06 14:00:51', '2026-01-13 09:18:52');
INSERT INTO `user` VALUES('36', 'Reposar, James Frederick R.', '-', 'Supervisor', '2485', 'O056', '$argon2id$v=19$m=65536,t=4,p=1$aVhFcFduUXU2RjN0YkFLeA$Sb0KS4xUypJqUQOk081JvS/kgU4IsQO2rIQRn8BAiZo', '2026-01-06 14:03:37', '2026-01-13 09:18:52');
INSERT INTO `user` VALUES('37', 'Rodriguez, Lovelyn A.', '-', 'User', '8338', '971P', '$argon2id$v=19$m=65536,t=4,p=1$OXRXSFNQb3lUOHYxYnJUVA$0qooTY8UWJeSuCEhOz6YupxrUyJF/kw0vbCCEP8XgC0', '2026-01-06 14:04:02', '2026-01-13 09:18:52');
INSERT INTO `user` VALUES('38', 'Rona, Joshua Melecio M.', '-', 'User', '8272', '864P', '$argon2id$v=19$m=65536,t=4,p=1$VC9oaGVFRTQ1dFJyNUhuWQ$lYo2Orj7a0OEgKCPCb6efPdEMQRxXejOU7HZppDp7Lw', '2026-01-06 14:04:41', '2026-01-13 09:18:53');
INSERT INTO `user` VALUES('39', 'Taala, Joel M.', '-', 'Supervisor', '2284', 'R847', '$argon2id$v=19$m=65536,t=4,p=1$YXBRZC5kUC5BckdyZlQ1Lg$ogUTNtjTsmIKhFgzj+47ageWs/31NY6Ut4dQSO3/tdk', '2026-01-06 14:05:31', '2026-01-13 09:18:53');
INSERT INTO `user` VALUES('40', 'Vacal, Lemuel C.', '-', 'Supervisor', '9626', 'R437', '$argon2id$v=19$m=65536,t=4,p=1$dmFBN2lpcldQM0w0ei80cA$6wZvR9jYvr/FblAahXJUshVHKZSoQDgEXio9DnVlnLU', '2026-01-06 14:06:02', '2026-01-13 09:18:53');


CREATE TABLE `user_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(1000) NOT NULL,
  `type` varchar(1000) NOT NULL,
  `supervisors` varchar(1000) NOT NULL,
  `users` varchar(1000) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_group` VALUES('1', 'Wifi Team', 'IT', '40|3|8', '22', '2025-11-11 17:06:17', '2026-01-13 10:19:27');
INSERT INTO `user_group` VALUES('3', 'FPOSI Tech - Leyte Main Bldg.', 'IT', '36', '6|7|33|34|37|10', '2026-01-06 14:09:13', '2026-01-13 10:19:44');
INSERT INTO `user_group` VALUES('4', 'LEYTE FPOSI NETWORK TEAM', 'IT', '39', '11|16|32', '2026-01-06 14:12:10', '2026-01-13 10:18:54');
INSERT INTO `user_group` VALUES('5', 'Annex 2nd Floor - IT TECH LEYTE', 'IT', '15', '9|12|19|21|25|26|28|30|38', '2026-01-06 14:37:57', '2026-01-13 10:19:56');
INSERT INTO `user_group` VALUES('6', 'Annex 1st Floor TECH', 'IT', '31', '13|14|17|20|23|24|27|29|35', '2026-01-06 14:46:16', '2026-01-13 10:20:06');
INSERT INTO `user_group` VALUES('7', 'FPOSIREAL TECH', 'IT', '|', '18', '2026-01-06 14:47:15', '2026-01-13 10:20:09');


CREATE TABLE `wifi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gid` varchar(255) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wifi` VALUES('1', '1', '1', 'DDC Device Wifi', 'Temp@2026!!', '2025-11-11 17:07:12', '2025-11-11 17:07:12');
INSERT INTO `wifi` VALUES('2', '1', '2', 'DDC Planning Wifi', 'Annex@2026!!', '2025-11-21 10:07:02', '2025-11-21 10:07:02');
INSERT INTO `wifi` VALUES('3', '1', '2', 'DDC Admin Wifi', 'Annex@2026!!', '2025-11-21 10:12:35', '2025-11-21 10:12:35');
INSERT INTO `wifi` VALUES('4', '1', '2', 'ITAdmin', '123@Temp!!', '2025-11-21 10:13:26', '2025-11-21 10:13:26');
INSERT INTO `wifi` VALUES('5', '1', '2', 'Accounting DDC', '123@Temp!!', '2025-11-21 10:13:56', '2025-11-21 10:13:56');
INSERT INTO `wifi` VALUES('6', '1', '2', 'DDC Guest Wifi', 'Guest@2026!!', '2025-11-21 10:16:08', '2025-11-21 10:16:08');
