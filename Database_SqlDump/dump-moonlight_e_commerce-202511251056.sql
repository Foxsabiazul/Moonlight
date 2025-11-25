-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: moonlight_e_commerce
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auditoriapreco`
--

DROP TABLE IF EXISTS `auditoriapreco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auditoriapreco` (
  `id_audipreco` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_games` bigint(20) NOT NULL,
  `preco_antigo` decimal(10,2) NOT NULL,
  `preco_novo` decimal(10,2) NOT NULL,
  `data_alteracao` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_alteracao` varchar(255) DEFAULT NULL COMMENT 'Usuario que alterou',
  PRIMARY KEY (`id_audipreco`),
  KEY `fk_auditoria_games` (`id_games`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `biblioteca`
--

DROP TABLE IF EXISTS `biblioteca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `biblioteca` (
  `id_user` bigint(20) NOT NULL COMMENT 'Dono do jogo',
  `id_games` bigint(20) NOT NULL COMMENT 'Jogo adquirido',
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Quando foi liberado na conta ou data de acesso',
  PRIMARY KEY (`id_user`,`id_games`),
  KEY `fk_biblioteca_jogo` (`id_games`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` bigint(20) NOT NULL AUTO_INCREMENT,
  `nm_cat` varchar(255) NOT NULL,
  `desc_cat` varchar(255) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favoritos` (
  `id_user` bigint(20) NOT NULL,
  `id_games` bigint(20) NOT NULL,
  `fav_star` enum('1','2','3','4','5','indefinido') NOT NULL DEFAULT 'indefinido',
  PRIMARY KEY (`id_user`,`id_games`),
  KEY `fk_favoritos_jogo` (`id_games`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itenscompra`
--

DROP TABLE IF EXISTS `itenscompra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itenscompra` (
  `id_pedido` bigint(20) NOT NULL COMMENT 'Qual compra',
  `id_games` bigint(20) NOT NULL COMMENT 'Qual jogo foi comprado',
  `preco` decimal(10,2) NOT NULL COMMENT 'Preço do jogo na data da compra',
  PRIMARY KEY (`id_pedido`,`id_games`),
  KEY `idx_itemcompra_pedido` (`id_pedido`),
  KEY `idx_itemcompra_games` (`id_games`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jogos`
--

DROP TABLE IF EXISTS `jogos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jogos` (
  `id_games` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_categoria` bigint(20) NOT NULL COMMENT 'Id de categoria',
  `titulo` varchar(255) NOT NULL COMMENT 'Nome do jogo',
  `descricao` varchar(255) DEFAULT NULL COMMENT 'Texto descritivo',
  `preco` decimal(10,2) NOT NULL COMMENT 'Preço do jogo',
  `imagem` varchar(255) DEFAULT NULL COMMENT 'Caminho/URL da capa',
  `link` varchar(255) DEFAULT NULL COMMENT 'Caminho/URL do arquivo (ou null)',
  `data_lancamento` date NOT NULL COMMENT 'Data de lançamento',
  `ativo` enum('S','N') NOT NULL,
  PRIMARY KEY (`id_games`),
  KEY `fk_jogos_categoria` (`id_categoria`),
  KEY `idx_jogos_titulo` (`titulo`),
  KEY `idx_jogos_preco` (`preco`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_auditar_preco_jogo`
AFTER UPDATE ON `Jogos` 
FOR EACH ROW
BEGIN
    -- Verifica se a coluna 'preco' foi alterada
    IF OLD.preco <> NEW.preco THEN
        INSERT INTO `AuditoriaPreco` (
            `id_games`,         
            `preco_antigo`,
            `preco_novo`,
            `usuario_alteracao`
        )
        VALUES (
            NEW.id_games,       
            OLD.preco,
            NEW.preco,
            COALESCE(@usuario_logado, USER())
        );
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `id_pedido` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) NOT NULL COMMENT 'Quem comprou',
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Data da compra',
  `total` decimal(10,2) NOT NULL COMMENT 'Valor total para o usuário',
  `status` enum('pendente','aprovado','reembolsado','cancelado') NOT NULL DEFAULT 'pendente',
  `preference_id` varchar(50) NOT NULL COMMENT 'id do mercado pago',
  PRIMARY KEY (`id_pedido`),
  KEY `fk_pedidos_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuários`
--

DROP TABLE IF EXISTS `usuários`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuários` (
  `id_user` bigint(20) NOT NULL AUTO_INCREMENT,
  `nm_user` varchar(255) NOT NULL COMMENT 'Nome do usuário',
  `email` varchar(150) NOT NULL COMMENT 'Único (login)',
  `senha` varchar(255) NOT NULL COMMENT 'Hash da senha',
  `data_criacao` datetime NOT NULL COMMENT 'Quando foi criado',
  `tipo` enum('cliente','admin') NOT NULL DEFAULT 'cliente',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'moonlight_e_commerce'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-25 10:56:50
