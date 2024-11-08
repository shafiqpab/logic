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
-- Table structure for table `lib_standard_cm_entry`
--

DROP TABLE IF EXISTS `lib_standard_cm_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lib_standard_cm_entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `applying_period_date` date DEFAULT NULL,
  `applying_period_to_date` date DEFAULT NULL,
  `bep_cm` double NOT NULL,
  `asking_profit` double NOT NULL,
  `max_profit` double NOT NULL,
  `asking_cm` double NOT NULL,
  `monthly_cm_expense` double NOT NULL,
  `no_factory_machine` int(11) NOT NULL DEFAULT '0',
  `working_hour` double NOT NULL,
  `cost_per_minute` double NOT NULL,
  `asking_avg_rate` double NOT NULL,
  `status_active` tinyint(1) DEFAULT '1' COMMENT '0:No 1:Yes',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT '0=No; 1=Yes',
  `inserted_by` int(11) NOT NULL DEFAULT '0',
  `insert_date` datetime DEFAULT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `update_date` datetime DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT '0' COMMENT '0=No; 1=Yes',
  `actual_cm` double NOT NULL,
  `depreciation_amorti` double NOT NULL,
  `interest_expense` double NOT NULL,
  `income_tax` double NOT NULL,
  `operating_expn` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `applying_period_date` (`applying_period_date`),
  KEY `applying_period_to_date` (`applying_period_to_date`),
  KEY `status_active` (`status_active`),
  KEY `is_deleted` (`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lib_standard_cm_entry`
--

LOCK TABLES `lib_standard_cm_entry` WRITE;
/*!40000 ALTER TABLE `lib_standard_cm_entry` DISABLE KEYS */;
INSERT INTO `lib_standard_cm_entry` VALUES (2,21,'2013-07-01','2014-10-31',2.4,10,0,12.4,9000000,200,8,3.69,2.56,0,1,2,'2012-10-09 00:00:00',1,'2013-12-23 11:04:36',0,0,0,0,0,0),(3,8,'2013-09-12','2013-10-31',6,7,0,13,5000000,40,8,10.016,2.85,1,0,2,'2013-05-26 10:46:34',1,'2015-01-07 14:01:09',0,0,5,0,0,5),(4,3,'2013-08-01','2014-11-30',5,8,0,13,8000000,300,10,1.7094,0,0,1,2,'2013-05-26 10:47:08',1,'2014-03-11 15:48:57',0,0,0,0,0,0),(7,18,'2013-09-01','2013-12-31',5,10,0,15,6000000,300,8,1.6026,0,0,1,1,'2013-09-28 12:25:38',1,'2014-03-18 14:14:08',0,0,0,0,0,0),(8,13,'2013-09-01','2014-02-28',14,10,0,24,25000000,220,8,9.1055,2.5,0,1,1,'2013-09-28 13:15:52',1,'2014-06-24 17:37:43',0,0,0,0,0,0),(9,6,'2013-10-01','2014-02-28',15,45,5,60,50000000,2000,8,2.0032,2,1,0,1,'2013-10-12 14:34:50',1,'2015-01-07 14:01:15',0,45,0,0,0,5),(10,5,'2013-10-01','2016-03-30',20,45,0,65,20000000,500,5,5.1282,2,1,0,1,'2013-10-12 15:30:44',1,'2014-12-23 17:22:50',0,4,5,0,2,0),(11,25,'2013-10-01','2014-10-31',15,8,0,23,5000000,200,8,2.0032,0,1,0,1,'2013-10-26 12:58:01',1,'2013-12-11 09:00:29',0,0,0,0,0,0),(12,17,'2013-11-01','2014-12-31',4,4,0,8,8000000,250,8,2.5641,0,1,0,1,'2013-11-05 13:09:15',0,NULL,0,0,0,0,0,0),(13,20,'2013-11-01','2013-11-30',4,6,0,10,20000000,500,10,2.5641,0,0,1,1,'2013-11-09 10:13:46',1,'2014-06-25 10:00:51',0,0,0,0,0,0),(14,15,'2013-01-01','2013-12-31',12,10,0,22,30000000,1024,10,10,2,0,1,1,'2013-11-13 10:47:48',1,'2014-06-25 10:00:55',0,0,0,0,0,0),(15,12,'2013-11-19','2014-05-31',8,6,0,14,50000000,500,8,8.0128,4,0,1,1,'2013-11-19 19:14:52',1,'2015-01-19 14:47:11',0,5,5,0,0,0),(16,35,'2013-01-01','2014-06-30',8,8,0,16,50000000,780,10,4.1091,3.5,1,0,1,'2013-11-28 09:29:50',1,'2014-01-25 09:04:11',0,0,0,0,0,0),(17,27,'2013-12-17','2013-12-09',4,4,0,8,3,3,4,0.0002,3,0,1,1,'2013-12-14 17:45:32',1,'2013-12-30 13:30:40',0,0,0,0,0,0),(19,24,'0000-00-00','0000-00-00',13,7,0,20,5000000,200,8,2.0032,3,1,0,1,'2013-12-15 12:36:09',0,NULL,0,0,0,0,0,0),(20,28,'2013-12-01','2013-12-31',3,5,0,8,80000,300,8,0.0214,4,1,0,1,'2013-12-17 09:46:48',1,'2013-12-17 09:47:33',0,0,0,0,0,0),(21,26,'2013-12-01','2014-02-28',7,5,0,12,50000000,500,8,8.0128,8,1,0,1,'2013-12-17 15:37:13',0,NULL,0,0,0,0,0,0),(22,32,'2013-12-01','2014-12-31',10,10,0,20,100000000,1250,8,6.4103,3,1,0,1,'2013-12-18 13:50:39',1,'2013-12-30 17:02:39',0,0,0,0,0,0),(23,21,'2013-10-01','2014-04-30',2,10,0,12,800000,150,8,0.4274,3,1,0,1,'2013-12-23 11:06:55',1,'2013-12-23 11:07:04',0,0,0,0,0,0),(24,4,'2013-12-23','2014-03-10',8,7,0,15,5000000,150,8,2.6709,3,1,0,1,'2013-12-23 11:14:46',0,NULL,0,0,0,0,0,0),(25,33,'2013-10-01','2014-06-30',8,15,0,23,5000000,120,8,4,3,1,0,1,'2013-12-31 10:10:02',1,'2014-04-03 13:08:44',0,0,0,0,0,0),(26,34,'2014-01-01','2014-07-31',10,15,0,25,10000000,250,8,3.2051,2.5,1,0,1,'2014-01-12 11:12:35',1,'2014-09-18 14:24:35',0,4,0,0,0,0),(27,19,'2014-01-01','2015-01-01',20,15,0,35,10000000,200,10,3.2051,0.35,1,0,1,'2014-01-28 17:58:52',1,'2014-03-31 13:57:34',0,0,0,0,0,0),(28,36,'2014-01-01','2014-12-31',10,15,0,25,50000000,1500,8,2.6709,2.5,1,0,1,'2014-02-27 14:04:18',1,'2014-03-20 10:09:54',0,0,0,0,0,0),(29,3,'2014-03-12','2014-03-20',10,15,0,25,15000000,300,10,3.2051,3,1,0,1,'2014-03-11 15:49:23',1,'2014-04-15 09:37:54',0,0,0,0,0,0),(30,39,'2014-01-01','2015-09-30',12,5,0,17,50000000,450,10,7.1225,2.5,1,0,24,'2014-04-05 12:41:09',24,'2014-04-05 12:43:23',0,0,0,0,0,0),(31,11,'2014-05-01','2014-05-31',20000,1.5,0,20001.5,50000,25,8,0.1603,3,1,0,1,'2014-05-10 11:00:57',1,'2014-08-24 15:58:49',0,0,0,0,0,0),(32,1,'2014-05-15','2014-05-20',6,11,0,17,2,10,3,0,4,1,0,1,'2014-05-13 09:40:32',1,'2014-05-13 16:36:11',0,0,0,0,0,0),(33,37,'2014-05-01','2024-05-15',5,10,0,15,1500000,300,10,0.3205,1.5,1,0,1,'2014-05-15 09:57:09',0,NULL,0,0,0,0,0,0),(34,40,'2014-01-01','2021-09-30',10,15,0,25,50000000,200,10,16.0256,5,0,1,1,'2014-05-21 19:01:06',1,'2014-06-25 10:30:18',0,0,0,0,0,0),(38,40,'0000-00-00','0000-00-00',10,15,0,25,50000000,200,10,16.0256,5,1,0,1,'2014-06-24 18:58:00',0,NULL,0,5,0,0,0,0),(39,8,'0000-00-00','0000-00-00',6,7,0,13,5000000,200,8,2.0032,2.85,1,0,1,'2014-06-24 19:05:30',0,NULL,0,5,0,0,0,0),(40,8,'0000-00-00','0000-00-00',6,7,0,13,5000000,200,8,2.0032,2.85,1,0,1,'2014-06-24 19:06:31',0,NULL,0,5,0,0,0,0),(41,6,'0000-00-00','0000-00-00',15,10,0,25,50000000,2000,8,2.0032,2,1,0,1,'2014-06-24 16:54:02',0,NULL,0,3,0,0,0,0),(42,6,'0000-00-00','0000-00-00',15,10,0,25,50000000,2000,8,2.0032,2,1,0,1,'2014-06-24 16:54:35',0,NULL,0,3,0,0,0,0),(43,1,'2014-06-01','2014-06-30',4,4,0,8,4,4,4,0.0002,4,0,1,1,'2014-06-24 17:35:59',1,'2014-06-24 17:36:40',0,4,0,0,0,0),(44,8,'2014-06-01','2014-06-30',6,7,0,13,5000000,200,8,2.0032,2.85,0,1,1,'2014-06-24 17:36:52',1,'2014-06-25 10:29:36',0,5,0,0,0,0),(46,5,'0000-00-00','0000-00-00',10,10,0,20,20000000,500,8,7.7,2,1,0,1,'2014-06-25 15:55:23',0,NULL,0,10,0,0,0,0),(47,12,'0000-00-00','0000-00-00',10,15,0,25,1000000,1400,10,0.0458,2,0,1,1,'2014-06-25 15:56:29',1,'2014-06-25 10:01:12',0,4.5,0,0,0,0),(48,12,'0000-00-00','0000-00-00',10,15,0,25,1000000,1400,10,0.0458,2,0,1,1,'2014-06-25 15:56:58',1,'2014-06-25 10:01:05',0,4.5,0,0,0,0),(49,12,'2014-06-01','2014-06-30',10,15,0,25,1000000,1400,10,0.0458,2,0,1,1,'2014-06-25 10:00:08',1,'2014-06-25 10:00:45',0,4.5,0,0,0,0),(50,12,'2014-06-01','2014-06-30',3,3,0,6,3,3,3,0.0002,3,0,1,1,'2014-06-25 10:01:32',1,'2014-06-25 10:29:12',0,3,0,0,0,0),(51,12,'2014-07-01','2014-07-31',10,10,0,20,20500000,300,10,4.3803,3.5,1,0,1,'2014-06-25 10:01:50',1,'2014-12-23 17:23:49',0,4,5,0,0,0),(52,24,'2014-06-01','2014-06-30',3,3,0,6,3,3,3,0.0002,3,1,0,1,'2014-06-25 10:03:17',0,NULL,0,3,0,0,0,0),(54,1,'2014-07-01','2014-07-31',10,15,0,25,500000,1500,8,0.0267,3,1,0,1,'2014-06-25 10:06:41',1,'2014-06-25 10:32:43',0,4,0,0,0,0),(55,2,'2014-08-01','2014-08-31',4,4,4,8,4,4,4,0.0002,4,1,0,1,'2014-08-11 15:47:31',1,'2015-01-07 14:01:32',0,0,5,0,0,5),(56,43,'2014-09-01','2014-09-30',10,15,0,25,20000000,1000,8,1.6026,2,1,0,1,'2014-08-11 15:49:43',1,'2014-08-12 10:18:29',0,0,0,0,0,0),(57,1,'2014-05-01','2014-12-31',10,10,0,20,50000000,500,10,6.4103,4,1,0,1,'2014-09-01 13:16:41',1,'2014-09-18 14:44:01',0,5,0,0,0,0),(58,34,'2014-02-01','2014-02-28',10,15,0,25,10000000,250,8,3.2051,2.5,1,0,1,'2014-09-18 14:32:12',0,NULL,0,4,0,0,0,0),(59,34,'2014-03-01','2014-03-31',10,15,0,25,10000000,250,8,3.2051,2.5,1,0,1,'2014-09-18 14:32:35',0,NULL,0,4,0,0,0,0),(60,34,'2014-04-01','2014-04-30',10,15,0,25,10000000,250,8,3.2051,2.5,1,0,1,'2014-09-18 14:32:59',1,'2014-09-20 09:10:28',0,5,0,0,0,0),(61,1,'2014-09-01','2014-09-30',10,10,100,20,50000000,500,10,6.4103,4,1,0,1,'2014-09-18 14:45:17',1,'2014-09-20 09:11:11',0,5,0,0,0,0),(62,1,'2014-12-01','2014-12-31',7,15,0,22,20000000,500,8,3.2051,2.5,1,0,1,'2014-12-15 12:08:26',0,NULL,0,3,0,0,0,0),(63,2,'2015-01-01','2015-01-31',5,10,0,15,300000,1000,8,0.024,2,1,0,1,'2015-01-08 12:37:06',0,NULL,0,2.3,5,3,3,0),(64,2,'2014-12-01','2014-12-31',4,4,4,8,4,4,4,0.0002,4,1,0,1,'2015-01-08 12:37:59',0,NULL,0,0,5,0,0,5),(65,33,'2015-01-01','2015-01-31',8,15,0,23,5000000,120,8,4,3,1,0,1,'2015-01-18 17:17:14',1,'2015-02-01 09:29:37',0,0,5,2,15,2),(66,12,'2015-01-01','2015-01-31',10,10,0,20,20500000,300,10,4.3803,3.5,1,0,1,'2015-01-19 14:51:30',0,NULL,0,4,5,0,0,0),(67,33,'2015-02-01','2015-02-28',8,15,0,23,5000000,120,8,4,3,1,0,1,'2015-02-01 09:31:15',0,NULL,0,0,5,2,15,2),(68,2,'2015-05-01','2015-05-31',20,12,15,32,2000,600,12,0.0002,10,1,0,1,'2015-05-18 11:44:09',0,NULL,0,250,2,15,10,5),(69,1,'2015-11-01','2015-11-30',10,10,100,20,50000000,500,10,6.4103,4,1,0,1,'2015-11-27 19:10:41',0,NULL,0,5,0,0,0,0),(70,1,'2015-07-01','2015-07-31',6,11,0,17,2,10,3,0,4,1,0,1,'2015-11-27 19:11:09',0,NULL,0,0,0,0,0,0),(71,1,'2016-01-01','2016-01-31',5,5,0,10,20000000,1123,10,4.6666,1.7,1,0,1,'2015-12-23 10:02:04',0,NULL,0,3,0,0,0,0),(72,1,'2016-07-01','2016-07-31',5,5,30,10,10000000,300,10,2.1368,2,1,0,1,'2016-07-14 15:25:08',0,NULL,0,3,1,1,1,1),(73,2,'2016-07-01','2016-07-31',4,6,30,10,10000000,500,10,1.2821,1.5,1,0,1,'2016-07-14 15:26:22',0,NULL,0,2,1,1,1,1),(74,12,'2016-07-01','2016-07-31',5,5,0,10,20000000,2500,10,0.5128,2,1,0,1,'2016-07-21 16:32:20',0,NULL,0,0,0,0,0,0);
/*!40000 ALTER TABLE `lib_standard_cm_entry` ENABLE KEYS */;
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
