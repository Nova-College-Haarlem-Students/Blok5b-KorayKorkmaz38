-- MariaDB dump 10.19  Distrib 10.11.16-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: Blok5B
-- ------------------------------------------------------
-- Server version	10.11.16-MariaDB-ubu2204

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
-- Table structure for table `fiets_types`
--

DROP TABLE IF EXISTS `fiets_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fiets_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(255) NOT NULL COMMENT 'stadsfiets, e-bike, tandem, bakfiets',
  PRIMARY KEY (`id`),
  UNIQUE KEY `naam` (`naam`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fiets_types`
--

LOCK TABLES `fiets_types` WRITE;
/*!40000 ALTER TABLE `fiets_types` DISABLE KEYS */;
INSERT INTO `fiets_types` VALUES
(4,'bakfiets'),
(2,'e-bike'),
(1,'stadsfiets'),
(3,'tandem');
/*!40000 ALTER TABLE `fiets_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fietsen`
--

DROP TABLE IF EXISTS `fietsen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `fietsen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fiets_type_id` int(11) NOT NULL,
  `merk` varchar(255) NOT NULL,
  `framemaat` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL COMMENT 'pad naar de afbeelding',
  `dagprijs` decimal(8,2) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'NULL = in de verhuur, datum = uit de verhuur (soft delete)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fiets_type_id` (`fiets_type_id`),
  CONSTRAINT `fietsen_ibfk_1` FOREIGN KEY (`fiets_type_id`) REFERENCES `fiets_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fietsen`
--

LOCK TABLES `fietsen` WRITE;
/*!40000 ALTER TABLE `fietsen` DISABLE KEYS */;
INSERT INTO `fietsen` VALUES
(1,1,'Gazelle','53','gazelle-stad.jpg',12.50,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(2,1,'Batavus','57','batavus-stad.jpg',12.50,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(3,1,'Sparta','49','sparta-stad.jpg',11.00,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(4,2,'Stromer','54','stromer.jpg',29.95,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(5,2,'Gazelle','57','gazelle-ebike.jpg',27.50,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(6,2,'QWIC','50','qwic.jpg',25.00,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(7,3,'Santos','L','santos-tandem.jpg',35.00,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(8,3,'Cortina','XL','cortina-tandem.jpg',32.50,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(9,4,'Babboe','M','babboe.jpg',39.95,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(10,4,'Urban Arrow','M','urban-arrow.jpg',45.00,'2026-10-20 08:00:00','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(11,1,'Cortina','61','cortina.jpg',13.00,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(12,2,'Batavus','53','batavus-ebike.jpg',27.50,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02');
/*!40000 ALTER TABLE `fietsen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gebruikers`
--

DROP TABLE IF EXISTS `gebruikers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gebruikers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_id` int(11) NOT NULL,
  `naam` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `wachtwoord` varchar(255) NOT NULL,
  `telefoon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `rol_id` (`rol_id`),
  CONSTRAINT `gebruikers_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `rollen` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gebruikers`
--

LOCK TABLES `gebruikers` WRITE;
/*!40000 ALTER TABLE `gebruikers` DISABLE KEYS */;
INSERT INTO `gebruikers` VALUES
(1,3,'Joost de Vries','admin@trapkracht.nl','$2y$12$binlEH1G0e/tmEae2hmsqeFGM5extiGNLd/kv0Vo.gpRKtvDxJ0te','0612345678','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(2,2,'Sanne Bakker','sanne@trapkracht.nl','$2y$12$binlEH1G0e/tmEae2hmsqeFGM5extiGNLd/kv0Vo.gpRKtvDxJ0te','0623456789','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(3,2,'Mehmet Yilmaz','mehmet@trapkracht.nl','$2y$12$binlEH1G0e/tmEae2hmsqeFGM5extiGNLd/kv0Vo.gpRKtvDxJ0te',NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(4,1,'Lisa Jansen','lisa@example.com','$2y$12$binlEH1G0e/tmEae2hmsqeFGM5extiGNLd/kv0Vo.gpRKtvDxJ0te','0634567890','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(5,1,'Tom de Boer','tom@example.com','$2y$12$binlEH1G0e/tmEae2hmsqeFGM5extiGNLd/kv0Vo.gpRKtvDxJ0te',NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(6,1,'Fatima El Amrani','fatima@example.com','$2y$12$binlEH1G0e/tmEae2hmsqeFGM5extiGNLd/kv0Vo.gpRKtvDxJ0te','0645678901','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(7,1,'Daan Visser','daan@example.com','$2y$12$binlEH1G0e/tmEae2hmsqeFGM5extiGNLd/kv0Vo.gpRKtvDxJ0te','0656789012','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(8,1,'Emma Smit','emma@example.com','$2y$12$binlEH1G0e/tmEae2hmsqeFGM5extiGNLd/kv0Vo.gpRKtvDxJ0te',NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(9,1,'oudheusden','nova@live.com','$2y$12$IXSr6FIk7hWSYuNLktSBmOJsSDH.i5ntiYb8yEAFsVxaklB1sP30W','0612345','2026-10-29 10:03:30','2026-10-29 10:03:30');
/*!40000 ALTER TABLE `gebruikers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logboek`
--

DROP TABLE IF EXISTS `logboek`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `logboek` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gebruiker_id` int(11) NOT NULL COMMENT 'wie de actie deed',
  `actie` varchar(255) NOT NULL COMMENT 'wat er is gedaan',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gebruiker_id` (`gebruiker_id`),
  CONSTRAINT `logboek_ibfk_1` FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruikers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logboek`
--

LOCK TABLES `logboek` WRITE;
/*!40000 ALTER TABLE `logboek` DISABLE KEYS */;
INSERT INTO `logboek` VALUES
(1,2,'Fiets 12 (Batavus) toegevoegd','2026-08-01 07:15:00'),
(2,3,'Dagprijs van fiets 4 (Stromer) aangepast naar € 29,95','2026-09-15 12:02:00'),
(3,2,'Reservering 9 bijgewerkt (inleverdatum: 2026-08-24)','2026-08-24 15:40:00'),
(4,3,'Fiets 10 (Urban Arrow) uit de verhuur gehaald','2026-10-20 08:00:00');
/*!40000 ALTER TABLE `logboek` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reserveringen`
--

DROP TABLE IF EXISTS `reserveringen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reserveringen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gebruiker_id` int(11) NOT NULL COMMENT 'de klant',
  `fiets_id` int(11) NOT NULL,
  `ophaaldatum` date NOT NULL,
  `terugbrengdatum` date NOT NULL COMMENT 'geplande datum',
  `totaalprijs` decimal(8,2) NOT NULL COMMENT 'prijs op het moment van reserveren',
  `inleverdatum` date DEFAULT NULL COMMENT 'leeg zolang de fiets onderweg is',
  `opmerking` text DEFAULT NULL COMMENT 'bijv. lekke band',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gebruiker_id` (`gebruiker_id`),
  KEY `fiets_id` (`fiets_id`),
  CONSTRAINT `reserveringen_ibfk_1` FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruikers` (`id`),
  CONSTRAINT `reserveringen_ibfk_2` FOREIGN KEY (`fiets_id`) REFERENCES `fietsen` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reserveringen`
--

LOCK TABLES `reserveringen` WRITE;
/*!40000 ALTER TABLE `reserveringen` DISABLE KEYS */;
INSERT INTO `reserveringen` VALUES
(1,4,1,'2026-05-02','2026-05-04',25.00,'2026-05-04',NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(2,5,2,'2026-05-10','2026-05-11',12.50,'2026-05-11','lekke band','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(3,6,3,'2026-06-01','2026-06-05',44.00,'2026-06-05',NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(4,7,1,'2026-06-14','2026-06-15',12.50,'2026-06-15',NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(5,8,2,'2026-07-03','2026-07-07',50.00,'2026-07-08','een dag te laat ingeleverd','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(6,4,3,'2026-07-20','2026-07-22',22.00,'2026-07-22',NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(7,5,4,'2026-08-01','2026-08-03',59.90,'2026-08-03',NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(8,6,5,'2026-08-15','2026-08-16',27.50,'2026-08-16',NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(9,7,7,'2026-08-22','2026-08-24',70.00,'2026-08-24',NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(10,8,9,'2026-09-05','2026-09-06',39.95,'2026-09-06','regenhoes kapot','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(11,4,10,'2026-09-20','2026-09-21',45.00,'2026-09-21','ketting gebroken','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(12,5,1,'2026-10-25','2026-10-29',50.00,NULL,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(13,6,6,'2026-10-26','2026-10-28',50.00,NULL,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(14,7,4,'2026-11-07','2026-11-09',59.90,NULL,NULL,'2026-10-27 10:21:02','2026-10-27 10:21:02'),
(15,4,12,'2026-10-30','2026-11-01',82.50,NULL,NULL,'2026-10-29 10:14:37','2026-10-29 10:14:37'),
(16,4,9,'2026-10-31','2026-11-03',159.80,NULL,NULL,'2026-10-29 10:16:36','2026-10-29 10:16:36');
/*!40000 ALTER TABLE `reserveringen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rollen`
--

DROP TABLE IF EXISTS `rollen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rollen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(255) NOT NULL COMMENT 'klant, medewerker, admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `naam` (`naam`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rollen`
--

LOCK TABLES `rollen` WRITE;
/*!40000 ALTER TABLE `rollen` DISABLE KEYS */;
INSERT INTO `rollen` VALUES
(1,'klant','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(2,'medewerker','2026-10-27 10:21:02','2026-10-27 10:21:02'),
(3,'admin','2026-10-27 10:21:02','2026-10-27 10:21:02');
/*!40000 ALTER TABLE `rollen` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-29 11:25:13
