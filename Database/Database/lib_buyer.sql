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
-- Table structure for table `lib_buyer`
--

DROP TABLE IF EXISTS `lib_buyer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lib_buyer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buyer_name` varchar(100) DEFAULT NULL,
  `short_name` varchar(30) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `exporters_reference` varchar(200) DEFAULT NULL,
  `party_type` varchar(50) NOT NULL,
  `designation` varchar(50) NOT NULL,
  `tag_company` varchar(100) NOT NULL,
  `country_id` int(11) NOT NULL,
  `web_site` varchar(30) DEFAULT NULL,
  `buyer_email` varchar(100) NOT NULL,
  `address_1` varchar(500) DEFAULT NULL,
  `address_2` varchar(500) NOT NULL,
  `address_3` varchar(500) NOT NULL,
  `address_4` varchar(500) NOT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `supllier` int(11) NOT NULL,
  `marketing_team_id` int(11) NOT NULL,
  `control_delivery` tinyint(2) NOT NULL DEFAULT '0',
  `credit_limit_days` int(11) NOT NULL DEFAULT '0',
  `credit_limit_amount` int(11) NOT NULL DEFAULT '0',
  `credit_limit_amount_currency` int(11) NOT NULL DEFAULT '0',
  `discount_method` varchar(50) NOT NULL,
  `securitye_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `vat_to_be_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `ait_to_be_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `sewing_effi_mkt_percent` float NOT NULL DEFAULT '100',
  `sewing_effi_plaing_per` double NOT NULL DEFAULT '100',
  `inserted_by` int(11) DEFAULT '0',
  `insert_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `status_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=In-Active; 1=Active;',
  `is_deleted` int(1) NOT NULL DEFAULT '0',
  `cut_off_used` int(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `buyer_name` (`buyer_name`),
  KEY `country_id` (`country_id`),
  KEY `supllier` (`supllier`),
  KEY `credit_limit_days` (`credit_limit_days`),
  KEY `status_active` (`status_active`),
  KEY `is_deleted` (`is_deleted`),
  KEY `remark` (`remark`(255))
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lib_buyer`
--

LOCK TABLES `lib_buyer` WRITE;
/*!40000 ALTER TABLE `lib_buyer` DISABLE KEYS */;
INSERT INTO `lib_buyer` VALUES (1,'EKO TEX','EKO','','','2,1','','1,16,14,35,43',21,'','','','','','','',0,36,0,0,0,1,'1',1,1,1,0,0,2,'2012-10-18 00:00:00',1,'2014-09-07 09:23:21',1,0,0),(3,'Wall Mart','WM','Mr. Wall Mart','0436987456321','20,90,7','CEO','2,15,16',107,'www.wallmart.com','Wallmart@gmail.com','ROME,NEW ARDOA HOUSE,ITALY','ROME,NEW ARDOA HOUSE,ITALY','ROME,NEW ADORA HOUSE,ITALY','ROME,NEW ARDOA HOUSE,ITALY','THIS IS A SENCITIVE BUYER',25,24,0,45,6000000,2,'0',1,1,1,100,100,2,'2012-10-18 00:00:00',1,'2014-07-12 15:09:54',1,0,0),(4,'NKD','NKD','q','','1,3,6,21,23,2','q','2,8,11,16,33,35,36,39,40',0,'','','','','','','',0,23,0,0,0,1,'0',1,1,1,100,100,2,'2012-10-18 00:00:00',1,'2014-07-12 15:10:02',1,0,0),(6,'Clemens n August','C N A','','','20,23,1,5,6','www','2,15,5,32,35,24,36,43',0,'','','','','','','',0,15,0,0,0,1,'0',1,1,1,100,100,2,'2012-11-04 00:00:00',1,'2014-08-20 15:51:32',1,0,0),(7,'TEXIBO','TEXIB','','','20','GM','2',0,'','','','','','','',0,0,0,0,0,1,'0',1,1,1,100,100,2,'2012-11-13 00:00:00',2,'2012-11-17 00:00:00',0,1,0),(8,'Mc Tek','MCT','','','4,6','GM','2',0,'','','','','','','',0,23,0,0,0,1,'0',1,1,1,100,100,2,'2012-11-14 00:00:00',1,'2014-07-13 09:16:10',1,0,0),(10,'KIABI','KB','','','1,2,3,4,5,6,20,21,22,30,90,23','DD','2,11,19',0,'','','','','','','',9,34,0,0,0,1,'0',1,1,1,100,100,2,'2012-12-06 00:00:00',1,'2014-07-13 09:16:21',1,0,0),(11,'SIPLEC','SIP','SAEED','','1,3,20,21,90,22,23,6','Saeed','5,35',21,'','','','','','','',1,31,0,0,0,1,'2',1,1,1,100,100,2,'2012-12-18 00:00:00',1,'2014-07-13 09:16:41',1,0,0),(12,'Brice','Brice','Mr. Rifat','01911390100','1,20,7,22,6','Executive','1,2,3',21,'','rifat@logicsoftbd.com','','','','','',7,31,0,0,0,1,'2',1,1,1,100,100,2,'2013-01-02 00:00:00',1,'2014-07-13 09:18:32',1,0,0),(14,'American Egle','AE','','','1,20,21,3,2,6,22','American','6,11,19,33',21,'','','','','','','',0,15,0,0,0,1,'2',1,1,1,100,100,2,'2013-02-12 00:00:00',1,'2014-07-13 09:16:50',1,0,0),(19,'MGB HK Ltd.','MGB','A','A','1,3,6,20,21,2,5','A','5,16,23,33',21,'A','A','Dhaka','A','A','A','',0,30,0,0,0,1,'2',1,1,1,100,100,2,'2013-02-28 13:13:18',1,'2014-07-13 09:17:14',1,0,0),(22,'G.GULDENPFENNIQ GMBH','G.GUL','Md.Munir','','1','MD','7',84,'','','','','','','',0,25,0,0,0,2,'2',1,1,1,100,100,2,'2013-02-28 17:28:04',1,'2014-07-13 09:19:16',1,0,0),(24,'X-MAN','X','MAN','01750507454','1,2,3,4,22,5','IT','2,7,3,4,34',4,'','shabbirhossain90@gmail.com','Dhaka','Dhaka','D','sd','sdsdsdsds',0,30,0,123333330,122222220,2,'2',1,1,2,100,100,2,'2013-02-28 17:37:22',1,'2014-07-13 09:26:57',1,0,0),(27,'SAS KIABI','KIABI','SASW','017453645','2,1,3,4,5,6,20,21','IT','2',21,'WWW','@','DHAKA','DHAKA','DHAKA','DHAKA','',15,34,0,10000000,0,2,'2',1,1,1,100,100,2,'2013-03-02 11:00:54',1,'2014-07-13 09:27:54',1,0,0),(28,'SUMO','SM','SSS','1','1,2,3,4,21','s','7,2,3,35',5,'5','2','4','3','6','7','gfghjm,kl;',0,33,0,0,0,1,'0',1,1,1,100,100,2,'2013-03-02 12:48:34',1,'2014-07-13 09:28:16',2,0,0),(29,'GMAN','GN','','','1,3','CEO','2,7,6,24',0,'','','','','','','',0,33,0,0,0,1,'0',1,1,1,100,100,2,'2013-03-24 09:54:41',1,'2014-07-13 09:29:31',1,0,0),(31,'KARATOA','KT','DOYEL','','1,2,3,4,5,6,20,21,22,23,30,90,80','BUYER','2,7',0,'','','Mirpur,Dhaka','','','','',7,33,0,0,0,1,'0',1,1,1,100,100,2,'2013-04-23 12:07:11',1,'2014-07-13 09:30:12',1,0,0),(33,'GAP','GAP','MR S','01680636095','1,20,4,5,6','MD','2,15,32,39,12',0,'','','','','','','',0,33,0,0,0,1,'0',1,1,1,100,100,2,'2013-05-05 09:01:29',1,'2014-07-13 09:30:28',1,0,0),(34,'Calvin Klein','CK','Mr. Mark','00981230986','1,4,5,22','Manerag','5,32',84,'','mrmark@gmil.com','','','','','nasirs',0,33,0,0,0,2,'2',1,1,1,100,100,2,'2013-05-05 09:20:10',1,'2014-07-13 09:30:57',1,0,0),(35,'Logicsoftbd','LST','','','1,2,3','Software','2,7',0,'','','','','','','',7,32,0,0,0,1,'2',1,1,1,100,100,2,'2013-05-12 12:41:23',1,'2014-07-13 09:31:09',1,0,0),(37,'Levies','L','Safin','','1,3,4,5,6,20,21,22,23,30,90','MD','2,15,32,12',0,'','','','','','','S',15,15,0,50,5000000,1,'1',1,1,1,100,100,2,'2013-05-15 11:45:20',1,'2014-07-13 09:31:37',1,0,0),(38,'NIKE','N','Nafis','','1,20,23,22,3,4,5,6,21,30,90','Buyer','2,15,39,32',1,'','','','','','','S U',15,23,0,50,5000000,1,'1',1,1,1,100,100,2,'2013-05-15 15:11:57',1,'2014-07-13 09:31:49',1,0,0),(39,'Hugo Boss','H B','MD','','1,23,22','MD','21,17',0,'','','','','','','',0,20,0,0,0,2,'0',1,1,1,100,100,2,'2013-05-26 09:57:06',1,'2014-07-13 09:32:05',1,0,0),(40,'Emperio Armani','EA','MD','','1','MD','2,7',0,'','','','','','','',0,25,0,0,0,1,'0',1,1,1,100,100,2,'2013-05-26 09:58:31',1,'2014-07-13 09:32:20',1,0,0),(41,'Black Exclusive','Black','','','1,3','Buyer','1,2',0,'','','','','','','',0,0,0,0,0,1,'0',1,1,1,100,100,1,'2013-05-29 13:58:23',1,'2013-06-06 14:21:58',0,1,0),(43,'Tichbo','Tichb','Mr.Sen','','1,21,20,22,6,23','MD','13,12,1',0,'','','','','','','',25,26,0,0,0,1,'0',1,1,1,100,100,1,'2013-06-05 11:23:45',1,'2014-07-13 09:33:45',1,0,0),(44,'MQ','Mq','','','1,21,20,22,6,23','','13,12,1',207,'','','','','','','',0,12,0,0,0,2,'2',1,1,1,55,100,1,'2013-06-18 16:35:56',1,'2014-07-13 09:34:10',1,0,0),(45,'KMART','KMT','','','1','','1',0,'','','','','','','',0,13,0,0,0,1,'0',1,1,1,100,100,1,'2013-06-22 09:37:26',1,'2014-07-13 09:34:27',1,0,0),(47,'Tom Tailor','TT','','','1,20,5,6,4','','1,15',152,'','','','','','','',0,28,0,0,0,1,'2',1,1,1,100,100,1,'2013-07-03 11:42:59',1,'2014-07-13 09:35:31',1,0,0),(48,'HnM','HnM','md','','21,1,5,22,2,3,80','md','11,1,32,5',195,'','','','','','','',37,16,0,0,0,2,'2',1,1,1,100,100,1,'2013-07-10 10:04:20',1,'2014-07-13 09:35:50',1,0,0),(49,'Esprit','ep','md','','6,21','md','11,15,32',0,'','','','','','','',0,17,0,0,0,2,'0',1,1,1,100,100,1,'2013-07-13 14:56:41',1,'2014-07-13 09:36:17',1,0,0),(51,'Zara Fashion','ZARA','Mr. M.F.Hosain','01710-293935','2,1,3,20,21,30,90','CEO','11,2,14,12',21,'zarafashion.com','fidel@zara.com','House~345,Road#25 Baridhara DOHS, Dhaka-1000.','','','','',25,1,0,0,0,1,'2',1,1,1,100,100,1,'2013-07-17 08:37:23',1,'2014-07-13 09:37:13',1,0,0),(52,'s.Oliver','SO','Md. Masum Ahmad','01710293935','1,3,90,4,5,6,2','CEO','15,32',21,'soliver.com','masum.aust20@gmail.com','House~2, Road#3,Block#A Rankin Street ,Frankfurt. Germany','','','','',7,21,0,0,0,1,'2',1,1,1,100,100,1,'2013-07-18 15:04:17',1,'2014-07-13 09:37:29',1,0,0),(55,'PUMA','PUMA','Nasim','01722231680','1,6,21,22,23','Imple.','18,2,32',34,'WWW.PUMA.COM','','Africa','Africa','Africa','Africa','',45,3,0,15,75,2,'2',1,1,1,100,100,1,'2013-09-08 11:07:12',1,'2014-07-13 09:40:14',1,0,0),(56,'NEXT','Next','','','1','','2,39',0,'','','','','','','',0,18,0,0,0,1,'0',1,1,1,100,100,1,'2013-10-05 08:52:09',1,'2014-07-13 09:40:35',1,0,0),(57,'Hwa Well','HWell','FM','017115','2,21,20,3','MKT','19,6',21,'www.HwaWelltex.com','Jahangiralom@gmail.com','Mymenshing','Mymenshing','Valoka','Mymenshing','Test Report',15,14,0,30,10000,1,'2',1,1,1,100,100,1,'2013-10-12 11:08:58',1,'2014-07-13 09:40:51',1,0,0),(116,'New look','NL','MR.Fakhrul islam','54554','1,2,3,4,5,6,21,22,23,30','Director','12,13,2,7,23,8,3,17,21,11,15,22,19,1',1,'new look.com','director new look@gmail.com','new york in south America','new york in south America','new york in south America','new york in south America','',56,20,0,10000,2600000,2,'2',1,1,1,100,100,1,'2013-12-01 14:48:15',1,'2014-07-13 09:48:54',1,0,0),(119,'T.M tailor','df','df','34324324324','3,5,4,6','dfdsf','7,23,8',5,'dsfds','df','dsf','dsf','dsf','dsfds','dfdfdsfdfdf',57,36,0,3434,3434,1,'3',1,1,1,100,100,1,'2013-12-05 16:19:22',1,'2014-07-13 09:55:49',1,0,0),(125,'DK','DK','S','01422','1','SD','12,13,27,2,7,23',4,'www.something.com','@Gmail.com','Something','dsf','sdfdf','sdf','asdfdfghffefg',25,4,0,30,50000000,1,'2',1,1,1,100,100,1,'2013-12-15 10:59:13',1,'2014-07-13 09:58:19',1,0,0),(128,'Fal-Mart','Fal-Mart','Mr.Anderson','7668992490','1,5,2,23','Marketing Manager','25,8,11,21,13,12,20,30,28,26',70,'fal.mart.uk','andu.fal-mart','H-52-S-38 SOUTH ZONE','','','','',0,6,0,0,0,1,'2',1,1,1,100,100,1,'2013-12-17 17:12:52',1,'2014-07-13 09:58:49',1,0,0),(129,'Addidas','Addidas','Joy','215745356','1,7','Chairman','2,12,8',21,'wwwwww','joy@gmail.com','Dhaka','','','','',0,28,0,0,0,1,'0',1,1,1,100,100,1,'2013-12-26 09:41:46',1,'2014-07-13 09:59:15',1,0,0),(130,'Sun City','SC','','','1,6,21,22','','33',0,'','','','','','','',0,28,0,0,0,1,'0',1,1,1,100,100,1,'2013-12-30 17:28:45',1,'2014-07-13 09:59:33',1,0,0),(131,'Giant Tiger','GT','','','1,3,6,21,22','','33',0,'','','','','','','',0,28,0,0,0,1,'0',1,1,1,100,100,1,'2013-12-30 17:30:23',1,'2014-07-13 09:59:46',1,0,0),(133,'Local Boyz Ltd.','LCB','','','21,6,22,23','','33,1',0,'','','','','','','',0,28,0,0,0,1,'0',1,1,1,100,100,1,'2013-12-30 17:34:43',1,'2014-07-13 10:00:42',1,0,0),(134,'ZARA','ZARA','Mr.Fakhrul islam','016145798','1,3,20,21,22,23,5,30','Senior advisor','12,2,34,39',16,'zara.com','fakhrul@zara.com','new cap town in Australia','new cap town in Australia','new cap town in Australia','new cap town in Australia','',0,6,0,60000,1800000,2,'2',1,1,1,100,100,1,'2014-01-02 10:23:58',1,'2014-07-13 10:01:20',1,0,0),(144,'LPP','LPP','','','30','','2',0,'','','','','','','',0,31,0,0,0,1,'0',1,1,1,0,0,1,'2014-03-31 11:03:15',1,'2014-07-13 10:05:17',1,0,0),(147,'Zara ladis','ZL','Mr. Lalita kiuamr atapattu','','1','MD','2,12',0,'Zaraladis.com','','','','','','',0,25,0,0,0,1,'0',1,1,1,0,0,1,'2014-05-14 08:58:41',1,'2014-07-13 10:06:15',1,0,0),(152,'red blue','red','poiu1230','tgt102','1,2,3,90,80,23,21,22,6,4,5','merchandiser','12,35,2,7,23,37,8,39',155,'rfrfr-02','rdfrf102','fffrf','02012','rfr3-01','rfrpo---000','100',25,0,0,200,240001,1,'2',1,1,1,60,55,1,'2014-07-01 16:15:34',1,'2014-07-01 16:17:45',0,1,0),(153,'Bizzbee','BZ','Nasim','1','1,4,5,6,22','Associate','43',21,'www','nasim@logicsoftbd.com','Dhaka','Dhaka','Dhaka1','Dhaka','',0,14,0,0,0,2,'0',1,1,1,0,0,1,'2014-08-11 13:04:17',1,'2014-08-11 13:05:56',1,0,0),(154,'Divine Tex','DT','','','2','','43',0,'','','','','','','',0,0,0,0,0,1,'0',1,1,1,0,0,1,'2014-08-18 14:56:38',NULL,NULL,1,0,0),(158,'H n M','H n M','','','1','','41',0,'','','','','','','',0,0,0,0,0,1,'0',1,1,1,0,0,1,'2014-10-27 10:43:26',NULL,NULL,1,0,0);
/*!40000 ALTER TABLE `lib_buyer` ENABLE KEYS */;
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
