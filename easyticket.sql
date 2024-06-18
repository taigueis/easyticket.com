-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15-Jun-2024 às 14:34
-- Versão do servidor: 10.4.28-MariaDB
-- versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `easyticket`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `autocarro`
--

CREATE TABLE `autocarro` (
  `id_autocarro` int(11) NOT NULL,
  `capacidade_autocarro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Extraindo dados da tabela `autocarro`
--

INSERT INTO `autocarro` (`id_autocarro`, `capacidade_autocarro`) VALUES
(125, 42),
(136, 64),
(217, 53),
(222, 41),
(345, 30),
(555, 40),
(648, 65),
(773, 52);

-- --------------------------------------------------------

--
-- Estrutura da tabela `bilhetes`
--

CREATE TABLE `bilhetes` (
  `id_bilhete` int(11) NOT NULL,
  `linha_autocarro` int(4) NOT NULL,
  `preco_bilhete` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Extraindo dados da tabela `bilhetes`
--

INSERT INTO `bilhetes` (`id_bilhete`, `linha_autocarro`, `preco_bilhete`) VALUES
(1, 3001, 2.5),
(2, 3002, 2.4),
(3, 3301, 2.3),
(4, 3302, 2.2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `compra_bilhete`
--

CREATE TABLE `compra_bilhete` (
  `id_compra` int(11) NOT NULL,
  `id_bilhete` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `data_compra` datetime NOT NULL,
  `metodo_pagamento` set('MBWAY','Paypal','Payshop','Cartão de Crédito') NOT NULL,
  `status_pagamento` set('Comprado','Cancelado') NOT NULL DEFAULT 'Comprado',
  `preco_compra` double NOT NULL,
  `quantidade_bilhetes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Extraindo dados da tabela `compra_bilhete`
--

INSERT INTO `compra_bilhete` (`id_compra`, `id_bilhete`, `id_utilizador`, `data_compra`, `metodo_pagamento`, `status_pagamento`, `preco_compra`, `quantidade_bilhetes`) VALUES
(70, 1, 18, '2024-05-21 13:52:04', '', 'Comprado', 10, 4),
(71, 1, 18, '2024-05-21 13:52:24', '', 'Comprado', 2.5, 1),
(72, 1, 18, '2024-05-21 14:41:46', '', 'Comprado', 5, 2),
(73, 2, 18, '2024-05-21 14:53:13', '', 'Comprado', 7.2, 3),
(74, 3, 18, '2024-05-21 14:53:17', '', 'Comprado', 11.5, 5),
(76, 1, 18, '2024-05-21 16:50:53', '', 'Comprado', 5, 2),
(77, 1, 39, '2024-05-21 16:59:46', '', 'Comprado', 7.5, 3),
(78, 2, 39, '2024-05-21 17:00:04', '', 'Comprado', 7.2, 3),
(79, 3, 39, '2024-05-21 17:00:09', '', 'Comprado', 2.3, 1),
(81, 3, 39, '2024-05-21 17:10:50', '', 'Comprado', 2.3, 1),
(82, 1, 40, '2024-05-21 17:16:30', '', 'Comprado', 5, 2),
(83, 2, 40, '2024-05-21 17:16:35', '', 'Comprado', 2.4, 1),
(84, 3, 40, '2024-05-21 17:16:38', '', 'Comprado', 2.3, 1),
(88, 3, 18, '2024-05-23 11:50:57', '', 'Comprado', 9.2, 4),
(91, 1, 18, '2024-06-14 15:28:45', '', 'Comprado', 25, 10);

-- --------------------------------------------------------

--
-- Estrutura da tabela `depositos_utilizador`
--

CREATE TABLE `depositos_utilizador` (
  `id_deposito` int(11) NOT NULL,
  `id_utilizador` int(11) NOT NULL,
  `nome_titular_cartao` varchar(100) NOT NULL,
  `numero_cartao` varchar(20) NOT NULL,
  `data_validade_cartao` varchar(5) NOT NULL,
  `cvv` varchar(4) NOT NULL,
  `valor_depositado` decimal(10,2) NOT NULL,
  `data_deposito` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Extraindo dados da tabela `depositos_utilizador`
--

INSERT INTO `depositos_utilizador` (`id_deposito`, `id_utilizador`, `nome_titular_cartao`, `numero_cartao`, `data_validade_cartao`, `cvv`, `valor_depositado`, `data_deposito`) VALUES
(19, 18, 'Tiago', '1231 2312 3211 2312', '07/27', '123', 1.00, '2024-06-14 11:45:43');

-- --------------------------------------------------------

--
-- Estrutura da tabela `totalbilhetesporlinha`
--

CREATE TABLE `totalbilhetesporlinha` (
  `id_utilizador` int(11) NOT NULL,
  `id_bilhete` int(11) NOT NULL,
  `total_bilhetes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Extraindo dados da tabela `totalbilhetesporlinha`
--

INSERT INTO `totalbilhetesporlinha` (`id_utilizador`, `id_bilhete`, `total_bilhetes`) VALUES
(18, 1, 15),
(18, 2, 2),
(18, 3, 5),
(39, 1, 2),
(39, 2, 3),
(39, 3, 0),
(40, 1, 2),
(40, 2, 1),
(40, 3, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id_utilizador` int(11) NOT NULL,
  `nome_utilizador` varchar(50) NOT NULL,
  `tipo_utilizador` set('admin','user','semregisto') NOT NULL,
  `email_utilizador` varchar(50) NOT NULL,
  `password_utilizador` varchar(16) NOT NULL,
  `contacto_utilizador` int(9) NOT NULL,
  `data_nasc` date NOT NULL,
  `ativo` set('1','0') NOT NULL,
  `saldo` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id_utilizador`, `nome_utilizador`, `tipo_utilizador`, `email_utilizador`, `password_utilizador`, `contacto_utilizador`, `data_nasc`, `ativo`, `saldo`) VALUES
(0, 'semregisto', 'semregisto', '', 'semregisto', 0, '0000-00-00', '1', 0.00),
(2, 'tiago', 'admin', 'tiago@gmail.com', 'tiago2006', 928098706, '2006-04-13', '1', 0.00),
(3, 'diogo', 'user', 'diogogomes@gmail.com', 'dioguinho', 932995961, '2005-07-15', '0', 0.00),
(4, 'filipe', 'user', 'filipecarvalho@hotmail.com', 'filipecarvalho17', 952076167, '2004-10-22', '0', 0.00),
(11, 'peazyys', 'user', 'peazyys@gmail.com', 'peazyys1234', 936075743, '2005-03-02', '1', 0.00),
(15, 'leituga7', 'user', 'pedroleituga@gmail.com', '13tct2006', 923123123, '2005-11-12', '1', 0.00),
(16, 'felicci', 'user', 'tiagoalves1962@gmail.com', 'feliccigoatcr7', 949853458, '1962-08-13', '1', 0.00),
(18, 'costinha', 'user', 'tiago2006@gmail.com', 'tiago1304', 913278387, '2006-04-13', '1', 128.48),
(19, 'lofri', 'user', 'lofreta_sp@gmail.com', 'lofris2007', 957150234, '2005-07-10', '1', 0.00),
(20, 'tns', 'user', 'diogonuno@gmail.com', 'tns162006', 923741632, '2006-02-13', '1', 0.00),
(21, 'tiaguinho', 'user', 'tiaguinho@gmail.com', 'tiago1312', 977667567, '2005-04-20', '0', 0.00),
(22, 'manel', 'user', 'baganhaprior@gmail.com', 'baganhaog05', 948578934, '2005-12-11', '0', 0.00),
(23, 'nando', 'user', 'nandofernandes@gmail.com', 'nandinho123', 965432167, '2004-01-22', '1', 0.00),
(24, 'pedro', 'user', 'pedrojesus@gmail.com', 'peter2006', 987345634, '2006-01-10', '0', 0.00),
(25, 'kpop_guy', 'user', 'kpophomem@gmail.com', 'fortnitemorte', 935426624, '2003-09-11', '1', 0.00),
(26, 'martim', 'user', 'martim@gmail.com', 'martim2005', 974372463, '2005-06-08', '0', 0.00),
(27, 'luis', 'user', 'luis@gmail.com', 'luisgaspar', 987654328, '2006-03-11', '1', 0.00),
(28, 'pedro06', 'user', 'pedro@gmail.com', 'pedro2006', 937289635, '2006-03-15', '1', 0.00),
(29, 'bintecinco', 'user', 'bintecinco@gmail.com', 'vintcinq025', 926158174, '2006-11-15', '0', 0.00),
(30, 'ribasgc', 'user', 'ribasgc2006@gmail.com', 'ribas2006', 983287382, '2005-08-29', '1', 0.00),
(32, 'ronaldo7', 'admin', 'ronaldo@gmail.com', 'ronaldo07', 983727372, '1997-03-18', '1', 0.00),
(33, 'lara', 'user', 'lara2006@gmail.com', 'lara2006', 983823727, '2006-06-13', '1', 0.00),
(34, 'taigaz', 'user', 'taigaz@gmail.com', 'taigaz2006', 983823231, '2006-03-20', '1', 0.00),
(35, 'taigueis', 'user', 'taigueis@gmail.com', 'taigueis2006', 983283283, '2005-03-22', '1', 0.00),
(39, 'carlos', 'user', 'carlosferreira@gmail.com', 'carlos2005', 999999999, '2014-05-21', '1', 0.60),
(40, 'dani', 'user', 'dani@gmail.com', 'dani2005', 976575747, '2014-05-21', '1', 0.30),
(41, 'vasco', 'user', 'vasco@gmail.com', 'vasco017', 983928283, '2014-05-21', '1', 0.00),
(42, 'little', 'user', 'littletostas@gmail.com', 'tostinhas06', 997686577, '2014-06-14', '1', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `viagem`
--

CREATE TABLE `viagem` (
  `id_viagem` int(11) NOT NULL,
  `id_autocarro` int(11) NOT NULL,
  `linha_autocarro` int(4) NOT NULL,
  `sentido` set('ida','volta') NOT NULL DEFAULT 'ida',
  `origem` varchar(40) NOT NULL,
  `destino` varchar(40) NOT NULL,
  `hora_partida` time NOT NULL,
  `hora_chegada` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Extraindo dados da tabela `viagem`
--

INSERT INTO `viagem` (`id_viagem`, `id_autocarro`, `linha_autocarro`, `sentido`, `origem`, `destino`, `hora_partida`, `hora_chegada`) VALUES
(3, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '07:00:00', '07:30:00'),
(4, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '07:30:00', '08:00:00'),
(5, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '08:00:00', '08:30:00'),
(6, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '08:30:00', '09:00:00'),
(7, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '09:30:00', '10:00:00'),
(8, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '10:00:00', '10:30:00'),
(9, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '10:30:00', '11:00:00'),
(10, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '11:00:00', '11:30:00'),
(11, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '11:30:00', '12:00:00'),
(12, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '13:30:00', '14:00:00'),
(14, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '15:00:00', '15:30:00'),
(15, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '15:30:00', '16:00:00'),
(16, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '16:00:00', '16:30:00'),
(17, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '12:30:00', '13:00:00'),
(19, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '14:30:00', '15:00:00'),
(22, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '12:00:00', '12:30:00'),
(23, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '13:00:00', '13:30:00'),
(24, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '06:00:00', '06:30:00'),
(25, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '16:30:00', '17:00:00'),
(27, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '17:30:00', '18:00:00'),
(28, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '17:00:00', '17:30:00'),
(29, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '18:00:00', '18:30:00'),
(30, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '18:30:00', '19:00:00'),
(31, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '19:00:00', '19:30:00'),
(32, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '19:30:00', '20:00:00'),
(33, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '20:30:00', '21:00:00'),
(34, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '20:00:00', '20:30:00'),
(35, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '21:00:00', '21:30:00'),
(36, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '21:30:00', '22:00:00'),
(37, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '22:00:00', '22:30:00'),
(38, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '22:30:00', '23:00:00'),
(39, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '09:00:00', '09:30:00'),
(40, 125, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '14:00:00', '14:30:00'),
(41, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '06:30:00', '07:00:00'),
(43, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '07:30:00', '08:00:00'),
(44, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '08:00:00', '08:30:00'),
(45, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '08:30:00', '09:00:00'),
(46, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '09:00:00', '09:30:00'),
(47, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '09:30:00', '10:00:00'),
(48, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '10:00:00', '10:30:00'),
(49, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '10:30:00', '11:00:00'),
(50, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '11:00:00', '11:30:00'),
(51, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '11:30:00', '12:00:00'),
(52, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '12:00:00', '12:30:00'),
(53, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '12:30:00', '13:00:00'),
(54, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '13:00:00', '13:30:00'),
(55, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '13:30:00', '14:00:00'),
(56, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '14:00:00', '14:30:00'),
(58, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '15:00:00', '15:30:00'),
(59, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '15:30:00', '16:00:00'),
(60, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '16:00:00', '16:30:00'),
(61, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '16:30:00', '17:00:00'),
(62, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '17:00:00', '17:30:00'),
(63, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '18:30:00', '19:00:00'),
(64, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '17:30:00', '18:00:00'),
(65, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '18:00:00', '18:30:00'),
(66, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '19:00:00', '19:30:00'),
(67, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '19:30:00', '20:00:00'),
(68, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '20:00:00', '20:30:00'),
(69, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '20:30:00', '21:00:00'),
(70, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '21:00:00', '21:30:00'),
(71, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '21:30:00', '22:00:00'),
(72, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '22:00:00', '22:30:00'),
(74, 136, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '07:00:00', '07:30:00'),
(109, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '06:15:00', '06:45:00'),
(111, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '06:30:00', '07:00:00'),
(112, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '05:00:00', '05:30:00'),
(113, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '05:00:00', '05:30:00'),
(116, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '14:30:00', '15:00:00'),
(122, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '07:15:00', '07:45:00'),
(123, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '07:45:00', '08:15:00'),
(124, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '08:15:00', '08:45:00'),
(125, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '08:45:00', '09:15:00'),
(126, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '09:15:00', '09:45:00'),
(127, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '09:45:00', '10:15:00'),
(129, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '10:45:00', '11:15:00'),
(130, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '11:15:00', '11:45:00'),
(131, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '11:45:00', '12:15:00'),
(132, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '12:15:00', '12:45:00'),
(133, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '12:45:00', '13:15:00'),
(135, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '07:15:00', '07:45:00'),
(136, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '13:15:00', '13:45:00'),
(137, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '13:45:00', '14:15:00'),
(138, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '14:15:00', '14:45:00'),
(146, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '07:45:00', '08:15:00'),
(147, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '14:45:00', '15:15:00'),
(148, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '15:15:00', '15:45:00'),
(149, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '15:45:00', '16:15:00'),
(150, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '16:15:00', '16:45:00'),
(151, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '16:45:00', '17:15:00'),
(152, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '17:15:00', '17:45:00'),
(153, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '17:45:00', '18:15:00'),
(154, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '18:15:00', '18:45:00'),
(155, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '18:45:00', '19:15:00'),
(156, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '19:15:00', '19:45:00'),
(157, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '19:45:00', '20:15:00'),
(158, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '20:45:00', '21:15:00'),
(159, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '20:15:00', '20:45:00'),
(160, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '21:15:00', '21:45:00'),
(161, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '21:45:00', '22:15:00'),
(162, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '22:15:00', '22:45:00'),
(163, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '22:45:00', '23:15:00'),
(164, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '08:15:00', '08:45:00'),
(165, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '08:45:00', '09:15:00'),
(166, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '10:15:00', '10:45:00'),
(167, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '10:15:00', '10:45:00'),
(168, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '09:15:00', '09:45:00'),
(169, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '09:45:00', '10:15:00'),
(170, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '06:45:00', '07:15:00'),
(171, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '10:45:00', '11:15:00'),
(172, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '11:15:00', '11:45:00'),
(173, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '11:45:00', '12:15:00'),
(174, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '12:15:00', '12:45:00'),
(175, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '12:45:00', '13:15:00'),
(176, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '13:15:00', '13:45:00'),
(177, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '13:45:00', '14:15:00'),
(178, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '14:15:00', '14:45:00'),
(179, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '15:15:00', '15:45:00'),
(180, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '14:45:00', '15:15:00'),
(181, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '15:45:00', '16:15:00'),
(182, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '16:15:00', '16:45:00'),
(183, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '16:45:00', '17:15:00'),
(184, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '17:15:00', '17:45:00'),
(185, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '17:45:00', '18:15:00'),
(186, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '18:15:00', '18:45:00'),
(187, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '18:45:00', '19:15:00'),
(188, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '19:15:00', '19:45:00'),
(189, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '19:45:00', '20:15:00'),
(190, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '20:15:00', '20:45:00'),
(191, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '20:45:00', '21:15:00'),
(192, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '21:15:00', '21:45:00'),
(193, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '21:45:00', '22:15:00'),
(194, 222, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '22:15:00', '22:45:00'),
(196, 217, 3002, 'volta', 'Estação Metro Portas Fronhas', 'Torrinha', '22:45:00', '23:15:00'),
(197, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '05:30:00', '06:00:00'),
(198, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '05:30:00', '06:00:00'),
(199, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '06:00:00', '06:30:00'),
(200, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '06:30:00', '07:00:00'),
(201, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '07:00:00', '07:30:00'),
(202, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '07:30:00', '08:00:00'),
(203, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '08:00:00', '08:30:00'),
(204, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '08:30:00', '09:00:00'),
(205, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '09:00:00', '09:30:00'),
(206, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '09:30:00', '10:00:00'),
(207, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '10:00:00', '10:30:00'),
(208, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '10:30:00', '11:00:00'),
(209, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '11:00:00', '11:30:00'),
(210, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '11:30:00', '12:00:00'),
(211, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '12:00:00', '12:30:00'),
(212, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '12:30:00', '13:00:00'),
(213, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '13:00:00', '13:30:00'),
(214, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '13:30:00', '14:00:00'),
(215, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '14:00:00', '14:30:00'),
(216, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '14:30:00', '15:00:00'),
(217, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '15:00:00', '15:30:00'),
(218, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '15:30:00', '16:00:00'),
(219, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '16:00:00', '16:30:00'),
(220, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '16:30:00', '17:00:00'),
(221, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '17:00:00', '17:30:00'),
(222, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '17:30:00', '18:00:00'),
(223, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '18:00:00', '18:30:00'),
(224, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '18:30:00', '19:00:00'),
(225, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '19:00:00', '19:30:00'),
(226, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '19:30:00', '20:00:00'),
(227, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '20:00:00', '20:30:00'),
(228, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '20:30:00', '21:00:00'),
(229, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '21:00:00', '21:30:00'),
(230, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '21:30:00', '22:00:00'),
(231, 555, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '22:00:00', '22:30:00'),
(232, 648, 3301, 'ida', 'Barranha', 'Estação Metro Vila do Conde', '22:30:00', '23:00:00'),
(233, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '06:00:00', '06:30:00'),
(234, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '07:00:00', '07:30:00'),
(235, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '06:30:00', '07:00:00'),
(236, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '07:30:00', '08:00:00'),
(237, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '08:00:00', '08:30:00'),
(238, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '09:00:00', '09:30:00'),
(239, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '10:00:00', '10:30:00'),
(240, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '11:00:00', '11:30:00'),
(241, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '12:00:00', '12:30:00'),
(242, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '13:00:00', '13:30:00'),
(243, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '14:00:00', '14:30:00'),
(244, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '15:00:00', '15:30:00'),
(245, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '16:00:00', '16:30:00'),
(246, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '17:00:00', '17:30:00'),
(247, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '18:00:00', '18:30:00'),
(248, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '19:00:00', '19:30:00'),
(249, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '20:00:00', '20:30:00'),
(250, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '21:00:00', '21:30:00'),
(251, 648, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '22:00:00', '22:30:00'),
(252, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '09:30:00', '10:00:00'),
(253, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '08:30:00', '09:00:00'),
(254, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '10:30:00', '11:00:00'),
(255, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '11:30:00', '12:00:00'),
(256, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '12:30:00', '13:00:00'),
(257, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '13:30:00', '14:00:00'),
(258, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '14:30:00', '15:00:00'),
(259, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '15:30:00', '16:00:00'),
(260, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '16:30:00', '17:00:00'),
(261, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '17:30:00', '18:00:00'),
(262, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '18:30:00', '19:00:00'),
(263, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '19:30:00', '20:00:00'),
(264, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '20:30:00', '21:00:00'),
(265, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '21:30:00', '22:00:00'),
(266, 555, 3301, 'volta', 'Estação Metro Vila do Conde', 'Barranha', '22:30:00', '23:00:00'),
(267, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '07:00:00', '07:30:00'),
(268, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '07:30:00', '08:00:00'),
(269, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '09:00:00', '09:30:00'),
(270, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '08:00:00', '08:30:00'),
(271, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '10:00:00', '10:30:00'),
(272, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '11:00:00', '11:30:00'),
(273, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '12:00:00', '12:30:00'),
(274, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '13:00:00', '13:30:00'),
(275, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '14:00:00', '14:30:00'),
(276, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '15:00:00', '15:30:00'),
(277, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '16:00:00', '16:30:00'),
(278, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '17:00:00', '17:30:00'),
(279, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '18:00:00', '18:30:00'),
(280, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '19:00:00', '19:30:00'),
(281, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '20:00:00', '20:30:00'),
(282, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '21:00:00', '21:30:00'),
(283, 839, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '22:00:00', '22:30:00'),
(284, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '08:30:00', '09:00:00'),
(285, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '09:30:00', '10:00:00'),
(286, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '10:30:00', '11:00:00'),
(287, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '11:30:00', '12:00:00'),
(288, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '12:30:00', '13:00:00'),
(289, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '13:30:00', '14:00:00'),
(290, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '14:30:00', '15:00:00'),
(291, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '15:30:00', '16:00:00'),
(292, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '16:30:00', '17:00:00'),
(293, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '17:30:00', '18:00:00'),
(294, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '18:30:00', '19:00:00'),
(295, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '19:30:00', '20:00:00'),
(296, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '20:30:00', '21:00:00'),
(297, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '21:30:00', '22:00:00'),
(298, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '22:30:00', '23:00:00'),
(299, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '07:00:00', '07:30:00'),
(300, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '07:30:00', '08:00:00'),
(301, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '09:30:00', '10:00:00'),
(302, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '08:30:00', '09:00:00'),
(303, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '10:30:00', '11:00:00'),
(304, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '11:30:00', '12:00:00'),
(305, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '12:30:00', '13:00:00'),
(306, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '13:30:00', '14:00:00'),
(307, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '14:30:00', '15:00:00'),
(308, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '15:30:00', '16:00:00'),
(309, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '16:30:00', '17:00:00'),
(310, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '19:30:00', '20:00:00'),
(311, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '17:30:00', '18:00:00'),
(312, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '18:30:00', '19:00:00'),
(313, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '20:30:00', '21:00:00'),
(314, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '21:30:00', '22:00:00'),
(315, 839, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '22:30:00', '23:00:00'),
(316, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '08:00:00', '08:30:00'),
(317, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '09:00:00', '09:30:00'),
(318, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '10:00:00', '10:30:00'),
(319, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '12:00:00', '12:30:00'),
(320, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '11:00:00', '11:30:00'),
(321, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '13:00:00', '13:30:00'),
(322, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '14:00:00', '14:30:00'),
(323, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '15:00:00', '15:30:00'),
(324, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '16:00:00', '16:30:00'),
(325, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '17:00:00', '17:30:00'),
(326, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '19:00:00', '19:30:00'),
(327, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '20:00:00', '20:30:00'),
(328, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '21:00:00', '21:30:00'),
(329, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '22:00:00', '22:30:00'),
(330, 125, 3001, 'volta', 'Estação Metro Portas Fronhas', 'Rua de Santo António', '22:30:00', '23:00:00'),
(334, 773, 3302, 'ida', 'Zona Industrial de Amorim', 'Areia Tanque', '06:30:00', '07:00:00'),
(335, 773, 3302, 'volta', 'Areia Tanque', 'Zona Industrial de Amorim', '18:00:00', '18:30:00'),
(342, 136, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '06:30:00', '07:00:00'),
(345, 217, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '06:15:00', '06:45:00'),
(346, 222, 3002, 'ida', 'Torrinha', 'Estação Metro Portas Fronhas', '06:45:00', '07:15:00'),
(347, 0, 3001, 'ida', 'Rua de Santo António', 'Estação Metro Portas Fronhas', '06:00:00', '06:30:00');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `autocarro`
--
ALTER TABLE `autocarro`
  ADD PRIMARY KEY (`id_autocarro`);

--
-- Índices para tabela `bilhetes`
--
ALTER TABLE `bilhetes`
  ADD PRIMARY KEY (`id_bilhete`),
  ADD KEY `linha_autocarro` (`linha_autocarro`);

--
-- Índices para tabela `compra_bilhete`
--
ALTER TABLE `compra_bilhete`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `id_bilhete` (`id_bilhete`,`id_utilizador`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `depositos_utilizador`
--
ALTER TABLE `depositos_utilizador`
  ADD PRIMARY KEY (`id_deposito`),
  ADD KEY `id_utilizador` (`id_utilizador`);

--
-- Índices para tabela `totalbilhetesporlinha`
--
ALTER TABLE `totalbilhetesporlinha`
  ADD PRIMARY KEY (`id_utilizador`,`id_bilhete`),
  ADD KEY `id_bilhete` (`id_bilhete`);

--
-- Índices para tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`id_utilizador`);

--
-- Índices para tabela `viagem`
--
ALTER TABLE `viagem`
  ADD PRIMARY KEY (`id_viagem`),
  ADD UNIQUE KEY `id_viagem` (`id_viagem`),
  ADD KEY `id_autocarro` (`id_autocarro`),
  ADD KEY `linha_autocarro` (`linha_autocarro`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `bilhetes`
--
ALTER TABLE `bilhetes`
  MODIFY `id_bilhete` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de tabela `compra_bilhete`
--
ALTER TABLE `compra_bilhete`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de tabela `depositos_utilizador`
--
ALTER TABLE `depositos_utilizador`
  MODIFY `id_deposito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id_utilizador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de tabela `viagem`
--
ALTER TABLE `viagem`
  MODIFY `id_viagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=348;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `compra_bilhete`
--
ALTER TABLE `compra_bilhete`
  ADD CONSTRAINT `compra_bilhete_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`);

--
-- Limitadores para a tabela `depositos_utilizador`
--
ALTER TABLE `depositos_utilizador`
  ADD CONSTRAINT `depositos_utilizador_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`);

--
-- Limitadores para a tabela `totalbilhetesporlinha`
--
ALTER TABLE `totalbilhetesporlinha`
  ADD CONSTRAINT `totalbilhetesporlinha_ibfk_1` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizadores` (`id_utilizador`),
  ADD CONSTRAINT `totalbilhetesporlinha_ibfk_2` FOREIGN KEY (`id_bilhete`) REFERENCES `bilhetes` (`id_bilhete`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
