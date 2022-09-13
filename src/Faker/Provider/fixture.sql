-- mysqldump-php https://github.com/ifsnop/mysqldump-php
--
-- Host: 127.0.0.1:3306	Database: dbsaver_test_fixture
-- ------------------------------------------------------
-- Server version 	8.0.30-0ubuntu0.22.04.1
-- Date: Tue, 13 Sep 2022 08:26:14 +0200

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
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `messenger_messages` with 0 row(s)
--

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5A8A6C8DA76ED395` (`user_id`),
  CONSTRAINT `FK_5A8A6C8DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post`
--

LOCK TABLES `post` WRITE;
/*!40000 ALTER TABLE `post` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `post` VALUES (1,1,'Esse et quia enim.','Veniam cupiditate voluptas in harum maxime optio. Ullam dolor et ipsa fugiat rerum et. Provident neque aut labore aut ducimus non sapiente.','2015-09-29 16:46:56'),(2,14,'Odio est amet quia minima ut.','Quae voluptas est porro illum. Dolorem ut sunt voluptatum numquam unde voluptas explicabo. Dolore et quidem ipsum eos ipsam. Facilis necessitatibus aut officiis minima quae exercitationem.','2021-08-27 02:14:01'),(3,12,'Sed iste mollitia est.','Aut occaecati voluptate possimus suscipit nobis. Rem enim numquam molestias ipsa et dolorum. Ut iure nihil quia dolor.','2021-11-21 09:02:22'),(4,8,'Nam unde quo est aliquam.','Reprehenderit hic eligendi et quas est voluptas ipsa. Eos eveniet non laboriosam aut. Saepe sequi est similique modi cum.','2013-07-23 06:20:56'),(5,14,'Et sit recusandae est ut.','Pariatur possimus quia velit ex sit. Unde voluptatem nulla accusamus non. Enim et dolorem sit error voluptas. Nesciunt iure omnis autem voluptatum molestiae.','2013-11-23 13:21:30');
/*!40000 ALTER TABLE `post` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `post` with 5 row(s)
--

--
-- Table structure for table `post_tag`
--

DROP TABLE IF EXISTS `post_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_tag` (
  `post_id` int NOT NULL,
  `tag_id` int NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`),
  KEY `IDX_5ACE3AF04B89032C` (`post_id`),
  KEY `IDX_5ACE3AF0BAD26311` (`tag_id`),
  CONSTRAINT `FK_5ACE3AF04B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5ACE3AF0BAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_tag`
--

LOCK TABLES `post_tag` WRITE;
/*!40000 ALTER TABLE `post_tag` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `post_tag` VALUES (1,7),(2,3),(2,9),(2,11),(2,16),(3,6),(4,11),(4,12),(4,13),(4,14),(4,17),(5,2);
/*!40000 ALTER TABLE `post_tag` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `post_tag` with 12 row(s)
--

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `tag` VALUES (2,'est'),(3,'vero'),(6,'natus'),(7,'dolor'),(9,'quas'),(11,'voluptatum'),(12,'quod'),(13,'est'),(14,'velit'),(16,'deleniti'),(17,'nostrum');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `tag` with 11 row(s)
--

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `user` VALUES (1,'renee.duhamel@noos.fr','[]','$2y$13$wD/tsbF52MwxXFzhrvXcae0k3AZogxaMe14uW1NsPCpcr5RvDAcV2'),(8,'franck.pinto@wanadoo.fr','[]','$2y$13$VnaoQpTf9zQ2EqCDcOqzJu9nZ7FMiO/HnY3QY0FXeX/w.cf4IshHi'),(12,'elise.dias@yahoo.fr','[]','$2y$13$OOoqlzaShroRkxa.KPKcaOO0cOmF4ShzPhf8Y.blOmcS1oVV0nAR6'),(14,'aroussel@wanadoo.fr','[]','$2y$13$IalGcpMcEXMLldn7j2F4.O7Yo7DCVxDwniBgp09dK8xVvEZgpwMV6');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `user` with 4 row(s)
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on: Tue, 13 Sep 2022 08:26:14 +0200
