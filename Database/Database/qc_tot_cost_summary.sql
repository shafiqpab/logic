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
-- Table structure for table `qc_tot_cost_summary`
--

DROP TABLE IF EXISTS `qc_tot_cost_summary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qc_tot_cost_summary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mst_id` int(11) NOT NULL DEFAULT '0',
  `buyer_agent_id` int(11) NOT NULL DEFAULT '0',
  `location_id` int(11) NOT NULL DEFAULT '0',
  `no_of_pack` double NOT NULL,
  `is_confirm` tinyint(2) NOT NULL DEFAULT '2',
  `is_cm_calculative` tinyint(2) NOT NULL DEFAULT '2',
  `mis_lumsum_cost` double NOT NULL,
  `commision_per` double NOT NULL,
  `tot_fab_cost` double NOT NULL,
  `tot_sp_operation_cost` double NOT NULL,
  `tot_accessories_cost` double NOT NULL,
  `tot_cm_cost` double NOT NULL,
  `tot_fright_cost` double NOT NULL,
  `tot_lab_test_cost` double NOT NULL,
  `tot_miscellaneous_cost` double NOT NULL,
  `tot_other_cost` double NOT NULL,
  `tot_commission_cost` double NOT NULL,
  `tot_cost` double NOT NULL,
  `tot_fob_cost` double NOT NULL,
  `tot_rmg_ratio` double NOT NULL DEFAULT '0',
  `inserted_by` int(11) NOT NULL DEFAULT '0',
  `insert_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '0',
  `update_date` datetime NOT NULL,
  `status_active` tinyint(2) NOT NULL DEFAULT '1',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qc_tot_cost_summary`
--

LOCK TABLES `qc_tot_cost_summary` WRITE;
/*!40000 ALTER TABLE `qc_tot_cost_summary` DISABLE KEYS */;
/*!40000 ALTER TABLE `qc_tot_cost_summary` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-02 11:59:08