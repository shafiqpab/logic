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
-- Table structure for table `user_passwd`
--

DROP TABLE IF EXISTS `user_passwd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_passwd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(15) DEFAULT NULL,
  `user_name` varchar(25) DEFAULT NULL,
  `user_email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `user_full_name` varchar(50) NOT NULL COMMENT 'new add by reza',
  `designation` int(11) NOT NULL DEFAULT '0' COMMENT 'new add by reza',
  `created_on` date NOT NULL,
  `created_by` varchar(25) DEFAULT NULL,
  `access_ip` varchar(25) NOT NULL,
  `access_proxy_ip` varchar(25) NOT NULL,
  `expire_on` date DEFAULT '0000-00-00',
  `user_level` int(2) NOT NULL DEFAULT '0',
  `buyer_id` varchar(500) NOT NULL,
  `supplier_id` varchar(500) NOT NULL,
  `unit_id` varchar(500) NOT NULL,
  `company_location_id` varchar(100) NOT NULL,
  `store_location_id` varchar(100) NOT NULL,
  `item_cate_id` varchar(100) NOT NULL,
  `is_data_level_secured` int(1) NOT NULL DEFAULT '0',
  `valid` int(1) NOT NULL DEFAULT '0',
  `department_id` int(11) NOT NULL DEFAULT '0',
  `is_fst_time` int(2) NOT NULL DEFAULT '0',
  `reset_code` int(10) NOT NULL,
  `graph_id` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`),
  KEY `user_name` (`user_name`),
  KEY `password` (`password`),
  KEY `buyer_id` (`buyer_id`(255)),
  KEY `is_data_level_secured` (`is_data_level_secured`),
  KEY `valid` (`valid`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_passwd`
--

LOCK TABLES `user_passwd` WRITE;
/*!40000 ALTER TABLE `user_passwd` DISABLE KEYS */;
INSERT INTO `user_passwd` VALUES (1,'11','sumon','','4OHc1teUkZg=','Md.Sumon Rahman',10,'2014-12-23','1','','','0000-00-00',2,'136,120','','2,7,23','','','',0,1,0,0,0,0),(2,NULL,'mamoor','','3+HTyNLRwA==','',0,'0000-00-00','1','','','0000-00-00',2,'0','','0','','','',0,1,0,0,0,0),(3,NULL,'enam','','np6imw==','Enamul',19,'0000-00-00','1','','','0000-00-00',1,'26','','2','','','',0,1,0,0,0,0),(4,NULL,'rana','','np6imw==','Rana',27,'0000-00-00','1','','','0000-00-00',1,'14,26,12,25,6','','12,2,7,23,8,3,33,34,11,6,32,24,1,19','','','',1,1,0,0,0,0),(5,NULL,'nasir','','np6imw==','Nasir Hojja',19,'0000-00-00','1','','','0000-00-00',1,'34','','0','','','',0,1,0,0,0,0),(6,NULL,'abc','','np6i','',0,'0000-00-00','1','','','0000-00-00',1,'34','','5','','','',1,1,0,0,0,0),(7,NULL,'dada','','np6imw==','Beeresh',29,'0000-00-00','4','','','0000-00-00',1,'0','','0','','','',0,1,0,0,0,0),(8,NULL,'rana007','','np6imw==','',0,'0000-00-00','1','','','0000-00-00',1,'14','','2','','','',1,1,0,0,0,0),(9,NULL,'hossain','','np6imw==','',0,'0000-00-00','1','','','0000-00-00',1,'0','','0','','','',0,1,0,0,0,0),(10,NULL,'mahamud','','np6imw==','',0,'0000-00-00','1','','','0000-00-00',4,'14','','0','','','',1,1,0,0,0,0),(11,NULL,'rahman','','383X1MrR','',0,'0000-00-00','1','','','0000-00-00',1,'14,53,26,30','','2,7,8,17','','','',1,1,0,0,0,0),(12,NULL,'tusar','','np6imw==','',0,'0000-00-00','1','','','0000-00-00',4,'14,53,26','','2','','','',1,1,0,0,0,0),(13,NULL,'faisal','','np6imw==','',0,'0000-00-00','1','','','0000-00-00',4,'12,6','','2','','','',1,1,0,0,0,0),(14,NULL,'ss','','np6im54=','',0,'0000-00-00','1','','','0000-00-00',1,'61,14,53,26,30,12,6,34,40,49,25,15,','','16','','','',1,1,0,0,0,0),(15,NULL,'shabbir','','np6i','',0,'0000-00-00','1','','','0000-00-00',1,'61,14,53,26,30,12,6,34,40,49,25,15,','','22','','','',1,1,0,0,0,0),(16,NULL,'Masum','','np6i','Masum ahmad',32,'0000-00-00','1','','','0000-00-00',1,'','','','','','',1,1,0,0,0,0),(17,NULL,'al amin','','np6im56Zlp2r','',0,'0000-00-00','1','','','0000-00-00',1,'117,12,6,34,65','','2','','','',1,1,0,0,0,0),(19,NULL,'reza','','np6im56Z','Saidul',19,'0000-00-00','1','','','0000-00-00',1,'','','','','','',1,1,0,0,0,0),(20,NULL,'3373','','np6imw==','md fakhrul islam prodan',29,'0000-00-00','1','','','2014-04-10',4,'6','','2','','','',1,1,0,0,0,0),(21,NULL,'Firoz Mukul','','09Xh1uOUkZg=','Firoz Mukul',10,'0000-00-00','1','','','0000-00-00',1,'131,130','','33','','','',1,1,0,0,0,0),(22,NULL,'Logic','','udvW0Mw=','Software',19,'2016-04-27','1','','','0000-00-00',1,'','','','','','',1,1,0,0,0,0),(23,NULL,'haque','','np6imw==','Logic',24,'0000-00-00','1','','','0000-00-00',1,'','','','','','',1,1,0,0,0,0),(24,NULL,'alamin','','np6imw==','alamin',19,'0000-00-00','1','','','0000-00-00',1,'35,134','','2,1','','','',1,1,0,0,0,0),(25,NULL,'khan','','np6im54=','khan',24,'0000-00-00','1','','','0000-00-00',1,'12,48,39','','12,2,7','','','',1,1,0,0,0,0),(26,NULL,'asd','','oJ6g','alamin',10,'0000-00-00','1','','','0000-00-00',1,'61,125,33,56,52,134','','12,1,11,37','','','',1,1,0,0,0,0),(27,NULL,'bdsaeed','','z9DiyM7Iww==','Saeed Khan',27,'0000-00-00','1','','','0000-00-00',1,'48','','5','','','',1,1,0,0,0,0),(28,NULL,'mnbv','','oJ6g','mnbv',10,'0000-00-00','1','','','0000-00-00',1,'61,30,12,65,125,25,33,29,48','','12,2,35,7,23,11,6,32,19,1,24,38,31,','','','',1,1,0,0,0,0),(29,NULL,'ashik','','np6i','ashik',19,'0000-00-00','1','','','0000-00-00',2,'12,138,65,35,134,140,141,13','','33,11,6,1,19,36','','','',1,1,0,0,0,0),(30,NULL,'Atik','','np6ilw==','Alamin',26,'0000-00-00','1','','','0000-00-00',1,'125,33,48,56,4,52','','12,3,36,31','','','',1,1,0,0,0,0),(31,NULL,'mim','','2tXcmJuW','mim',12,'0000-00-00','1','','','0000-00-00',1,'128,48,56,38,52,134','','12,19,1,3','','','',1,1,0,0,0,0),(32,NULL,'Rasel','','np6im56Zlp2r','Md Rasel',29,'0000-00-00','1','192.168.11.1','','0000-00-00',1,'14,61,117,48,123,10,50,134,141','','2,37,19,36,5','','','',1,1,0,0,0,0),(33,NULL,'Zahid','','np6imw==','Md Zahid Hasan',15,'0000-00-00','1','','','0000-00-00',2,'4','','2','','','',1,1,0,0,0,0),(34,NULL,'salamin','','np6imw==','salamin',19,'0000-00-00','1','','','0000-00-00',1,'4,3','','40','','','',1,1,0,0,0,0),(35,NULL,'shajjad','','4NTQ0dPEw5akow==','shajjad hossain',28,'0000-00-00','1','','','0000-00-00',1,'53,47','','2','','','',0,1,0,0,0,0),(36,NULL,'nasim','','np6im54=','Nasim',19,'0000-00-00','1','','','0000-00-00',1,'','','','','','',1,1,0,0,0,0),(37,NULL,'saeed123','','4M3UzM2UkZg=','Saeed',11,'0000-00-00','1','','','0000-00-00',1,'48,6','','5','','','',1,1,0,0,0,0),(38,NULL,'alamin shekh','','np6i','alamin shekh',10,'0000-00-00','1','','','0000-00-00',1,'','','','','','',0,1,0,0,0,0),(39,NULL,'kabir','','np6i','kabir',19,'0000-00-00','1','','','0000-00-00',1,'','','','','','',1,1,0,0,0,0),(40,NULL,'tushar','','np6i','tushar',15,'0000-00-00','1','','','0000-00-00',1,'','','','','','',1,1,0,0,0,0),(41,NULL,'arefin','','np6i','arefin',6,'0000-00-00','1','','','0000-00-00',1,'','','','','','',1,1,0,0,0,0),(42,NULL,'e','','np6imw==','eddd',19,'0000-00-00','1','192.168.11.25','','2014-05-13',2,'136,120','','12','','','',1,1,0,0,0,0),(43,NULL,'u','','np6imw==','eddd',19,'0000-00-00','1','192.168.11.252','','2014-05-13',2,'136,120','','12','','','',1,1,0,0,0,0),(44,NULL,'ashraful','','zt/XmJuW','Ashraful Islam',26,'2014-07-01','1','','','0000-00-00',3,'','','','','','',1,1,0,0,0,0),(45,NULL,'Fakhrul','','083az9vYyw==','Fakhrul islam prodan',1,'2014-10-30','1','','','0000-00-00',1,'','','','','','',1,0,0,0,0,0),(46,NULL,'fakhrul.123','','083az9vYyw==','Fakhrul islam',19,'2014-10-30','1','','','0000-00-00',1,'','','','','','',1,1,0,0,0,0),(47,NULL,'VVV','','5eTn3w==','Mr. VVV',19,'2014-12-23','1','','','0000-00-00',1,'','','','','','',1,1,0,0,0,0),(48,NULL,'2713','ma.kaiyum1992@gmail.com','n6Ogmg==','Abdul Kaiyum',10,'2016-08-09','1','','','2025-08-05',2,'138','','2','','','',1,1,0,0,0,0);
/*!40000 ALTER TABLE `user_passwd` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-02 11:25:50
