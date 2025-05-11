-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.22-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.6.0.6814
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for ark_apartment_db
CREATE DATABASE IF NOT EXISTS `ark_apartment_db` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `ark_apartment_db`;

-- Dumping structure for table ark_apartment_db.apartments
CREATE TABLE IF NOT EXISTS `apartments` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `apartment_no` varchar(50) NOT NULL,
  `category_id` int(30) NOT NULL,
  `description` text NOT NULL,
  `price` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table ark_apartment_db.apartments: ~3 rows (approximately)
INSERT INTO `apartments` (`id`, `apartment_no`, `category_id`, `description`, `price`) VALUES
	(3, '54', 4, 'r3er3', 555555),
	(4, '55', 1, 'grege', 50000),
	(6, '11', 1, 'This is a duplex room self contained', 160000);

-- Dumping structure for table ark_apartment_db.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table ark_apartment_db.categories: ~4 rows (approximately)
INSERT INTO `categories` (`id`, `name`) VALUES
	(1, 'Duplex'),
	(2, 'Single-Family Home'),
	(3, 'Multi-Family Home'),
	(4, '2-story house');

-- Dumping structure for table ark_apartment_db.chat
CREATE TABLE IF NOT EXISTS `chat` (
  `chat_id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_msg_id` int(11) NOT NULL,
  `outgoing_msg_id` int(11) NOT NULL,
  `msg` longtext NOT NULL,
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

-- Dumping data for table ark_apartment_db.chat: ~3 rows (approximately)
INSERT INTO `chat` (`chat_id`, `incoming_msg_id`, `outgoing_msg_id`, `msg`, `date_sent`) VALUES
	(25, 4, 1, 'Welcome to ARK Apartments. Feel Free to ask and we will support.', '2024-04-28 23:02:29'),
	(26, 1, 4, 'Alright thanks', '2024-04-28 23:44:45'),
	(27, 1, 4, 'I will reach out', '2024-04-28 23:47:04');

-- Dumping structure for table ark_apartment_db.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) DEFAULT NULL,
  `apartment_id` int(11) DEFAULT NULL,
  `payment_due` date DEFAULT NULL,
  `status` enum('Not Paid','Paid') DEFAULT 'Not Paid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table ark_apartment_db.notifications: ~1 rows (approximately)
INSERT INTO `notifications` (`id`, `tenant_id`, `apartment_id`, `payment_due`, `status`) VALUES
	(3, 4, 6, '2024-04-30', 'Paid');

-- Dumping structure for table ark_apartment_db.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `tenant_id` int(30) NOT NULL,
  `apartment_no` int(30) NOT NULL,
  `amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `invoice` varchar(50) NOT NULL,
  `date_created` date NOT NULL,
  `time_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table ark_apartment_db.payments: ~1 rows (approximately)
INSERT INTO `payments` (`id`, `tenant_id`, `apartment_no`, `amount`, `invoice`, `date_created`, `time_created`) VALUES
	(4, 4, 11, 160000.00, '', '2024-04-29', '2024-04-29 01:30:32');

-- Dumping structure for table ark_apartment_db.system_settings
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `cover_img` text NOT NULL,
  `about_content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table ark_apartment_db.system_settings: ~1 rows (approximately)
INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `cover_img`, `about_content`) VALUES
	(1, 'Advanced Tenant Payment & Support System', 'arkapartments@gmail.com', '+265999999999', '1714231860_chart (6).png', '');

-- Dumping structure for table ark_apartment_db.tenantapartment
CREATE TABLE IF NOT EXISTS `tenantapartment` (
  `apartment_id` int(30) NOT NULL,
  `tenant_id` int(30) NOT NULL,
  `date_in` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = active, 0 = inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table ark_apartment_db.tenantapartment: ~1 rows (approximately)
INSERT INTO `tenantapartment` (`apartment_id`, `tenant_id`, `date_in`, `status`) VALUES
	(6, 4, '2024-04-26', 1);

-- Dumping structure for table ark_apartment_db.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `username` varchar(200) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `middlename` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(50) NOT NULL DEFAULT '',
  `contact` varchar(200) NOT NULL,
  `user_type` enum('1','2') NOT NULL,
  `status` enum('Offline','Online') NOT NULL DEFAULT 'Offline',
  `apartment_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = active, 0 = inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table ark_apartment_db.users: ~3 rows (approximately)
INSERT INTO `users` (`id`, `name`, `username`, `firstname`, `middlename`, `lastname`, `email`, `password`, `contact`, `user_type`, `status`, `apartment_status`) VALUES
	(1, 'Administrator', 'admin', '', '', '', '', '0192023a7bbd73250516f069df18b500', '', '1', 'Offline', 1),
	(4, '', '', 'Appie', 'David', 'Mbewe', 'apiembewe@gmail.com', '12345678', '0999999999', '2', 'Offline', 1),
	(6, 'Administrator25', 'admin2', '', '', '', '', '25d55ad283aa400af464c76d713c07ad', '', '1', 'Offline', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
