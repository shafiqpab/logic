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
-- Table structure for table `qc_cons_rate_dtls`
--

DROP TABLE IF EXISTS `qc_cons_rate_dtls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qc_cons_rate_dtls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mst_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `particular_type_id` int(11) NOT NULL DEFAULT '0',
  `formula` varchar(800) NOT NULL,
  `consumption` double NOT NULL,
  `unit` int(11) NOT NULL DEFAULT '0',
  `is_calculation` tinyint(2) NOT NULL DEFAULT '2',
  `rate` double NOT NULL,
  `rate_data` varchar(800) NOT NULL,
  `value` double NOT NULL DEFAULT '0',
  `inserted_by` int(11) NOT NULL DEFAULT '0',
  `insert_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `update_date` datetime NOT NULL,
  `status_active` tinyint(2) NOT NULL DEFAULT '1',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qc_cons_rate_dtls`
--

LOCK TABLES `qc_cons_rate_dtls` WRITE;
/*!40000 ALTER TABLE `qc_cons_rate_dtls` DISABLE KEYS */;
INSERT INTO `qc_cons_rate_dtls` VALUES (1,0,0,0,0,'',0,0,2,0,'~~',0,0,'0000-00-00 00:00:00',0,'0000-00-00 00:00:00',1,0);
/*!40000 ALTER TABLE `qc_cons_rate_dtls` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-02 11:59:07
