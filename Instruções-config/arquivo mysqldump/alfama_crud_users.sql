-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: alfama_crud
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `google_id` varchar(255) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expira` datetime DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `imagem_path` varchar(255) DEFAULT NULL COMMENT 'Caminho da imagem no sistema de arquivos',
  `imagem_blob` longblob DEFAULT NULL COMMENT 'Dados binários da imagem (backup)',
  `imagem_type` varchar(50) DEFAULT NULL COMMENT 'Tipo MIME da imagem',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'joao teste','joaoteste@teste.com','Joao@0450','79999093689','04784336540','Alfama Web','Rua teste','2025-04-29 02:19:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'joao teste','cosmesena@teste.com','$2y$10$F1Md7i7HtMcymJFRw638uu25/WX94ul.Z73gFyx8IBgP6VPY2NhOa',NULL,NULL,NULL,NULL,'2025-04-29 04:00:30',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'Joao cosme sen sa','cosmelito@teste.com','$2y$10$VIIwvkl7YPJe/3Tij38HuuGOACfrzY7tJjYrkyXgnd0sC06Ht3Vqy','7992301121','99784226500','Alfama Web','rua dos bobos','2025-04-29 04:50:41',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'alfama teste ','joaoteste@alfama.com.br','$2y$10$5i2Hz9863GWwWokzNm.jhOeIkLE8HL0vjcx3REbMrlo7C/2m9AfM.',NULL,NULL,NULL,NULL,'2025-04-29 13:12:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'João Cosme Sena','joaocss@dcomp.ufs.br','','7999829812211','12333221233','Alfamasasww','asdasd','2025-04-29 13:20:49','103656366348954281828',NULL,NULL,NULL,NULL,NULL,NULL),(6,'Thauanny Nunes Sá','psi.thauanny.ns@gmail.com','','79333554477','11111122222','atualizar','Avenida Aparecida do Rio Negro','2025-04-29 13:21:34','108078015732261102298',NULL,NULL,NULL,NULL,NULL,NULL),(7,'João Sena peixa','joao.sena.ufs@gmail.com','','0799990908','04784006540','teste joao','LIv','2025-04-29 13:22:54','107525084683723318166',NULL,NULL,NULL,NULL,NULL,NULL),(8,'carlos be','carlos@teste.com','$2y$10$gT2Udfgo2z4DO7JXHZIiJ.kg807INJ6l0.cBFDHXgqdfqBlGUMj2C',NULL,NULL,NULL,NULL,'2025-04-29 14:07:28',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'pedro cesar','pedro@teste.com','$2y$10$jtlX95bfcJnkqa5rFjQ8Xu2.Va4mg51xkbkkRwEK2zg0gdYd2BPIm',NULL,NULL,NULL,NULL,'2025-04-30 01:04:31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'cosme teste br','cosme@teste.com.br','$2y$10$7.pm9iXB/1ZwZzNksDE3vOUHtbVoIkavoS2MXkIiaDYgpLf.oxIV.','72123123123','1243454446554','qdsfsdf','sdfsdfs','2025-04-30 01:47:09',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'telefone','sena@teste.com','$2y$10$qgYB/7RO9N4S3jPuWf6Xwuaji0CtHE/uzJAzPZUmTuWzHSXQYPeKu','79900117722','02000002030','tlanta','tarde da noite','2025-04-30 04:32:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'MARIA LUIZA LIMA CARVALHO','maria@teste.com','$2y$10$nej5g1.91VFsc18VmAVgEeC7rMu2yI9TbnxMos6Gb77y9RqroHJWi','79911111113','01019929972','maria da luz','rua da minha casa','2025-04-30 11:46:01',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'Joao Sena Sá','cosmelito.sena@gmail.com','','7933355488','1118887773','Alfamab','rua x','2025-04-30 11:51:06','103620009538344811287',NULL,NULL,NULL,NULL,NULL,NULL),(14,'email teste','suping8352@uorak.com','$2y$10$ZrCVtvX/.4fIAmdRCw10xOCCQKWGqQINITQBvFtAkbFdrwsNg1VNC',NULL,NULL,NULL,NULL,'2025-04-30 15:00:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,'victor teste','alayn1516@uorak.com','$2y$10$qc0z9Nsk.OO6bEDDGPgYhONr52OkPZGPnIz5dY7t/QNbMD.4ox86K','7999909232','31183464634','Ele diz que é bad','Rua Bela Vista','2025-04-30 20:05:11',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,'Foto perfil','testealfama@uorak.com','$2y$10$a9w4WDVo723KnSxJcHKgmuPYakJIWrEG9AasZQUnX9Cpb30V9C10q','11992882239','000222983764','foto perfil teste','rua d','2025-05-01 11:17:34',NULL,NULL,NULL,'../imagens/perfil.svg',NULL,NULL,NULL),(17,'tste dupl','yaisa5058@uorak.com','$2y$10$LSN1NhA6xgj4zMPPgNwcS.izjZhJplwndPagD0AVVKLJeKT6osraW','711109022','11111122222','atualizasaas','rua ls','2025-05-01 11:37:59',NULL,NULL,NULL,'../imagens/perfil.svg',NULL,NULL,NULL),(18,'cosme sena','ultimoteste@uorak.com','$2y$10$xyGbKauJ3TsifTIDnAs/UupP09xdK0w0zPBPbPB.D8Fh2F5XNsq2q',NULL,NULL,NULL,NULL,'2025-05-01 23:23:36',NULL,NULL,NULL,'../imagens/perfil.svg',NULL,NULL,NULL),(19,'Joao cosme ','cosmes@cvcrm.com.br','$2y$10$FsHb4Rf/LKku0xzfIRdyWO0w560w4o4/tttrg28TVOsfD7FoawDG6',NULL,NULL,NULL,NULL,'2025-05-02 01:21:04',NULL,NULL,NULL,'../imagens/perfil.svg',NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-01 22:57:21
