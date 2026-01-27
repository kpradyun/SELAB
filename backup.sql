/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.15-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: tgspdcl
-- ------------------------------------------------------
-- Server version	10.11.15-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bills`
--

DROP TABLE IF EXISTS `bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_number` varchar(20) NOT NULL,
  `service_number` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `units_consumed` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `fine_amount` decimal(10,2) DEFAULT 0.00,
  `paid_status` enum('PAID','UNPAID') DEFAULT 'UNPAID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bill_number` (`bill_number`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bills`
--

LOCK TABLES `bills` WRITE;
/*!40000 ALTER TABLE `bills` DISABLE KEYS */;
INSERT INTO `bills` VALUES
(1,'HO696FAC1B806EA','HH696FA75B3A2C0','2026-01-20','2026-01-20',100,150.00,0.00,'PAID'),
(2,'HOU24824','HH696FA75B3A2C0','2026-01-21','2026-01-21',100,150.00,0.00,'PAID');
/*!40000 ALTER TABLE `bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `role` enum('EMPLOYEE','SUPERVISOR','ADMIN') NOT NULL,
  `password` varchar(255) NOT NULL,
  `registration_date` date DEFAULT curdate(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES
(1,'Pradyun','admin@example.com','9876543210','ADMIN','ailab','2026-01-20'),
(2,'Uday','uday@gmail.com','8885584002','EMPLOYEE','ailab','2026-01-20');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_number` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `address` text NOT NULL,
  `pincode` varchar(6) NOT NULL,
  `connection_type` enum('HOUSEHOLD','COMMERCIAL','INDUSTRY') NOT NULL,
  `previous_reading` int(11) DEFAULT 0,
  `current_reading` int(11) DEFAULT 0,
  `registration_date` date DEFAULT curdate(),
  `last_bill_date` date DEFAULT NULL,
  `fine` decimal(10,2) DEFAULT 0.00,
  `bill_status` enum('PAID','UNPAID') DEFAULT 'UNPAID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_number` (`service_number`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'HH696FA75B3A2C0','Ram','ram@gmail.com','9988776655','Adilabad','500478','HOUSEHOLD',200,0,'2026-01-20','2026-01-21',0.00,'PAID'),
(2,'TEST3777','Test User 1','test3196@example.com','9642423448','Address 1','123456','HOUSEHOLD',0,0,'2026-01-27',NULL,0.00,'UNPAID'),
(3,'TEST4967','Test User 2','test3393@example.com','9642423448','Address 1','123456','HOUSEHOLD',0,0,'2026-01-27',NULL,0.00,'UNPAID'),
(4,'1650749403','Test Household','hh@test.com','9000058113','Addr','123456','HOUSEHOLD',0,0,'2026-01-27',NULL,0.00,'UNPAID'),
(5,'2997480143','Test Household','com@test.com','9000041133','Addr','123456','COMMERCIAL',0,0,'2026-01-27',NULL,0.00,'UNPAID'),
(6,'3177702873','Test Household','ind@test.com','9000070415','Addr','123456','INDUSTRY',0,0,'2026-01-27',NULL,0.00,'UNPAID'),
(7,'1826421714','Test Household','hh_refactor@test.com','8000084083','Addr','123456','HOUSEHOLD',0,0,'2026-01-27',NULL,0.00,'UNPAID'),
(8,'2234293175','Test Household','com_refactor@test.com','8000037952','Addr','123456','COMMERCIAL',0,0,'2026-01-27',NULL,0.00,'UNPAID'),
(9,'3140929843','Test Household','ind_refactor@test.com','8000070998','Addr','123456','INDUSTRY',0,0,'2026-01-27',NULL,0.00,'UNPAID');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-27 22:44:10
