-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: castorypollux
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `qr_codigos`
--

DROP TABLE IF EXISTS `qr_codigos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qr_codigos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid_qr` char(36) NOT NULL COMMENT 'UUID del c├│digo QR',
  `estado` enum('PENDIENTE','HABILITADO','DESHABILITADO') NOT NULL DEFAULT 'PENDIENTE' COMMENT 'PENDIENTE=No asignado, HABILITADO=Activo, DESHABILITADO=Inactivo',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_asignacion` datetime DEFAULT NULL COMMENT 'Fecha cuando se asign├│ a un funcionario',
  `observaciones` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid_qr` (`uuid_qr`),
  KEY `idx_uuid` (`uuid_qr`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='C├│digos QR generados para veh├¡culos municipales';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qr_codigos`
--

LOCK TABLES `qr_codigos` WRITE;
/*!40000 ALTER TABLE `qr_codigos` DISABLE KEYS */;
INSERT INTO `qr_codigos` VALUES (1,'97358b20-bf44-11f0-ae71-a036bc6763e9','PENDIENTE','2025-11-11 18:22:56',NULL,'QR de prueba 1'),(2,'97358cd9-bf44-11f0-ae71-a036bc6763e9','PENDIENTE','2025-11-11 18:22:56',NULL,'QR de prueba 2'),(3,'97358d61-bf44-11f0-ae71-a036bc6763e9','PENDIENTE','2025-11-11 18:22:56',NULL,'QR de prueba 3'),(4,'97358d98-bf44-11f0-ae71-a036bc6763e9','PENDIENTE','2025-11-11 18:22:56',NULL,'QR de prueba 4'),(5,'97358dcc-bf44-11f0-ae71-a036bc6763e9','PENDIENTE','2025-11-11 18:22:56',NULL,'QR de prueba 5'),(6,'afff3b34-5e16-4d25-8866-07aac868f2ca','PENDIENTE','2025-11-12 15:17:33',NULL,'Lote generado - 2025-11-12 15:17:33'),(7,'17505426-2d28-4f55-965f-8f3cf22451bc','PENDIENTE','2025-11-12 15:17:33',NULL,'Lote generado - 2025-11-12 15:17:33'),(8,'e1e849bc-9b8f-4583-bf40-db355691daa9','PENDIENTE','2025-11-12 15:17:33',NULL,'Lote generado - 2025-11-12 15:17:33'),(9,'b8813123-cf45-4498-944c-42ecfaa574e8','PENDIENTE','2025-11-12 15:17:33',NULL,'Lote generado - 2025-11-12 15:17:33'),(10,'ebb5bbde-881e-458d-b316-5a5c3f166bab','PENDIENTE','2025-11-12 15:17:33',NULL,'Lote generado - 2025-11-12 15:17:33'),(11,'d1cc51fc-064a-4e05-a2bb-904d4516b7c3','PENDIENTE','2025-11-12 15:17:33',NULL,'Lote generado - 2025-11-12 15:17:33'),(12,'ab021d66-edeb-4188-a25c-1c4867cccf3d','PENDIENTE','2025-11-12 15:17:33',NULL,'Lote generado - 2025-11-12 15:17:33'),(13,'c841cea7-ea78-44a5-8a22-f0b3299d1f2f','PENDIENTE','2025-11-12 15:17:33',NULL,'Lote generado - 2025-11-12 15:17:33'),(14,'8458cd5e-c0b0-4902-8f3b-d80f1c80332d','PENDIENTE','2025-11-12 15:17:33',NULL,'Lote generado - 2025-11-12 15:17:33'),(15,'cc94f1f8-5eea-480a-a7ac-6ded9a0b269a','PENDIENTE','2025-11-12 15:17:33',NULL,'Lote generado - 2025-11-12 15:17:33'),(16,'23ae60ab-352e-4fdb-ad29-4e8e120ef682','PENDIENTE','2025-11-12 15:19:40',NULL,'Lote generado - 2025-11-12 15:19:40'),(17,'08572284-1158-4463-9141-515cc1885a57','PENDIENTE','2025-11-12 15:19:40',NULL,'Lote generado - 2025-11-12 15:19:40'),(18,'69aef0d8-1d60-4609-904a-74559c84528f','PENDIENTE','2025-11-12 15:19:40',NULL,'Lote generado - 2025-11-12 15:19:40'),(19,'821834e4-c121-4a24-9cf0-ccade59e35de','PENDIENTE','2025-11-12 15:19:40',NULL,'Lote generado - 2025-11-12 15:19:40'),(20,'80cba7db-f40a-4073-a351-afcdaf9fa412','PENDIENTE','2025-11-12 15:19:40',NULL,'Lote generado - 2025-11-12 15:19:40'),(21,'0f2a4914-4759-4ae8-84a5-d71b1d51e56a','PENDIENTE','2025-11-12 15:19:40',NULL,'Lote generado - 2025-11-12 15:19:40'),(22,'028b9503-c973-4fce-93ea-c5b6527ff42d','PENDIENTE','2025-11-12 15:19:40',NULL,'Lote generado - 2025-11-12 15:19:40'),(23,'8528a9d6-1c53-42ba-813c-ceb017b7dd14','PENDIENTE','2025-11-12 15:19:40',NULL,'Lote generado - 2025-11-12 15:19:40'),(24,'8d7bfa41-55a2-4f80-9ed9-74198d8defbf','PENDIENTE','2025-11-12 15:19:40',NULL,'Lote generado - 2025-11-12 15:19:40'),(25,'e082eb4c-01a2-4eb9-b4b4-887246a2a04b','PENDIENTE','2025-11-12 15:19:40',NULL,'Lote generado - 2025-11-12 15:19:40'),(26,'0e8ef437-305d-4ada-8af2-682d5e263856','PENDIENTE','2025-11-12 15:21:10',NULL,'Lote generado - 2025-11-12 15:21:10'),(27,'d5f66362-9e47-475e-a6b5-f39111f83b49','PENDIENTE','2025-11-12 15:21:10',NULL,'Lote generado - 2025-11-12 15:21:10'),(28,'affcbcc8-6b5d-4a9d-b649-c2f09d3d27af','PENDIENTE','2025-11-12 15:21:10',NULL,'Lote generado - 2025-11-12 15:21:10'),(29,'db228435-e3f3-407c-a76a-2de9013b1d16','PENDIENTE','2025-11-12 15:21:10',NULL,'Lote generado - 2025-11-12 15:21:10'),(30,'1e203ba3-5e08-4bd0-840d-59e9095bba6c','PENDIENTE','2025-11-12 15:21:10',NULL,'Lote generado - 2025-11-12 15:21:10'),(31,'e1f944cd-6ead-4095-8f92-e4c90557d6cc','PENDIENTE','2025-11-12 15:21:10',NULL,'Lote generado - 2025-11-12 15:21:10'),(32,'5f0e7cb4-2ed8-46f1-93a5-041232cbf4ba','PENDIENTE','2025-11-12 15:21:10',NULL,'Lote generado - 2025-11-12 15:21:10'),(33,'fd9f1fc8-b5a7-4b41-8289-4ae8a0ee1ba0','PENDIENTE','2025-11-12 15:21:10',NULL,'Lote generado - 2025-11-12 15:21:10'),(34,'4e884d66-1361-4387-a672-b6e1be27014a','PENDIENTE','2025-11-12 15:21:10',NULL,'Lote generado - 2025-11-12 15:21:10'),(35,'7176e02a-0eb9-48ec-bc7e-c0488a89330e','PENDIENTE','2025-11-12 15:21:10',NULL,'Lote generado - 2025-11-12 15:21:10'),(36,'33cf2cf9-7edf-40b9-9361-0e72abcc8532','PENDIENTE','2025-11-12 15:35:40',NULL,'Lote generado - 2025-11-12 15:35:40'),(37,'34ef7c7f-388b-49c6-b367-e5c2033ead24','PENDIENTE','2025-11-12 15:35:40',NULL,'Lote generado - 2025-11-12 15:35:40'),(38,'737c2d28-653a-4ee8-ad6e-5348b9de1a3c','PENDIENTE','2025-11-12 15:35:40',NULL,'Lote generado - 2025-11-12 15:35:40'),(39,'0c20001a-51f9-42b4-9348-812af52dce9b','PENDIENTE','2025-11-12 15:35:40',NULL,'Lote generado - 2025-11-12 15:35:40'),(40,'1b6270db-d402-4e78-a51d-ec97c9785931','PENDIENTE','2025-11-12 15:35:40',NULL,'Lote generado - 2025-11-12 15:35:40'),(41,'441773e5-cabd-4dc5-bd31-6efc4a453363','PENDIENTE','2025-11-12 15:35:40',NULL,'Lote generado - 2025-11-12 15:35:40'),(42,'317a0376-4e65-44f6-8a56-4c5f125d07b4','PENDIENTE','2025-11-12 15:35:40',NULL,'Lote generado - 2025-11-12 15:35:40'),(43,'899b55bc-54a0-43f4-a0ba-dfdff98fb614','PENDIENTE','2025-11-12 15:35:40',NULL,'Lote generado - 2025-11-12 15:35:40'),(44,'c20121c9-96a0-4b38-8548-dd21ebdaf2ea','PENDIENTE','2025-11-12 15:35:40',NULL,'Lote generado - 2025-11-12 15:35:40'),(45,'fb135444-4229-4d41-b497-533a0b0a952b','PENDIENTE','2025-11-12 15:35:40',NULL,'Lote generado - 2025-11-12 15:35:40'),(46,'0275ac90-7b57-4f22-bb55-1099068f2380','PENDIENTE','2025-11-12 15:42:39',NULL,'Lote generado - 2025-11-12 15:42:39'),(47,'3c7eb191-66b6-4784-a635-55a495ab65f2','PENDIENTE','2025-11-12 15:42:39',NULL,'Lote generado - 2025-11-12 15:42:39'),(48,'f5279e65-4d08-4dca-8207-bc06561be3c6','PENDIENTE','2025-11-12 15:42:39',NULL,'Lote generado - 2025-11-12 15:42:39'),(49,'7d66763e-81fb-48f4-aac0-2390ea9ed4d7','PENDIENTE','2025-11-12 15:42:39',NULL,'Lote generado - 2025-11-12 15:42:39'),(50,'d3f5ee1e-bea2-4a03-87ef-0bb395d84eae','PENDIENTE','2025-11-12 15:42:39',NULL,'Lote generado - 2025-11-12 15:42:39'),(51,'02757e58-b5a2-4365-ae51-b139a6f9c908','PENDIENTE','2025-11-12 15:42:39',NULL,'Lote generado - 2025-11-12 15:42:39'),(52,'5bac6185-91b2-42a2-ad73-4beb4ab808a5','PENDIENTE','2025-11-12 15:42:39',NULL,'Lote generado - 2025-11-12 15:42:39'),(53,'b83d9240-7449-43be-87aa-208ee5cc2324','PENDIENTE','2025-11-12 15:42:39',NULL,'Lote generado - 2025-11-12 15:42:39'),(54,'767b2c30-69f4-489e-9b13-4877c5408929','PENDIENTE','2025-11-12 15:42:39',NULL,'Lote generado - 2025-11-12 15:42:39'),(55,'99581056-0e4d-4877-b63c-b7f064329fba','PENDIENTE','2025-11-12 15:42:39',NULL,'Lote generado - 2025-11-12 15:42:39'),(56,'8272c950-989f-4a20-872b-4c150efcdb22','PENDIENTE','2025-11-12 15:47:09',NULL,'Lote generado - 2025-11-12 15:47:09'),(57,'6120828b-b073-4d7e-acab-9670857aac9f','PENDIENTE','2025-11-12 15:47:09',NULL,'Lote generado - 2025-11-12 15:47:09'),(58,'0a43489b-a434-4695-a480-d21d6583e336','PENDIENTE','2025-11-12 15:47:09',NULL,'Lote generado - 2025-11-12 15:47:09'),(59,'5c8d1843-9b37-46b0-a1e5-dbe60d39e9d5','PENDIENTE','2025-11-12 15:47:09',NULL,'Lote generado - 2025-11-12 15:47:09'),(60,'8ca2803d-df7d-4879-b423-5c6bd25e0266','PENDIENTE','2025-11-12 15:47:09',NULL,'Lote generado - 2025-11-12 15:47:09'),(61,'f1e5b5c6-7ec0-4cf5-b2e4-b8f882765676','PENDIENTE','2025-11-12 15:47:09',NULL,'Lote generado - 2025-11-12 15:47:09'),(62,'45fd2206-69f7-4102-b682-d03eef0225dd','PENDIENTE','2025-11-12 15:47:09',NULL,'Lote generado - 2025-11-12 15:47:09'),(63,'7f764d27-9692-4663-bfb7-10aea00f0d36','PENDIENTE','2025-11-12 15:47:09',NULL,'Lote generado - 2025-11-12 15:47:09'),(64,'f58e1c38-8c2b-45c6-a687-ee1f1cc99f1b','PENDIENTE','2025-11-12 15:47:09',NULL,'Lote generado - 2025-11-12 15:47:09'),(65,'b3a33024-d748-4f65-b8d6-3aa8b7a8e799','PENDIENTE','2025-11-12 15:47:09',NULL,'Lote generado - 2025-11-12 15:47:09'),(66,'3d77e8c3-b348-4326-adda-be442e3d7ed8','PENDIENTE','2025-11-12 15:51:29',NULL,'Lote generado - 2025-11-12 15:51:29'),(67,'17440e4e-1546-4e09-92eb-138557c9e0c8','PENDIENTE','2025-11-12 15:51:29',NULL,'Lote generado - 2025-11-12 15:51:29'),(68,'0fe3dbb7-6610-441e-8177-44961f5b606c','PENDIENTE','2025-11-12 15:51:29',NULL,'Lote generado - 2025-11-12 15:51:29'),(69,'fd1223a5-ca35-477c-8cbe-9907c0f0c126','PENDIENTE','2025-11-12 15:51:29',NULL,'Lote generado - 2025-11-12 15:51:29'),(70,'a2073190-4c1d-49d9-b795-fd6a00c959eb','PENDIENTE','2025-11-12 15:51:29',NULL,'Lote generado - 2025-11-12 15:51:29'),(71,'c7f51e4c-e5d7-400c-a3f8-6d01d23b6482','PENDIENTE','2025-11-12 15:51:29',NULL,'Lote generado - 2025-11-12 15:51:29'),(72,'a4005075-4302-45b0-a531-227fcd986a24','PENDIENTE','2025-11-12 15:51:29',NULL,'Lote generado - 2025-11-12 15:51:29'),(73,'8740ba85-688b-45e0-b2ac-1b5da8a48185','PENDIENTE','2025-11-12 15:51:29',NULL,'Lote generado - 2025-11-12 15:51:29'),(74,'054401d4-2e80-4f39-a2de-7808990f6f45','PENDIENTE','2025-11-12 15:51:29',NULL,'Lote generado - 2025-11-12 15:51:29'),(75,'bce972c4-be1f-4677-a53b-ff4b0ac5b313','PENDIENTE','2025-11-12 15:51:29',NULL,'Lote generado - 2025-11-12 15:51:29'),(76,'ed5d0ad8-ff15-4c3a-bc6d-7cb49adff27c','PENDIENTE','2025-11-12 15:53:59',NULL,'Lote generado - 2025-11-12 15:53:59'),(77,'fa8824d0-36fe-4878-86ee-bcad8530379c','PENDIENTE','2025-11-12 15:53:59',NULL,'Lote generado - 2025-11-12 15:53:59'),(78,'38f84a2d-0ae1-4ab5-92d6-c7eacc7571a2','PENDIENTE','2025-11-12 15:53:59',NULL,'Lote generado - 2025-11-12 15:53:59'),(79,'5469be9a-0377-41a3-8610-154a74bd85d6','PENDIENTE','2025-11-12 15:53:59',NULL,'Lote generado - 2025-11-12 15:53:59'),(80,'de9b2f38-dc7c-4f09-8878-d58a04682be4','PENDIENTE','2025-11-12 15:53:59',NULL,'Lote generado - 2025-11-12 15:53:59'),(81,'e62b4977-3c5e-4bc4-b25c-03ee0feca4f5','PENDIENTE','2025-11-12 15:53:59',NULL,'Lote generado - 2025-11-12 15:53:59'),(82,'d90b0581-b8d4-4327-88b7-09fd61fdeb6a','PENDIENTE','2025-11-12 15:53:59',NULL,'Lote generado - 2025-11-12 15:53:59'),(83,'4726e7e8-a23b-4185-90c6-7c66e71e998d','PENDIENTE','2025-11-12 15:53:59',NULL,'Lote generado - 2025-11-12 15:53:59'),(84,'c13ed898-05a3-41b6-9b78-eb39740492a4','PENDIENTE','2025-11-12 15:53:59',NULL,'Lote generado - 2025-11-12 15:53:59'),(85,'77687102-7cb5-4203-bc49-bfff7ec00bc8','PENDIENTE','2025-11-12 15:53:59',NULL,'Lote generado - 2025-11-12 15:53:59'),(86,'d2ef8039-3e22-4f3d-9785-b6535dde3c87','PENDIENTE','2025-11-12 15:55:19',NULL,'Lote generado - 2025-11-12 15:55:19'),(87,'1eaa0257-c462-4e6d-8433-e841f2ddbe1d','PENDIENTE','2025-11-12 15:55:19',NULL,'Lote generado - 2025-11-12 15:55:19'),(88,'7a537303-e413-4fdc-8959-b117c87db4f4','PENDIENTE','2025-11-12 15:55:19',NULL,'Lote generado - 2025-11-12 15:55:19'),(89,'690890c4-9188-4058-83f7-4f69058ba2e8','PENDIENTE','2025-11-12 15:55:19',NULL,'Lote generado - 2025-11-12 15:55:19'),(90,'eb0be6a2-5fab-4809-92a7-3016acc0377f','PENDIENTE','2025-11-12 15:55:19',NULL,'Lote generado - 2025-11-12 15:55:19'),(91,'29f9b6f1-f2a4-4df6-af9e-1b35a7d9bf3a','PENDIENTE','2025-11-12 15:55:19',NULL,'Lote generado - 2025-11-12 15:55:19'),(92,'1a0af0ea-9f53-4cd8-bc38-a059dc9d661b','PENDIENTE','2025-11-12 15:55:19',NULL,'Lote generado - 2025-11-12 15:55:19'),(93,'41d1a393-84c9-40fd-a826-6cbd86c6bfa5','PENDIENTE','2025-11-12 15:55:19',NULL,'Lote generado - 2025-11-12 15:55:19'),(94,'3338894d-1dd8-4da4-999d-82866d8102a1','PENDIENTE','2025-11-12 15:55:19',NULL,'Lote generado - 2025-11-12 15:55:19'),(95,'00cb0695-8258-45fa-b996-c7bf4cf82ecc','PENDIENTE','2025-11-12 15:55:19',NULL,'Lote generado - 2025-11-12 15:55:19'),(96,'e90a0280-7251-4049-b410-4ac9a604025b','PENDIENTE','2025-11-12 15:57:05',NULL,'Lote generado - 2025-11-12 15:57:05'),(97,'c6182c02-5fa6-4a35-88ab-1c575223bdd4','PENDIENTE','2025-11-12 15:57:05',NULL,'Lote generado - 2025-11-12 15:57:05'),(98,'803789d8-5057-4c9c-9890-8136549917b1','PENDIENTE','2025-11-12 15:57:05',NULL,'Lote generado - 2025-11-12 15:57:05'),(99,'c7871c6f-c9bc-4b67-be6b-55647d7b0d21','PENDIENTE','2025-11-12 15:57:05',NULL,'Lote generado - 2025-11-12 15:57:05'),(100,'087e08dc-9231-440c-9723-fae9c7f7f2b4','PENDIENTE','2025-11-12 15:57:05',NULL,'Lote generado - 2025-11-12 15:57:05'),(101,'7a065b4b-7e91-455c-ae12-7108b311142c','PENDIENTE','2025-11-12 15:57:05',NULL,'Lote generado - 2025-11-12 15:57:05'),(102,'d7389ec5-1e55-4c96-98da-4e22c5e38263','PENDIENTE','2025-11-12 15:57:05',NULL,'Lote generado - 2025-11-12 15:57:05'),(103,'0e069daa-fd92-40b3-8bff-d58d5785723b','PENDIENTE','2025-11-12 15:57:05',NULL,'Lote generado - 2025-11-12 15:57:05'),(104,'6e89250e-7beb-4d81-a92a-5efad0b2ff40','PENDIENTE','2025-11-12 15:57:05',NULL,'Lote generado - 2025-11-12 15:57:05'),(105,'83e66108-5288-4b53-b0ed-46901c74ae62','PENDIENTE','2025-11-12 15:57:05',NULL,'Lote generado - 2025-11-12 15:57:05'),(106,'f899f5a2-7464-4246-83b6-6229b261bcdb','PENDIENTE','2025-11-12 16:02:02',NULL,'Lote generado - 2025-11-12 16:02:02'),(107,'01d8f116-a4cd-4f4a-9661-7b038236dbf1','PENDIENTE','2025-11-12 16:02:02',NULL,'Lote generado - 2025-11-12 16:02:02'),(108,'faba72d3-4a88-4ab5-b2ea-b3607c7e661e','PENDIENTE','2025-11-12 16:02:02',NULL,'Lote generado - 2025-11-12 16:02:02'),(109,'0264b64e-21af-44cf-8844-d6549cbff5fe','PENDIENTE','2025-11-12 16:02:02',NULL,'Lote generado - 2025-11-12 16:02:02'),(110,'90ff5d56-3410-451c-a9f5-7b86d3fc9c53','PENDIENTE','2025-11-12 16:02:02',NULL,'Lote generado - 2025-11-12 16:02:02'),(111,'4a22b8ee-7aa0-43b5-977c-3fc4d237069f','PENDIENTE','2025-11-12 16:02:02',NULL,'Lote generado - 2025-11-12 16:02:02'),(112,'469728ac-f65a-4749-afba-70f7ea18d726','PENDIENTE','2025-11-12 16:02:02',NULL,'Lote generado - 2025-11-12 16:02:02'),(113,'3981777e-c519-41a0-87fa-c57da21ec689','HABILITADO','2025-11-12 16:02:02','2025-11-12 17:25:26','Lote generado - 2025-11-12 16:02:02'),(114,'eacde89f-d9dc-4748-8eda-54ec3bb131fb','PENDIENTE','2025-11-12 16:02:02',NULL,'Lote generado - 2025-11-12 16:02:02'),(115,'f6ecab27-9912-4c7c-9897-ca05dd708817','PENDIENTE','2025-11-12 16:02:02',NULL,'Lote generado - 2025-11-12 16:02:02'),(116,'c4a3b110-4e14-47f1-8468-4c49ac3c6b16','PENDIENTE','2025-11-14 15:57:08',NULL,'Lote generado - 2025-11-14 15:57:08'),(117,'59a7c8ec-9a8d-48d3-a26d-8abf15f55f24','PENDIENTE','2025-11-14 15:57:08',NULL,'Lote generado - 2025-11-14 15:57:08'),(118,'a94ff066-cc5c-469e-a119-25747c46ef0a','PENDIENTE','2025-11-14 15:57:08',NULL,'Lote generado - 2025-11-14 15:57:08'),(119,'451b0010-954e-4e0d-a733-65e61a508a68','PENDIENTE','2025-11-14 15:57:08',NULL,'Lote generado - 2025-11-14 15:57:08'),(120,'710f154e-70c5-48dd-a0a0-1ba6c1588a45','PENDIENTE','2025-11-14 15:57:08',NULL,'Lote generado - 2025-11-14 15:57:08'),(121,'bbbf56b5-a4c4-49dc-b5ff-c34c259478c7','PENDIENTE','2025-11-14 15:57:08',NULL,'Lote generado - 2025-11-14 15:57:08'),(122,'fdfae25b-5e69-4050-a03b-b087d80c3715','PENDIENTE','2025-11-14 15:57:08',NULL,'Lote generado - 2025-11-14 15:57:08'),(123,'36d21a54-0a55-4295-9cee-676ad499a2e4','PENDIENTE','2025-11-14 15:57:08',NULL,'Lote generado - 2025-11-14 15:57:08'),(124,'b6eee84a-64d8-4ac6-b42b-b839f6c9ef23','PENDIENTE','2025-11-14 15:57:08',NULL,'Lote generado - 2025-11-14 15:57:08'),(125,'c21ef24a-d42a-4c92-b288-36edc9870755','PENDIENTE','2025-11-14 15:57:08',NULL,'Lote generado - 2025-11-14 15:57:08'),(126,'624ef3e9-6ab2-4387-abe8-0d26f668cb15','PENDIENTE','2026-04-04 19:40:00',NULL,'Lote generado - 2026-04-04 19:40:00');
/*!40000 ALTER TABLE `qr_codigos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qr_logs`
--

DROP TABLE IF EXISTS `qr_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qr_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qr_codigo_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned DEFAULT NULL COMMENT 'NULL si no es inspector/admin',
  `tipo` enum('REGISTRO_INICIAL','CONSULTA_PUBLICA','CONSULTA_INSPECTOR','INTENTO_SIN_GPS') NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL COMMENT 'Latitud',
  `lon` decimal(10,7) DEFAULT NULL COMMENT 'Longitud',
  `gps_accuracy_m` float DEFAULT NULL COMMENT 'Precisi├│n en metros',
  `fecha_evento` datetime NOT NULL DEFAULT current_timestamp(),
  `hora_evento` time GENERATED ALWAYS AS (cast(`fecha_evento` as time)) STORED COMMENT 'Hora del escaneo (para detectar horarios sospechosos)',
  PRIMARY KEY (`id`),
  KEY `fk_qr_logs_usuarios` (`usuario_id`),
  KEY `idx_qr_codigo` (`qr_codigo_id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_fecha` (`fecha_evento`),
  KEY `idx_hora` (`hora_evento`),
  KEY `idx_gps` (`lat`,`lon`),
  CONSTRAINT `fk_qr_logs_qr_codigos` FOREIGN KEY (`qr_codigo_id`) REFERENCES `qr_codigos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qr_logs_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `qr_usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de todos los escaneos de QR con GPS (excepto registro inicial)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qr_logs`
--

LOCK TABLES `qr_logs` WRITE;
/*!40000 ALTER TABLE `qr_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `qr_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qr_registros`
--

DROP TABLE IF EXISTS `qr_registros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qr_registros` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `qr_codigo_id` int(10) unsigned NOT NULL COMMENT 'Relaci├│n 1:1 con qr_codigos',
  `correo_funcionario` varchar(255) NOT NULL COMMENT 'Correo @municipalidadarica.cl',
  `codigo_confirmacion` varchar(6) DEFAULT NULL COMMENT 'C├│digo de 6 d├¡gitos enviado por email',
  `codigo_confirmacion_expira` datetime DEFAULT NULL COMMENT 'Expiraci├│n del c├│digo (30-60 min)',
  `correo_confirmado` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=No confirmado, 1=Confirmado',
  `fecha_confirmacion` datetime DEFAULT NULL,
  `nombres` varchar(150) NOT NULL DEFAULT '' COMMENT 'Nombres del funcionario',
  `apellidos` varchar(150) NOT NULL DEFAULT '' COMMENT 'Apellidos del funcionario',
  `rut` varchar(20) DEFAULT NULL COMMENT 'RUT chileno formato XX.XXX.XXX-X',
  `unidad` varchar(255) DEFAULT NULL COMMENT 'Direcci├│n/Departamento municipal',
  `cargo` varchar(100) DEFAULT NULL,
  `celular` varchar(20) NOT NULL DEFAULT '' COMMENT 'Tel├®fono celular (obligatorio)',
  `anexo` varchar(20) DEFAULT NULL COMMENT 'Anexo telef├│nico (opcional)',
  `patente` varchar(10) DEFAULT NULL COMMENT 'Patente del veh├¡culo actual',
  `observaciones` text DEFAULT NULL COMMENT 'Observaciones adicionales',
  `fecha_registro` datetime DEFAULT NULL COMMENT 'Primera vez que se guardaron datos',
  `fecha_actualizacion` datetime DEFAULT NULL COMMENT '├Ültima actualizaci├│n de datos',
  `creado_por_ip` varchar(45) DEFAULT NULL,
  `actualizado_por_ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qr_codigo_id` (`qr_codigo_id`),
  KEY `idx_correo` (`correo_funcionario`),
  KEY `idx_rut` (`rut`),
  KEY `idx_patente` (`patente`),
  KEY `idx_nombres_apellidos` (`nombres`,`apellidos`),
  CONSTRAINT `fk_qr_registros_qr_codigos` FOREIGN KEY (`qr_codigo_id`) REFERENCES `qr_codigos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registros de funcionarios y veh├¡culos asociados a QR';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qr_registros`
--

LOCK TABLES `qr_registros` WRITE;
/*!40000 ALTER TABLE `qr_registros` DISABLE KEYS */;
INSERT INTO `qr_registros` VALUES (1,113,'jonathan.zepeda@municipalidadarica.cl',NULL,NULL,1,'2025-11-12 17:25:26','Jonathan','Zepeda','167701353','DOM','Informatico','+56950023383','','HTTC95','Vehículo modelo veloster color verde.','2025-11-12 17:25:26','2025-11-12 17:25:26','127.0.0.1','127.0.0.1');
/*!40000 ALTER TABLE `qr_registros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qr_registros_historial`
--

DROP TABLE IF EXISTS `qr_registros_historial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qr_registros_historial` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qr_registro_id` int(10) unsigned NOT NULL,
  `quien_correo` varchar(255) NOT NULL COMMENT 'Correo del funcionario o admin',
  `accion` enum('CREAR','EDITAR','RESET_QR','BORRAR') NOT NULL,
  `cambios_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Diff de cambios: {"campo": ["valor_anterior", "valor_nuevo"]}' CHECK (json_valid(`cambios_json`)),
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `fecha_evento` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_registro` (`qr_registro_id`),
  KEY `idx_fecha` (`fecha_evento`),
  CONSTRAINT `fk_hist_qr_registro` FOREIGN KEY (`qr_registro_id`) REFERENCES `qr_registros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de cambios en registros (audit trail)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qr_registros_historial`
--

LOCK TABLES `qr_registros_historial` WRITE;
/*!40000 ALTER TABLE `qr_registros_historial` DISABLE KEYS */;
INSERT INTO `qr_registros_historial` VALUES (1,1,'admin','CREAR','{\"mensaje\":\"Registro creado por administrador\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:145.0) Gecko/20100101 Firefox/145.0','2025-11-12 17:25:26');
/*!40000 ALTER TABLE `qr_registros_historial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qr_usuarios`
--

DROP TABLE IF EXISTS `qr_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qr_usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL COMMENT 'Correo @municipalidadarica.cl',
  `password_hash` varchar(255) NOT NULL COMMENT 'Hash bcrypt de la contrase├▒a',
  `rol` enum('ADMIN','INSPECTOR') NOT NULL DEFAULT 'INSPECTOR',
  `activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=Inactivo, 1=Activo',
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`),
  KEY `idx_correo` (`correo`),
  KEY `idx_rol` (`rol`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema (inspectores y administradores)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qr_usuarios`
--

LOCK TABLES `qr_usuarios` WRITE;
/*!40000 ALTER TABLE `qr_usuarios` DISABLE KEYS */;
INSERT INTO `qr_usuarios` VALUES (1,'Administrador Sistema','admin@municipalidadarica.cl','$2y$10$uouZkBW85nyoihNXsLnDd.SeyTfhqoBvLFZEPbT4I83/NzIz7Z2ia','ADMIN',1,'2025-11-11 18:22:56',NULL),(2,'Juan Inspector','inspector@municipalidadarica.cl','$2y$10$lABYpc1PwvBeMl09mCrPreu6RfpgIz9hQ5cldo9m1ZeXjajJsstci','INSPECTOR',0,'2025-11-11 18:22:56','2026-04-04 19:52:14'),(3,'pedro pascal','pedro@municipalidadarica.cl','$2y$10$BUgdgCPZotecAr8IxjFJcOdnT7W1TyGioYRZEuOELpKsR/AbxoyvW','INSPECTOR',0,'2025-11-17 15:20:51','2026-04-04 19:52:57'),(4,'Administrador APROTEC','admin@aprotec.cl','$2y$10$uouZkBW85nyoihNXsLnDd.SeyTfhqoBvLFZEPbT4I83/NzIz7Z2ia','ADMIN',1,'2026-03-04 19:47:36',NULL);
/*!40000 ALTER TABLE `qr_usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','editor','author') DEFAULT 'author',
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','admin@castorpollux.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Administrator','admin',NULL,1,'2025-11-10 01:46:02','2025-11-10 01:46:02');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `setting_type` enum('text','textarea','image','url','email','number') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'site_title','Castror & Pollux - Aricas Astronomy Community','text','Título principal del sitio','2025-11-10 01:46:02','2025-11-10 01:46:02'),(2,'hero_title','A Cosmic Odyssey','text','Título de la página principal','2025-11-10 01:46:02','2025-11-10 01:46:02'),(3,'hero_subtitle','Unveiling the wonders of the Atacama sky from Arica. We are explorers, dreamers, and astronomers charting the final frontier.','textarea','Subtítulo de la página principal','2025-11-10 01:46:02','2025-11-10 01:46:02'),(4,'site_description','Welcome to Castror & Pollux, Aricas premier astronomy community. We are a passionate group of stargazers, photographers, and science enthusiasts dedicated to exploring the night sky.','textarea','Descripción del sitio','2025-11-10 01:46:02','2025-11-10 01:46:02'),(5,'members_count','247+','text','Número de miembros','2025-11-10 01:46:02','2025-11-10 01:46:02'),(6,'observation_hours','1,200+','text','Horas de observación','2025-11-10 01:46:02','2025-11-10 01:46:02'),(7,'discoveries_count','83','text','Número de descubrimientos','2025-11-10 01:46:02','2025-11-10 01:46:02'),(8,'annual_events','15','text','Eventos anuales','2025-11-10 01:46:02','2025-11-10 01:46:02');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `source_url` varchar(500) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `featured` tinyint(1) DEFAULT 0,
  `author_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `featured` tinyint(1) DEFAULT 0,
  `author_id` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-05  0:24:45
