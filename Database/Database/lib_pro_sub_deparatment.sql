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
-- Table structure for table `lib_pro_sub_deparatment`
--

DROP TABLE IF EXISTS `lib_pro_sub_deparatment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lib_pro_sub_deparatment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sub_department_name` varchar(100) NOT NULL,
  `department_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `inserted_by` int(3) DEFAULT NULL,
  `insert_date` datetime DEFAULT NULL,
  `update_by` int(3) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `status_active` int(2) NOT NULL,
  `is_deleted` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lib_pro_sub_deparatment`
--

LOCK TABLES `lib_pro_sub_deparatment` WRITE;
/*!40000 ALTER TABLE `lib_pro_sub_deparatment` DISABLE KEYS */;
INSERT INTO `lib_pro_sub_deparatment` VALUES (1,'Shirt',4,12,1,'2014-01-01 12:00:50',1,'2014-06-24 19:35:01',1,0),(2,'Pant',1,141,1,'2014-01-01 12:20:31',1,'2014-03-11 11:00:54',1,0),(3,'trouser',7,12,1,'2014-01-01 12:22:30',1,'2014-01-01 15:47:36',0,1),(4,'ad',2,14,1,'2014-01-01 12:24:00',1,'2014-01-01 14:50:38',0,1),(5,'salman',4,61,1,'2014-01-01 14:23:34',24,'2014-04-07 08:56:52',1,0),(6,'Shirt',1,53,1,'2014-01-04 11:25:56',NULL,NULL,1,0),(7,'Pant',1,53,1,'2014-01-04 11:26:11',NULL,NULL,1,0),(8,'T- Shirt',1,4,1,'2014-01-04 14:05:03',1,'2014-01-16 10:43:42',1,0),(9,'aaaa',1,10,1,'2014-01-06 09:05:21',NULL,NULL,1,0),(10,'rrrr',1,44,1,'2014-01-06 09:06:09',NULL,NULL,1,0),(11,'Tee Shirt',1,133,1,'2014-01-28 18:04:56',1,'2014-05-04 12:14:31',1,0),(12,'Stromphe',2,14,1,'2014-01-28 18:05:25',NULL,NULL,1,0),(13,'X',1,139,1,'2014-02-27 13:54:29',1,'2014-02-27 13:55:55',1,0),(14,'XX',1,139,1,'2014-02-27 13:56:15',NULL,NULL,1,0),(15,'sub pant',2,61,1,'2014-03-11 16:07:35',1,'2014-03-11 16:08:04',1,0),(16,'aaa',1,129,1,'2014-03-23 12:00:13',NULL,NULL,1,0),(17,'Polo Shirt',1,133,1,'2014-05-04 12:14:58',NULL,NULL,1,0),(18,'Boys',5,12,1,'2014-06-24 19:22:13',NULL,NULL,1,0),(19,'yyyyyy',1,136,1,'2014-07-02 12:11:42',1,'2014-07-02 12:11:48',0,1),(20,'Shirt',4,153,1,'2014-08-11 14:11:57',1,'2014-08-11 14:12:21',1,0);
/*!40000 ALTER TABLE `lib_pro_sub_deparatment` ENABLE KEYS */;
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
