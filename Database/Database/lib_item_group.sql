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
-- Table structure for table `lib_item_group`
--

DROP TABLE IF EXISTS `lib_item_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lib_item_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(50) DEFAULT NULL,
  `trim_type` int(11) DEFAULT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `order_uom` int(11) DEFAULT NULL,
  `trim_uom` int(11) DEFAULT NULL COMMENT 'uom:  unit of measurement',
  `inserted_by` int(11) NOT NULL DEFAULT '0' COMMENT 'employee code will be add here',
  `insert_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL COMMENT 'employee code will be add here',
  `update_date` datetime DEFAULT NULL,
  `status_active` tinyint(1) DEFAULT '0' COMMENT '0:No 1:Yes',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT '0:No 1:Yes',
  `item_category` int(3) DEFAULT NULL,
  `item_group_code` varchar(25) DEFAULT NULL,
  `conversion_factor` float(10,2) DEFAULT NULL,
  `fancy_item` int(3) DEFAULT NULL,
  `cal_parameter` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `item_name` (`item_name`),
  KEY `status_active` (`status_active`),
  KEY `is_deleted` (`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lib_item_group`
--

LOCK TABLES `lib_item_group` WRITE;
/*!40000 ALTER TABLE `lib_item_group` DISABLE KEYS */;
INSERT INTO `lib_item_group` VALUES (1,'Main Lebel',1,'',24,24,2,'2012-10-07 00:00:00',2,'2012-10-17 00:00:00',0,1,4,'XX',70.00,1,1),(2,'Poly',1,'',24,24,2,'2012-10-07 00:00:00',2,'2012-10-17 00:00:00',0,1,4,'DD',60.00,1,0),(3,'Carton',2,'',1,1,2,'2012-10-17 00:00:00',2,'2012-10-24 00:00:00',0,1,4,'100',0.00,2,0),(4,'hang tag',2,'',2,2,2,'2012-10-17 00:00:00',1,'2016-11-07 11:07:52',0,1,4,'101',1.00,2,0),(5,'Wetting Agent',0,'',12,12,2,'2012-10-18 00:00:00',1,'2013-11-30 14:35:17',0,1,5,'102',1.00,2,0),(6,'Hang Tag',1,'',2,1,2,'2012-10-24 00:00:00',1,'2013-11-30 14:35:25',0,1,4,'102',12.00,2,0),(7,'rr',1,'',1,1,2,'2012-11-03 00:00:00',2,'2012-11-03 00:00:00',0,1,4,'',2.00,2,0),(8,'gg',1,'',1,1,2,'2012-11-04 00:00:00',2,'2012-11-04 00:00:00',0,1,4,'105',1.00,2,0),(9,'Interlining',1,'',1,1,2,'2012-11-04 00:00:00',2,'2012-11-20 00:00:00',0,1,4,'',1.00,2,0),(10,'Button',1,'',58,1,2,'2012-11-14 00:00:00',1,'2016-11-07 11:08:00',0,1,4,'103',3.00,2,0),(11,'Scouring Agent',0,'',12,12,2,'2012-11-14 00:00:00',1,'2013-11-30 14:36:08',0,1,5,'222',1.00,2,0),(12,'Disperse Dyes',1,'',12,12,2,'2012-11-14 00:00:00',1,'2014-04-27 10:06:17',1,0,6,'030201',1.00,2,0),(13,'Pigment',0,'',12,12,2,'2012-11-14 00:00:00',1,'2013-06-09 11:16:31',1,0,6,'',1.00,2,0),(14,'Reactive Dyes',0,'',12,12,2,'2012-11-14 00:00:00',1,'2013-06-09 11:17:43',1,0,6,'4545',1.00,2,0),(15,'Sulfur Dyes',0,'',12,12,2,'2012-11-14 00:00:00',1,'2013-06-09 11:16:40',1,0,6,'',1.00,2,0),(16,'Belt',0,'',1,1,2,'2012-11-14 00:00:00',1,'2013-06-09 11:26:02',1,0,8,'100',1.00,2,0),(17,'Machine Cam',0,'',1,1,2,'2012-11-15 00:00:00',1,'2013-06-09 11:26:11',1,0,8,'',1.00,2,0),(18,'Boiler parts',0,'',1,1,2,'2012-11-15 00:00:00',1,'2013-06-09 11:26:45',1,0,9,'',1.00,2,0),(42,'jghjgh',1,'',1,1,2,'2013-03-28 13:50:03',2,'2013-05-19 18:01:35',0,1,4,'',1.00,2,0),(43,'dfgh',1,'',1,1,2,'2013-03-28 13:55:52',2,'2013-05-19 18:02:02',0,1,4,'',1.00,2,0),(44,'',1,'',1,1,2,'2013-03-28 14:00:50',NULL,NULL,0,1,0,'',1.00,2,0),(45,'Sequestering Agent',0,'',12,12,2,'2013-03-28 15:12:41',1,'2013-06-09 11:11:33',1,0,5,'',1.00,2,0),(46,'fgjh',1,'',1,1,2,'2013-03-28 15:31:41',NULL,NULL,1,0,4,'',1.00,2,0),(47,'hjfgujh',1,'',1,1,2,'2013-03-28 15:34:38',2,'2013-05-19 18:02:48',0,1,4,'4545',1.00,2,0),(48,'ghhd',1,'',1,1,2,'2013-03-28 15:36:15',2,'2013-05-19 18:03:15',0,1,4,'454545',1.00,2,0),(49,'ghhg',1,'',1,1,2,'2013-03-28 15:36:28',NULL,NULL,1,0,10,'',1.00,2,0),(50,'454547',1,'',1,1,2,'2013-03-28 15:38:50',1,'2013-12-15 11:20:27',0,1,10,'',1.00,2,0),(51,'Jacuard ring',0,'',1,1,2,'2013-03-28 15:40:44',1,'2013-06-09 11:25:44',1,0,8,'',1.00,2,0),(52,'45454',1,'',1,1,2,'2013-03-28 17:18:30',2,'2013-05-19 18:03:35',0,1,4,'45454',1.00,2,0),(53,'xyz',1,'',1,1,2,'2013-03-30 09:23:41',2,'2013-03-30 09:24:03',1,0,4,'456',1.00,2,0),(54,'Base',1,'',1,1,2,'2013-03-30 09:45:03',2,'2013-03-30 10:02:47',1,0,4,'dfssg',1.00,2,0),(55,'Azo Dyes',0,'',12,12,2,'2013-03-30 10:04:06',1,'2013-06-09 11:17:18',1,0,6,'',1.00,2,0),(56,'Al AMin Labewl',2,'',1,1,2,'2013-03-30 10:09:22',2,'2013-05-07 17:31:51',0,1,4,'',1.00,2,0),(57,'Soaping Agent',0,'',12,12,2,'2013-03-30 10:25:44',1,'2013-06-09 11:12:25',1,0,5,'',1.00,2,0),(58,'123',2,'',1,1,2,'2013-03-30 10:27:14',NULL,NULL,1,0,4,'',1.00,2,0),(59,'4546',1,'',1,1,2,'2013-03-30 10:30:44',2,'2013-03-30 13:26:56',1,0,4,'',1.00,2,0),(60,'Hanger',2,'',2,2,2,'2013-04-08 15:23:41',NULL,NULL,1,0,4,'',1.00,2,0),(61,'Carton',2,'',1,1,2,'2013-04-08 15:24:21',1,'2014-03-18 10:17:05',1,0,4,'010203',1.00,2,2),(62,'Carton Sticker',2,'',1,1,2,'2013-04-08 15:24:47',NULL,NULL,1,0,4,'',1.00,2,3),(63,'Sewing Thread',1,'',52,52,2,'2013-04-08 15:25:14',NULL,NULL,1,0,4,'',1.00,2,1),(64,'Levelling Agent',0,'',12,12,2,'2013-04-22 17:38:30',1,'2013-06-09 11:13:11',1,0,5,'007',1.00,2,0),(66,'Pin box',2,'',54,54,2,'2013-04-23 09:26:41',1,'2014-03-19 12:53:23',1,0,11,'666',1.00,2,0),(67,'pancel',2,'',1,1,2,'2013-04-23 09:27:15',2,'2013-04-24 09:09:58',1,0,11,'777',1.00,2,0),(68,'scele',2,'',2,1,2,'2013-04-23 09:27:56',2,'2013-04-24 09:10:05',1,0,11,'888',12.00,2,0),(69,'gum tape',2,'',57,57,2,'2013-04-23 09:28:40',1,'2013-11-30 14:34:59',0,1,11,'999',1.00,2,0),(70,'Pencil',2,'',1,2,2,'2013-04-23 11:28:44',1,'2014-03-19 12:53:41',1,0,11,'788',12.00,2,0),(71,'Pen',0,'',2,1,2,'2013-04-23 11:29:24',1,'2014-08-24 12:50:51',1,0,11,'455',12.00,2,0),(72,'tep',2,'',57,1,2,'2013-04-23 11:30:05',2,'2013-04-24 09:09:40',1,0,11,'122',20.00,2,0),(73,'sceal',2,'',2,2,2,'2013-04-23 11:30:34',2,'2013-04-24 09:09:35',1,0,11,'322',1.00,2,0),(74,'softining agent',0,'',53,53,2,'2013-04-24 09:12:57',1,'2013-06-09 11:15:28',1,0,5,'123',1.00,2,0),(75,'Resisting Agent',0,'',12,12,2,'2013-04-24 09:14:15',1,'2013-06-09 11:15:36',1,0,5,'1234',1.00,2,0),(76,'Softening Agent',0,'',12,12,2,'2013-04-24 09:24:01',1,'2013-06-09 11:15:43',1,0,5,'545',1.00,2,0),(77,'Knitting CAM',0,'',1,1,2,'2013-04-28 10:55:19',1,'2013-06-09 11:27:17',1,0,9,'',1.00,2,0),(78,'Flame Retardant',0,'',12,12,2,'2013-04-28 12:04:40',1,'2013-06-09 11:15:06',1,0,5,'0006',1.00,2,0),(79,'Welsol',0,'',12,12,2,'2013-05-07 08:36:22',1,'2013-06-09 11:21:46',1,0,7,'q11',1.00,2,0),(80,'Optical Brightening Agent',0,'',12,12,2,'2013-05-07 08:36:57',1,'2013-06-09 11:20:50',1,0,7,'w11',1.00,2,0),(81,'Functional agent',0,'',12,12,2,'2013-05-07 08:37:38',1,'2013-06-09 11:21:30',1,0,7,'o11',1.00,2,0),(82,'stapler',0,'',1,1,2,'2013-05-07 09:06:21',NULL,NULL,1,0,11,'1212',1.00,2,0),(83,'roller',0,'',1,1,2,'2013-05-07 09:07:00',1,'2013-06-09 11:29:56',1,0,11,'222',1.00,2,0),(84,'Main Label',1,'',2,2,2,'2013-05-07 17:17:50',2,'2013-05-07 17:46:24',1,0,4,'',1.00,2,0),(85,'Care Label',1,'',2,2,2,'2013-05-07 17:34:59',NULL,NULL,1,0,4,'',1.00,2,0),(86,'Zipper',1,'',2,1,2,'2013-05-13 12:04:28',1,'2013-06-16 17:01:58',1,0,4,'',12.00,2,0),(87,'Draw Sting',2,'',2,2,2,'2013-05-26 10:57:04',2,'2013-05-26 11:01:34',0,1,4,'',1.00,2,0),(88,'Revet',2,'',3,2,2,'2013-05-26 11:01:13',2,'2013-05-26 11:01:27',0,1,4,'',12.00,2,0),(89,'Large cast steel spare',0,'',1,1,1,'2013-06-09 11:29:04',NULL,NULL,1,0,9,'',1.00,2,0),(90,'Tension',1,'',1,1,1,'2013-06-09 13:26:12',1,'2013-06-10 08:56:33',1,0,8,'',1.00,2,1),(91,'Bearings',0,'',1,1,1,'2013-06-09 13:51:45',1,'2013-10-22 13:43:50',1,0,8,'',1.00,2,0),(92,'Cartoon',1,'',1,1,1,'2013-06-11 12:09:34',NULL,NULL,1,0,4,'',1.00,2,2),(93,'test',0,'',1,1,1,'2013-06-20 12:02:17',NULL,NULL,1,0,4,'',1.00,2,0),(94,'Material',0,'',1,1,1,'2013-07-08 15:01:31',1,'2013-11-17 11:24:21',1,0,9,'',1.00,2,0),(95,'Pigment Chemical',0,'',12,12,1,'2013-08-27 11:55:25',NULL,NULL,1,0,5,'',1.00,2,0),(96,'Pen ink',0,'',1,1,1,'2013-09-10 15:03:18',NULL,NULL,1,0,11,'',1.00,2,0),(97,'s',1,'',1,1,1,'2013-09-29 11:54:22',NULL,NULL,1,0,4,'',1.00,2,0),(98,'Bercode Label',1,'',2,1,1,'2013-10-12 11:27:03',NULL,NULL,1,0,4,'',12.00,2,0),(99,'Pigment Dyes',0,'',12,12,1,'2013-10-22 13:36:27',NULL,NULL,1,0,6,'',1.00,2,0),(100,'Dyeing',0,'',1,1,1,'2013-10-22 13:55:52',NULL,NULL,1,0,9,'',1.00,2,0),(101,'Finishing',0,'',1,1,1,'2013-10-22 13:56:04',NULL,NULL,1,0,9,'',1.00,2,0),(102,'poly',2,'',2,2,1,'2013-11-05 12:44:41',1,'2014-03-30 12:00:12',1,0,4,'1',1.00,2,0),(103,'Elastic',1,'',27,50,1,'2013-11-14 11:02:01',1,'2014-09-17 17:21:01',1,0,4,'',100.00,2,5),(104,'ELASTRIC 2.5 cm',0,'',2,1,1,'2013-11-14 11:12:36',1,'2013-11-14 11:13:28',1,0,4,'',12.00,2,0),(105,'all',2,'',57,53,1,'2013-11-16 13:42:44',1,'2014-04-13 10:04:16',1,0,11,'',100000000.00,1,0),(106,'all accessories',1,'',12,12,1,'2013-11-16 14:24:27',1,'2014-04-13 10:04:32',1,0,4,'66',1.00,1,0),(107,'Bulb',0,'',1,1,1,'2013-11-20 08:45:35',NULL,NULL,1,0,15,'',1.00,2,0),(108,'Switch',0,'',1,1,1,'2013-11-20 08:45:57',NULL,NULL,1,0,15,'',1.00,2,0),(109,'fdg',0,'',1,1,1,'2013-11-20 10:07:52',NULL,NULL,1,0,4,'',1.00,2,0),(110,'Paper',0,'',27,27,1,'2013-11-26 08:48:43',NULL,NULL,1,0,8,'',1.00,2,0),(111,'eee',1,'',1,1,1,'2013-11-26 11:30:19',NULL,NULL,1,0,8,'789',1.00,2,0),(112,'fgfdhg',1,'',1,1,1,'2013-11-26 11:31:47',NULL,NULL,1,0,8,'aaa',1.00,2,0),(113,'hytgfh',1,'',1,1,1,'2013-11-30 14:34:44',NULL,NULL,1,0,5,'',1.00,2,1),(114,'all trims',2,'',2,2,1,'2013-12-09 12:26:30',1,'2013-12-09 12:31:18',1,0,4,'',1.00,2,0),(115,'button1',1,'',1,4,1,'2013-12-12 11:10:50',1,'2013-12-12 11:11:37',0,1,4,'',144.00,2,1),(116,'Brilliant Yellow 6G 300%',0,'',12,12,1,'2013-12-14 13:15:16',NULL,NULL,1,0,6,'',1.00,2,0),(117,'Direct Yellow 27',0,'',12,12,1,'2013-12-14 13:16:01',NULL,NULL,1,0,6,'',1.00,2,0),(118,'Direct Blue 67',0,'',12,12,1,'2013-12-14 13:16:32',NULL,NULL,1,0,6,'',1.00,2,0),(119,'Direct Black 19',0,'',12,12,1,'2013-12-14 13:17:31',NULL,NULL,1,0,6,'',1.00,2,0),(120,'Bilister poly',2,'',2,2,1,'2013-12-15 11:19:47',1,'2013-12-31 11:35:01',1,0,4,'',1.00,2,4),(121,'IT',0,'',1,1,1,'2013-12-15 13:17:03',NULL,NULL,1,0,11,'',1.00,2,0),(122,'yarn',1,'',23,52,1,'2013-12-15 14:49:36',1,'2013-12-15 14:51:10',0,1,4,'',3000.00,2,1),(123,'4555',1,'',23,52,1,'2013-12-15 15:45:30',1,'2013-12-15 15:45:57',0,1,4,'',3000.00,2,1),(124,'wood button',1,'',4,3,1,'2014-01-19 18:19:27',NULL,NULL,1,0,4,'402',12.00,2,0),(125,'Basic Chemicals',0,'',12,12,1,'2014-01-20 15:44:45',NULL,NULL,1,0,5,'',1.00,2,0),(126,'red pen',2,'',1,1,1,'2014-01-23 14:56:56',NULL,NULL,1,0,4,'abcd',1.00,2,0),(127,'REACTIVE',0,'',12,12,1,'2014-01-25 16:56:19',1,'2014-02-04 12:56:52',1,0,6,'',1.00,2,0),(128,'Basis',1,'',12,12,1,'2014-01-30 15:24:35',1,'2014-04-07 14:47:36',1,0,5,'',1.00,2,0),(129,'DISPERSE',0,'',12,12,1,'2014-02-04 12:54:30',NULL,NULL,1,0,6,'',1.00,2,0),(130,'NEON',0,'',12,12,1,'2014-02-04 12:55:58',NULL,NULL,1,0,6,'',1.00,2,0),(131,'FUCOZOL',0,'',12,12,1,'2014-02-04 12:58:29',NULL,NULL,1,0,6,'',1.00,2,0),(132,'BASIC',0,'',12,12,1,'2014-02-04 13:00:05',NULL,NULL,1,0,7,'',1.00,2,0),(133,'ETP',0,'',12,12,1,'2014-02-04 13:00:30',NULL,NULL,1,0,7,'',1.00,2,0),(134,'PRINT',0,'',12,12,1,'2014-02-04 13:00:57',NULL,NULL,1,0,7,'',1.00,2,0),(135,'Washing',0,'',12,12,1,'2014-02-24 15:12:29',NULL,NULL,1,0,5,'',1.00,2,0),(136,'soo n',1,'',1,3,1,'2014-03-11 13:54:38',1,'2014-04-27 10:08:33',1,0,5,'',2.00,2,0),(137,'ruber',0,'',1,1,1,'2014-03-22 15:24:29',NULL,NULL,1,0,22,'',1.00,2,0),(138,'New Test',1,'',1,1,1,'2014-03-24 09:04:05',NULL,NULL,1,0,4,'12',1.00,2,0),(139,'New Tes(Dzn)',1,'',2,2,1,'2014-03-24 15:04:00',NULL,NULL,1,0,4,'4',1.00,2,0),(140,'Collar',1,'',2,2,1,'2014-05-21 10:12:21',1,'2014-05-21 10:12:48',1,0,14,'r',1.00,2,1),(141,'dfds',1,'',1,1,1,'2014-05-22 15:44:43',NULL,NULL,1,0,9,'',1.00,2,1),(142,'44',1,'',1,1,1,'2014-05-22 15:45:12',NULL,NULL,1,0,9,'',1.00,2,2),(143,'T-shirt',1,'',1,1,1,'2014-05-22 15:51:03',NULL,NULL,1,0,19,'',1.00,2,2),(144,'Acitic acid',1,'',1,1,1,'2014-05-29 16:22:20',NULL,NULL,1,0,5,'',1.00,2,1),(145,'Acid',1,'',41,1,1,'2014-06-24 17:28:30',NULL,NULL,1,0,5,'',1.00,2,3),(146,'101polo',1,'',3,2,1,'2014-07-01 16:23:17',1,'2014-07-01 16:23:59',0,1,4,'100',114.00,2,1),(147,'Size Label',1,'',2,2,1,'2014-08-11 13:14:27',1,'2014-08-11 13:15:09',1,0,4,'',1.00,2,0),(148,'Phato Paper',0,'',1,1,1,'2014-08-24 12:52:32',NULL,NULL,1,0,11,'',1.00,2,0),(149,'Sequines',0,'',50,50,1,'2014-09-11 13:27:54',NULL,NULL,1,0,4,'',1.00,2,8),(150,'Plastic Eyelet Washer',2,'',57,1,1,'2014-09-17 16:06:27',1,'2014-09-17 16:08:53',1,0,4,'',5000.00,2,0),(151,'WIDTH 60',2,'',1,1,1,'2016-11-07 10:35:25',NULL,NULL,1,0,5,'',1.00,2,0),(152,'321',1,'',3,2,1,'2016-11-07 11:07:42',NULL,NULL,2,0,4,'32',333.00,1,3);
/*!40000 ALTER TABLE `lib_item_group` ENABLE KEYS */;
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