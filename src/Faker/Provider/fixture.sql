-- mysqldump-php https://github.com/ifsnop/mysqldump-php
--
-- Host: 127.0.0.1:3306	Database: livecomponent
-- ------------------------------------------------------
-- Server version 	8.0.30-0ubuntu0.22.04.1
-- Date: Thu, 01 Sep 2022 16:25:47 +0200

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
INSERT INTO `post` VALUES (1,1,'Esse et quia enim.','Veniam cupiditate voluptas in harum maxime optio. Ullam dolor et ipsa fugiat rerum et. Provident neque aut labore aut ducimus non sapiente.','2015-09-29 16:46:56'),(2,14,'Odio est amet quia minima ut.','Quae voluptas est porro illum. Dolorem ut sunt voluptatum numquam unde voluptas explicabo. Dolore et quidem ipsum eos ipsam. Facilis necessitatibus aut officiis minima quae exercitationem.','2021-08-27 02:14:01'),(3,12,'Sed iste mollitia est.','Aut occaecati voluptate possimus suscipit nobis. Rem enim numquam molestias ipsa et dolorum. Ut iure nihil quia dolor.','2021-11-21 09:02:22'),(4,8,'Nam unde quo est aliquam.','Reprehenderit hic eligendi et quas est voluptas ipsa. Eos eveniet non laboriosam aut. Saepe sequi est similique modi cum.','2013-07-23 06:20:56'),(5,14,'Et sit recusandae est ut.','Pariatur possimus quia velit ex sit. Unde voluptatem nulla accusamus non. Enim et dolorem sit error voluptas. Nesciunt iure omnis autem voluptatum molestiae.','2013-11-23 13:21:30'),(6,4,'Rerum facilis nihil aut.','Non vel quis exercitationem. Quas eos laudantium corporis sit maxime et amet. Et et incidunt velit.','2020-04-12 01:07:09'),(7,4,'Nisi atque autem at.','Et consequatur et dicta dolore accusamus. Similique dolorem impedit culpa ipsa voluptatem est. Ipsum beatae laudantium debitis in vero unde. Natus est voluptas non neque distinctio culpa et.','2016-08-31 22:11:04'),(8,8,'Consectetur minus nemo earum.','Qui et illo qui. Ut aperiam reprehenderit id nisi accusantium iure voluptatem aut. Atque enim unde earum animi sed. At sequi voluptatem amet alias consequatur.','2013-03-21 03:09:55'),(9,5,'Voluptatem nam molestiae est.','Beatae corporis officiis non eos consequuntur vero est minus. Esse soluta quam itaque fuga.','2014-04-05 12:31:19'),(10,15,'Assumenda culpa et autem ut.','Nisi eveniet dicta dolore doloremque voluptatibus aut cum saepe. Et est vitae ex modi ut dolores veritatis. Consequuntur molestiae id aut ratione magnam. Autem cum quia tenetur corrupti iure.','2019-06-29 17:10:20'),(11,5,'Veritatis iure est quis et.','Aliquam et eligendi nesciunt. Quasi consequatur autem ad voluptates atque hic ipsum aut.','2015-06-02 09:42:17'),(12,14,'Et voluptas tempore est non.','Porro veniam commodi sapiente repudiandae excepturi nisi natus. Dolores delectus est in hic quis. Ipsam quia ex voluptatem et vel et natus. Impedit sit eos et veritatis exercitationem sed magni.','2014-04-04 08:19:39'),(13,14,'Veritatis quo ut iste.','Labore veniam nostrum sed dolore. Qui sit occaecati suscipit aut. Perferendis delectus occaecati reprehenderit.','2019-03-01 11:52:18'),(14,5,'Ullam est explicabo in.','Ut quia veniam voluptatem ut reprehenderit ipsa iste. Est qui expedita quod. Vel vel et consectetur ut.','2020-05-22 08:58:38'),(15,2,'Quas esse molestiae optio.','Accusamus tempore fugit quaerat ducimus est. Quo atque ut et accusantium ipsam cumque dignissimos. Error aut qui distinctio perferendis.','2018-09-23 21:04:13'),(16,10,'A eum a sequi maxime.','Quod eos explicabo debitis ipsam. Consequatur sequi aliquid possimus animi id. At earum odit laudantium deleniti ut quidem. Consequuntur in earum rerum iure.','2017-01-24 10:10:50'),(17,13,'At vitae ut aut est totam.','Non quia soluta eius quasi tempore. Consequuntur illum consequuntur et et.','2017-01-02 21:49:30'),(18,2,'Quam autem odit in.','Neque praesentium necessitatibus vitae minus. Exercitationem sed nihil quia optio. Qui sed inventore pariatur quibusdam velit aut qui quia. Repellendus ex dolorem qui est.','2016-01-24 03:50:39'),(19,15,'Est qui nobis sequi ab.','Ullam necessitatibus ut pariatur. Nobis quos nam in consectetur quia. Voluptatum sed ab a est. Maiores tempore dolores quasi blanditiis eum error.','2020-04-29 15:39:05'),(20,2,'Aspernatur ut veniam ut.','Non omnis at perferendis quis. Corporis et qui quos nostrum iusto. Eos ex aspernatur voluptas libero est id. Dolores est alias placeat enim enim iusto qui.','2018-08-02 18:00:50'),(21,3,'Nihil ut accusantium officia.','In inventore fuga nostrum voluptatem eius aut qui ipsum. Sunt amet repudiandae voluptatem magni laborum ea sit. Laudantium et architecto quia incidunt ut possimus amet.','2022-07-09 05:19:05'),(22,1,'Velit quod ut ipsum ipsa.','Laboriosam eos officia natus ut aut error molestiae. Esse in in dolorum sit molestiae eum aut. Voluptatem perspiciatis dolor unde et ab accusamus.','2015-07-03 08:39:55'),(23,12,'Voluptas magni et et velit.','Libero qui aut vitae qui. Occaecati odio autem fuga qui occaecati quod. Labore eos commodi eum quis. Magni mollitia et esse velit.','2021-04-07 12:35:07'),(24,11,'Nobis aut quos et minima.','Qui asperiores voluptatum ipsum doloribus a. Eum quo ipsa id. Et voluptas quidem cum ut ut non.','2021-10-12 04:07:00'),(25,10,'Sed fugiat quam consequatur.','Qui et accusantium autem atque. Praesentium quia provident eligendi praesentium et eum et. Expedita ea eum et libero. Veniam quisquam temporibus est aut.','2019-01-12 19:18:49'),(26,2,'Nam molestiae ut accusantium.','Nesciunt vel molestiae et aut dolorum et. Nisi odit et molestias. Error autem dicta perspiciatis omnis. Aut voluptatum non nulla ut.','2019-01-23 06:49:42'),(27,7,'Quas cum aut quod illum et.','Et consequuntur et saepe repellendus quod. Repellat est quis odio nihil debitis officia deleniti debitis. Atque et asperiores sequi possimus iusto.','2021-10-27 02:05:49'),(28,9,'Dolorem labore quod iste.','Possimus nam minima voluptatem et optio. Ut qui ex deserunt et maxime quo asperiores.','2013-10-15 02:42:06'),(29,2,'Ea quo cum est earum.','Ullam omnis perferendis ut laboriosam et. Labore et error laboriosam. Suscipit suscipit sed aut hic enim reiciendis debitis.','2012-09-24 20:46:05'),(30,5,'Enim nostrum autem deleniti.','Repellendus doloremque placeat aliquam ut odit. Placeat neque enim magnam in. Ut facilis et quia dolor reiciendis quasi.','2017-04-05 16:00:19'),(31,9,'Sint sunt sunt voluptatem.','Pariatur ducimus fugit non ut at nisi. Voluptatem dolorem sed et eius ut sequi numquam. Eos adipisci nemo ad atque cupiditate aliquid est sed.','2014-12-09 14:51:10'),(32,14,'Rem minus ea sunt atque.','Ratione odit eius eaque blanditiis totam. Ab eum dolorem consequatur quibusdam. Quas adipisci dolorem recusandae dolore.','2020-06-15 03:34:09'),(33,7,'Ratione tempora mollitia ut.','Consequatur ut unde nostrum fugit asperiores facilis sed. Ipsa quia aut eaque reprehenderit rem. Cumque quae aut impedit distinctio corporis aperiam.','2012-09-24 08:34:27'),(34,11,'Rerum eum et vel ut.','Aspernatur quia voluptatem sed odio commodi saepe. Ea est et voluptatem deleniti voluptatem eum. Voluptas et magnam occaecati rerum.','2016-11-14 08:15:59'),(35,10,'Est temporibus ut esse aut.','Quisquam fugiat et et quisquam sed sunt. Debitis doloremque qui possimus porro aliquam quam non. Possimus aut architecto culpa neque officiis voluptatum accusantium.','2015-06-09 04:31:16'),(36,1,'Voluptas numquam vero autem.','Iure temporibus amet velit voluptas. Unde aut iste vel nemo laborum nulla perspiciatis. Cum voluptas doloremque at non. Sint porro est non atque illo dicta cumque.','2022-02-13 08:55:21'),(37,6,'Placeat aut nulla aut qui.','Ut et possimus at rerum dignissimos sapiente. At nesciunt impedit voluptate iure dolor sint magnam.','2012-08-25 17:59:50'),(38,5,'Cum occaecati adipisci sed.','Voluptatem aut cum totam dolore. Molestiae qui commodi molestiae soluta ipsam illum. Adipisci cupiditate autem distinctio nobis.','2021-03-08 15:32:54'),(39,9,'Et et voluptas sed unde.','Non sit placeat officia tempore. In nesciunt quos dolor qui. Fugiat odio dolore tempore minus. Laboriosam praesentium voluptatibus aut enim omnis aut.','2019-08-11 10:45:44'),(40,7,'Ut ut consequuntur eum dicta.','At impedit similique perspiciatis nesciunt fugiat officia qui. Repellendus dolorum consectetur cupiditate qui saepe esse. Saepe a laboriosam tempore non. Vero qui dolore aut odio autem ullam illo.','2021-06-04 14:48:05'),(41,4,'Dolore eveniet at tempore.','Eum consequuntur quis explicabo in ad non ad. Rerum doloremque assumenda rem ad non autem. Dolores voluptatem dolores voluptatem quas molestias quis iste.','2015-10-10 05:57:45'),(42,10,'Non quia sunt earum.','Beatae eaque commodi non magni et consequatur expedita. Est omnis dolorem suscipit quibusdam laudantium aspernatur. Sed et est voluptates. Voluptatem non aut animi temporibus.','2020-10-22 11:33:20'),(43,11,'Sit consectetur rerum nulla.','Amet hic amet quam culpa ut quia. Nesciunt recusandae atque doloremque. Aut voluptas aliquam voluptates accusantium accusantium.','2015-01-22 00:54:42'),(44,11,'In ab qui soluta et et neque.','Dolor molestiae doloremque nam laudantium dolor corporis. Repellat minus modi non vitae. Eveniet eum non vel sit magnam blanditiis quia.','2016-03-21 00:04:47'),(45,5,'Ut earum ut quisquam et ut.','Ut est totam asperiores rerum voluptatem ipsam ut. Doloremque sequi aut praesentium dolor minima. Officia ipsum similique sed deleniti rerum minima.','2014-01-29 10:16:12'),(46,10,'Qui dolorem incidunt id ea.','Qui ipsum sit odio id quam incidunt magnam. Quasi quae debitis exercitationem molestiae hic perferendis harum. Perspiciatis qui aperiam non est quod. Deserunt est occaecati in in nihil quas sit.','2015-11-29 16:01:13'),(47,7,'Est quis modi rerum optio.','Accusamus sunt ut in suscipit nulla. Earum officia nostrum repudiandae tempora est porro sint. Et consequuntur sit blanditiis doloremque magni.','2015-03-13 07:55:25'),(48,11,'Ut eos enim voluptatem enim.','Illo dolore assumenda animi quis. Id magni ut aut et. Molestiae ratione ratione eligendi impedit autem. Aut provident voluptas esse praesentium.','2014-12-31 06:44:54'),(49,4,'Quod harum eius qui modi qui.','Error rem quod qui aspernatur explicabo ut possimus. Et error deleniti quidem aliquam aut. Aut tempore at atque aut similique. Cumque eos libero veritatis assumenda.','2019-06-21 06:11:21'),(50,1,'Tenetur et eius non corrupti.','Enim ullam rerum ipsum facere aut. Ullam optio aut qui aperiam cupiditate non ipsum. Laboriosam dignissimos corrupti sequi qui.','2016-12-03 07:17:32'),(51,1,'Quia ut quod eaque quas quam.','Sed iure eum ipsum nisi voluptas doloremque nesciunt. Velit itaque impedit quas totam aut fuga. Et vero deleniti officiis aut eligendi praesentium. Excepturi qui dolores libero sit non et similique.','2020-02-08 19:22:38'),(52,7,'Aut illo vel eligendi odit.','Excepturi officia ut explicabo quaerat. Cupiditate tenetur ut quas quod. Deleniti quam id quasi. Quam quidem et eum aut est accusantium voluptatum.','2016-09-07 21:54:45'),(53,6,'Quam id deleniti vel.','Dignissimos ducimus et amet accusantium. Molestiae veniam dolorum dolorem voluptas. Maiores expedita eaque suscipit numquam.','2019-04-11 20:24:15'),(54,8,'Vel aut ullam omnis eius.','Dolores quae commodi est dolorem nobis non id. Soluta et fugiat veniam. Possimus ut soluta dolorem earum nulla.','2017-04-11 06:38:50'),(55,4,'Sint iusto quia non.','Nam qui sed doloribus nulla molestiae. Sed magni eum voluptate odio repudiandae. Vel labore sunt accusamus asperiores. Magni ab veniam nisi qui nostrum sit.','2018-01-28 22:07:54'),(56,9,'Dicta rerum labore iure.','Et unde voluptas velit sed ut vitae. Velit ut eveniet cumque neque explicabo laborum rerum. Voluptatibus rerum culpa excepturi. Ut aliquid enim quaerat ab. Totam aspernatur aut ratione cum ex id.','2020-04-03 11:31:59'),(57,12,'Et velit vitae voluptatem.','Ut laboriosam distinctio sequi tenetur sapiente. Fuga eligendi saepe voluptas. Id necessitatibus porro nobis beatae nobis eaque. Soluta quo eligendi vel velit velit.','2017-03-19 22:42:49'),(58,3,'At pariatur dolorem et.','Ea ipsa pariatur ut rerum tenetur assumenda. Alias rerum quas ratione tempore aliquid. Sed qui excepturi sit vel modi. Harum voluptatem quis dolor neque sapiente voluptas.','2012-08-12 20:02:49'),(59,14,'Aut iste iure et commodi.','Eum voluptatum voluptas officiis voluptas. Quo nihil perferendis dicta sed optio ea. Facere dolores odit similique et soluta.','2021-02-08 20:29:19'),(60,10,'Consequatur et et dolor aut.','Qui repellendus est aut officiis qui mollitia in itaque. Sed eum eos possimus dicta ea accusantium. Ex explicabo facere porro quos mollitia et. Eius tenetur facere velit autem similique velit enim.','2014-12-31 00:09:00'),(61,10,'Ipsam libero minus velit.','Veniam vel vitae qui itaque magnam ut quae. Voluptatem minus at quo quod dolorem sequi sequi. Et totam possimus id explicabo modi sint laboriosam. At deleniti aspernatur vero blanditiis et ea.','2020-04-09 13:53:36'),(62,5,'Adipisci eum accusamus ut.','Commodi possimus quia ullam aliquam est rerum quo possimus. Quaerat ipsa qui suscipit ut in. In quo vero sunt totam porro fugiat eos. Adipisci molestias fugiat consequatur.','2021-07-06 01:18:41'),(63,4,'Quia maxime dolor molestiae.','Cum non distinctio sequi aut. Temporibus quod quo non et aut cumque perspiciatis doloremque. Alias quas tempore dolorem et natus. Rerum exercitationem excepturi et tempora.','2019-11-14 16:04:58'),(64,12,'Qui et amet odit sit.','Qui illo inventore eligendi illum error error facilis. Qui eum ut officiis sit aperiam. Aspernatur sunt ut omnis vel.','2014-03-11 19:54:58'),(65,3,'Quis nisi eveniet dolores.','Ducimus exercitationem optio sed non. Omnis omnis et qui quis dolor nisi vel repellendus. Nihil ut enim autem eos qui harum temporibus.','2019-01-24 04:39:29'),(66,12,'Fugiat quis error asperiores.','Et aliquam quia accusamus labore quasi minima. Maiores quibusdam nemo et eveniet.','2015-09-18 09:21:50'),(67,14,'Eius in quo commodi maiores.','Deserunt maxime natus ut sunt quod. Dolores architecto mollitia non maiores deleniti qui. Harum pariatur perspiciatis deserunt ea labore.','2018-05-24 22:35:42'),(68,7,'Ut minima optio quia.','Voluptatem natus ut sit iusto. Eum assumenda qui est aut. Ea et ut officia ad dolore eos.','2016-01-25 02:52:20'),(69,1,'Minima id ad harum.','Rem necessitatibus quia est fugit ad. Necessitatibus enim ea sit voluptas eius laboriosam id. Iusto commodi incidunt rerum quam.','2016-03-22 10:28:23'),(70,13,'Amet maxime ullam distinctio.','Quasi provident eligendi molestias iusto autem consequatur officia perferendis. Exercitationem dolores omnis esse quibusdam consequatur omnis facere occaecati.','2014-06-21 09:11:03'),(71,4,'Quam ut ea aliquid sit quis.','Eum quo exercitationem quo accusantium rem mollitia. Porro vel et iste soluta quia est. Error voluptates quis nemo omnis.','2021-10-11 05:09:58'),(72,10,'Sunt est et dolores at.','Voluptatem corrupti qui consequatur qui ut quod. Iste qui nemo velit aut. Repellendus eligendi quia laborum ut aut voluptas. Molestiae vel quae ut.','2020-06-09 03:05:30'),(73,15,'Vel fugit sint voluptatem.','Hic facilis quidem dolorum qui. Molestiae delectus eos reprehenderit laboriosam ut. Adipisci ut rerum necessitatibus. Vero illum qui beatae quis quae.','2019-07-16 04:59:14'),(74,11,'Vero rerum vel qui.','Esse et iusto nobis fugiat non ipsam occaecati id. Molestias sint voluptatem neque velit. Dolor autem velit ipsa quia.','2016-09-07 14:06:28'),(75,9,'Quaerat sed facilis quod.','Voluptate dolore sequi aut dolorem dolorum est ipsum. Temporibus hic amet saepe similique natus. Possimus consequatur est asperiores ullam magnam velit nam.','2014-01-08 01:22:27'),(76,11,'Quasi quis odit id sunt nemo.','Aliquid sed omnis exercitationem maxime dicta et. Inventore laudantium accusantium natus expedita amet quidem. Nulla fuga in ducimus nesciunt.','2014-09-04 18:55:20'),(77,4,'A libero qui nisi eos omnis.','Excepturi perspiciatis ad temporibus accusantium voluptas non. Itaque ullam natus voluptatibus voluptates quo. Qui exercitationem voluptates minima illum. Quia minus qui fugiat enim aut consequuntur.','2014-02-14 00:41:59'),(78,15,'Rerum est porro atque.','Quis veniam sed nihil distinctio qui vitae neque. Perferendis distinctio voluptatibus id unde. Dolore velit doloremque suscipit ratione et quia. Tempora est ut optio ea.','2013-07-03 08:38:41'),(79,6,'Aperiam quo animi earum et.','Modi iure voluptatem in. Quia officiis perspiciatis animi non eos et. Vel fugit quia rerum exercitationem quidem inventore. Nemo et a nulla vitae suscipit expedita.','2017-02-21 15:17:25'),(80,8,'Aperiam ab sed est laborum.','Non quibusdam modi recusandae libero iste. Explicabo sint odit reiciendis architecto optio eaque eaque. Eius facere sequi ea animi.','2013-11-19 21:23:56'),(81,8,'Similique ratione quo nihil.','Non et asperiores possimus illum pariatur ea sit. Officia fugit perspiciatis et hic. Ea ducimus est voluptates in explicabo. Ullam facere blanditiis laboriosam eveniet.','2019-07-19 11:11:22'),(82,15,'Eos cum suscipit eum autem.','Sapiente quidem rerum mollitia minus nulla cum. Tempore quod debitis qui at dolores a. Enim saepe aut quam est occaecati quibusdam debitis.','2015-03-20 19:17:16'),(83,14,'Atque ut enim amet.','Sit et expedita sint ullam rerum reiciendis deleniti. Perferendis tenetur error ut tempore in fugit non. Perspiciatis ullam aut adipisci repudiandae et.','2017-07-22 05:22:32'),(84,3,'Expedita blanditiis ut sed.','Minima voluptatem itaque possimus ut perspiciatis. Similique exercitationem rerum voluptas. Et consequatur debitis nemo cum debitis reiciendis. Sunt ea in ullam.','2014-04-22 18:42:42'),(85,1,'Quis iure nihil quam.','Magni nihil adipisci autem a quo ex molestias. Quo laborum sit omnis quia. Ea aut et sit voluptate labore quas sit. Architecto eveniet ut facilis voluptatem eaque.','2013-08-20 06:27:52'),(86,11,'Est ipsa eius ipsum eius.','Eos temporibus deserunt porro. Eum nam similique quaerat perferendis ut. Perferendis omnis necessitatibus quo officia dolorem.','2012-11-02 18:03:19'),(87,5,'Est dolorum et in.','Dolorem ab numquam consequuntur aut quasi repellat. Est mollitia laborum sint sapiente. Asperiores error modi amet quis.','2021-04-20 16:47:59'),(88,10,'Aut illo autem sit voluptate.','Repudiandae cum eum dolores autem. Rerum odio autem qui sint commodi occaecati sed. Est quaerat consequuntur earum voluptatum officiis.','2013-03-09 07:11:58'),(89,13,'Tempore eius totam non.','Officia repudiandae pariatur quisquam quidem quaerat est. Ut iste minus accusantium qui laudantium asperiores. Aut aut rerum enim facilis illo doloremque vel.','2017-12-10 20:09:37'),(90,3,'Qui sint rerum harum.','Expedita quaerat hic qui. Officia molestias autem eveniet aut ut illo aliquid. Sed culpa consequatur ut nulla. Quidem quisquam accusantium cumque.','2016-08-17 22:50:20'),(91,10,'Ea fuga earum dicta.','Repellendus quaerat id non quae minima cum qui. Aut necessitatibus perferendis laborum ab. Animi rerum quia voluptatibus tempora architecto et illo. Quam sunt tenetur similique vero.','2018-04-18 20:42:10'),(92,15,'Est et eaque magnam.','Quia neque ut reprehenderit omnis et in soluta. Dolorum omnis facere optio quia laudantium. Saepe officiis velit eaque quis. Sit est molestias tempora perspiciatis sed dolorem magni.','2022-04-15 18:15:07'),(93,9,'Fuga aperiam blanditiis at.','Rerum accusantium est eius dolor expedita. Qui maxime consequatur et tempora et amet. Expedita fuga expedita et corporis nulla pariatur. Consequatur a enim et mollitia tenetur non et.','2020-12-05 15:14:47'),(94,4,'Vel aspernatur iure quas.','Minus architecto sed veritatis neque in. Earum veritatis velit dolor consequatur. Odio et itaque modi omnis nobis.','2020-05-12 11:57:01'),(95,13,'Est sint accusamus ipsa a.','Doloribus voluptatem sint neque. Consequatur qui aliquid laudantium a dicta. Officiis hic eius rerum voluptate facilis ipsam qui. Quo ut ducimus sit molestias iure.','2013-08-16 15:08:20'),(96,5,'Corrupti qui eius est.','Deserunt cum magnam non praesentium perferendis qui et ducimus. Temporibus aliquid deserunt sunt neque veniam ut nostrum. Architecto minus voluptatem sunt. Enim sit commodi voluptatem et.','2019-06-06 10:39:48'),(97,12,'Sit voluptas modi nobis et.','Numquam qui iusto pariatur atque laboriosam. Ea quaerat ea praesentium perspiciatis beatae blanditiis ex. Dolor tempore fugit voluptatem culpa voluptates.','2021-10-24 06:58:17'),(98,9,'Est enim sed esse similique.','Maxime veniam quidem delectus reiciendis. Eos corrupti enim ratione nesciunt blanditiis ipsa exercitationem ullam. Aut voluptatem odit exercitationem numquam tempora minus praesentium tenetur.','2019-10-30 19:42:43'),(99,3,'Aut dolores et eos dolorum.','Hic corporis placeat dolor qui libero reiciendis. Dolores perspiciatis pariatur tempora ducimus et. Ratione ab ducimus quod minus veniam ex.','2022-03-03 20:15:13'),(100,11,'Officia quia laborum enim.','Minus voluptatibus eos sunt perferendis nostrum illum. Esse qui dicta officia molestiae placeat. Non nihil doloribus est omnis ea quidem.','2012-12-31 14:52:46');
/*!40000 ALTER TABLE `post` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `post` with 100 row(s)
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
INSERT INTO `post_tag` VALUES (1,7),(2,3),(2,9),(2,11),(2,16),(3,6),(4,11),(4,12),(4,13),(4,14),(4,17),(5,2),(5,6),(5,8),(5,11),(5,12),(6,9),(6,10),(6,14),(6,19),(7,1),(7,11),(7,14),(7,16),(7,20),(8,8),(8,17),(8,18),(8,20),(9,7),(9,8),(10,3),(10,4),(10,12),(11,3),(11,13),(12,6),(12,9),(13,1),(13,14),(13,19),(14,18),(15,5),(15,6),(16,12),(16,19),(17,2),(17,5),(17,13),(17,14),(17,20),(18,6),(18,10),(18,15),(18,17),(19,3),(19,4),(19,8),(19,13),(19,17),(20,1),(20,5),(20,8),(20,9),(20,15),(21,18),(22,9),(22,10),(22,11),(22,16),(23,13),(23,20),(24,13),(25,1),(25,15),(25,19),(26,2),(26,3),(26,16),(26,17),(27,5),(27,14),(28,7),(28,14),(29,2),(29,13),(29,17),(29,20),(30,6),(30,8),(31,3),(31,6),(31,19),(32,13),(32,17),(32,20),(33,3),(33,11),(33,17),(33,20),(34,10),(35,1),(35,7),(35,15),(35,18),(35,19),(36,1),(36,10),(36,13),(37,7),(37,9),(37,11),(37,17),(37,20),(38,1),(38,20),(39,2),(39,7),(39,14),(39,20),(40,15),(40,17),(41,8),(41,17),(42,2),(42,3),(42,4),(43,5),(43,20),(44,6),(44,11),(44,16),(44,19),(45,6),(46,1),(46,3),(46,5),(46,14),(46,19),(47,2),(47,11),(47,13),(47,14),(48,1),(48,3),(48,18),(48,20),(49,7),(49,11),(49,12),(49,14),(50,18),(51,5),(52,7),(52,8),(52,12),(52,16),(53,1),(53,14),(53,15),(53,16),(54,6),(54,15),(55,4),(55,5),(55,6),(55,7),(55,19),(56,3),(56,11),(57,4),(57,12),(57,15),(58,9),(58,17),(58,19),(59,2),(59,10),(59,11),(59,17),(60,12),(60,13),(60,15),(60,20),(61,2),(61,14),(61,16),(62,5),(62,9),(62,12),(62,19),(62,20),(63,2),(63,6),(64,3),(64,7),(64,10),(64,18),(65,11),(66,2),(66,11),(67,5),(67,7),(67,11),(67,19),(68,4),(68,15),(68,20),(69,5),(69,6),(69,11),(69,12),(69,18),(70,9),(70,19),(71,6),(71,9),(71,14),(71,16),(72,1),(72,13),(73,3),(73,5),(73,6),(73,17),(73,20),(74,3),(74,7),(74,11),(74,12),(74,19),(75,8),(75,10),(75,18),(75,19),(75,20),(76,2),(76,3),(76,4),(76,7),(76,9),(77,1),(77,5),(77,11),(78,1),(78,7),(78,13),(78,14),(78,18),(79,19),(80,5),(81,1),(81,7),(81,14),(81,20),(82,2),(82,3),(83,4),(83,9),(83,12),(83,19),(84,3),(84,5),(84,11),(84,18),(84,20),(85,1),(85,6),(85,7),(86,2),(86,3),(86,4),(86,5),(86,13),(87,11),(87,13),(88,15),(88,19),(89,2),(89,7),(89,20),(90,3),(90,5),(90,8),(90,15),(91,4),(91,8),(91,17),(92,3),(92,5),(92,15),(92,16),(92,19),(93,19),(94,3),(94,9),(94,18),(95,3),(95,5),(95,7),(95,12),(95,18),(96,1),(96,7),(96,8),(96,19),(96,20),(97,1),(97,10),(97,13),(98,17),(99,4),(99,8),(99,20),(100,2),(100,7),(100,11),(100,14),(100,16);
/*!40000 ALTER TABLE `post_tag` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `post_tag` with 319 row(s)
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
INSERT INTO `tag` VALUES (1,'libero'),(2,'est'),(3,'vero'),(4,'reprehenderit'),(5,'voluptate'),(6,'natus'),(7,'dolor'),(8,'quam'),(9,'quas'),(10,'soluta'),(11,'voluptatum'),(12,'quod'),(13,'est'),(14,'velit'),(15,'inventore'),(16,'deleniti'),(17,'nostrum'),(18,'quia'),(19,'omnis'),(20,'et');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `tag` with 20 row(s)
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
INSERT INTO `user` VALUES (1,'renee.duhamel@noos.fr','[]','$2y$13$wD/tsbF52MwxXFzhrvXcae0k3AZogxaMe14uW1NsPCpcr5RvDAcV2'),(2,'dominique.gilles@launay.fr','[]','$2y$13$EDSAluXa3SBVp4QnND3Z9OulgJfahuMsZ/5y2mTJZvlHKM7H.vMsi'),(3,'jacques.fontaine@lombard.net','[]','$2y$13$5FZqQvDB.ksDfpfyvdGkau1U.lB4VSDK6SdIGuK2VA4A24ec96fLG'),(4,'christiane.brun@becker.org','[]','$2y$13$77CQLnSuzYyYoUH3VB/MZeTRiHm/vN6AnF4tW1enTcOG.UQi2cHD6'),(5,'lebrun.francois@dupuy.fr','[]','$2y$13$RQDkyE06aI4LYkO71xIZje1zSVmRhq6nzj2RXyto/DUCUz2SYl6Gu'),(6,'ylaporte@seguin.org','[]','$2y$13$PwOU7xOfNjNXeTFmkj1VLObZIq/FTunM.6L1HLaxbT3.RvNtBH/hm'),(7,'danielle47@live.com','[]','$2y$13$iMkp5DuWR0g.g5brJFi.5eGXUTaeJe8chMsk/q5t3mYKOFDZR84R2'),(8,'franck.pinto@wanadoo.fr','[]','$2y$13$VnaoQpTf9zQ2EqCDcOqzJu9nZ7FMiO/HnY3QY0FXeX/w.cf4IshHi'),(9,'margot43@free.fr','[]','$2y$13$4m/BaboN.EcYuA55BbEfi.N2kCg9KauR4F90yNblRDnhe00Uy7iLi'),(10,'camus.alex@bonneau.fr','[]','$2y$13$gJKPNYv3n0Vfl3fOt38NaubmHOykYhEpgDgIgtiXjkEcwNnkJQcxS'),(11,'vaillant.benoit@chevalier.com','[]','$2y$13$5GIanf6u/I2yUJT3nz7xsuU2TEyIJVPAfolWddYTkvHmOu3Ee36TC'),(12,'elise.dias@yahoo.fr','[]','$2y$13$OOoqlzaShroRkxa.KPKcaOO0cOmF4ShzPhf8Y.blOmcS1oVV0nAR6'),(13,'cordier.michelle@laposte.net','[]','$2y$13$TNidbknqT5fwp2yLfkroSux/uv3brCEbdtedO3ma/RM5Olno6zcNi'),(14,'aroussel@wanadoo.fr','[]','$2y$13$IalGcpMcEXMLldn7j2F4.O7Yo7DCVxDwniBgp09dK8xVvEZgpwMV6'),(15,'buisson.thibault@sfr.fr','[]','$2y$13$P/LCnpf/xPQnoS4CoqBgQO8c8nCLvuZjgliUM0f..JkdnQpLIxPTu');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `user` with 15 row(s)
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on: Thu, 01 Sep 2022 16:25:47 +0200
