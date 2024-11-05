-- MySQL dump 10.13  Distrib 5.5.8, for Win32 (x86)
--
-- Host: localhost    Database: logic_quick_costing
-- ------------------------------------------------------
-- Server version	5.5.8-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `lib_standard_cm_entry_history`
--

DROP TABLE IF EXISTS `lib_standard_cm_entry_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lib_standard_cm_entry_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `applying_period_date` date DEFAULT NULL,
  `applying_period_to_date` date DEFAULT NULL,
  `bep_cm` double NOT NULL,
  `asking_profit` double NOT NULL,
  `asking_cm` double NOT NULL,
  `monthly_cm_expense` double NOT NULL,
  `no_factory_machine` int(11) NOT NULL DEFAULT '0',
  `working_hour` double NOT NULL,
  `cost_per_minute` double NOT NULL,
  `asking_avg_rate` double NOT NULL,
  `inserted_by` int(11) NOT NULL DEFAULT '0',
  `insert_date` datetime DEFAULT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `update_date` datetime DEFAULT NULL,
  `status_active` tinyint(1) DEFAULT '1' COMMENT '0:No 1:Yes',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT '0:No 1:Yes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lib_standard_cm_entry_history`
--

LOCK TABLES `lib_standard_cm_entry_history` WRITE;
/*!40000 ALTER TABLE `lib_standard_cm_entry_history` DISABLE KEYS */;
INSERT INTO `lib_standard_cm_entry_history` VALUES (1,1,'2014-05-01','2014-05-10',6,11,17,2,10,3,0,4,1,'2014-05-13 16:36:11',0,NULL,1,0);
/*!40000 ALTER TABLE `lib_standard_cm_entry_history` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-02 11:25:49
