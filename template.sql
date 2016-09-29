-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 29, 2016 at 04:23 PM
-- Server version: 10.1.17-MariaDB
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `cuentas`
--

CREATE TABLE `cuentas` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `nombre` text NOT NULL,
  `descripcion` text NOT NULL COMMENT 'Descripción de la compra',
  `fecha_compra` date NOT NULL,
  `fecha_facturacion` varchar(7) NOT NULL,
  `monto_original` float NOT NULL,
  `divisa_original` varchar(3) NOT NULL DEFAULT 'CLP',
  `monto` float NOT NULL,
  `num_cuotas` int(2) NOT NULL DEFAULT '0',
  `info` text,
  `pagado` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cuenta_bancaria`
--

CREATE TABLE `cuenta_bancaria` (
  `id` int(10) UNSIGNED NOT NULL,
  `tipo` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `user` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `deudas`
--

CREATE TABLE `deudas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `deudor` int(11) NOT NULL,
  `descripcion` text,
  `monto` float NOT NULL DEFAULT '0',
  `fecha` date NOT NULL,
  `pagada` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `deudores`
--

CREATE TABLE `deudores` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tipo_cuenta_bancaria`
--

CREATE TABLE `tipo_cuenta_bancaria` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tipo_cuenta_bancaria`
--

INSERT INTO `tipo_cuenta_bancaria` (`id`, `nombre`) VALUES
(1, 'Débito'),
(2, 'Crédito');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` varchar(30) NOT NULL,
  `password` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cuentas_usuarios_idx` (`usuario_id`);

--
-- Indexes for table `cuenta_bancaria`
--
ALTER TABLE `cuenta_bancaria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD KEY `fk_cuenta_bancaria_tipo_cuenta_bancaria1_idx` (`tipo`),
  ADD KEY `fk_cuenta_bancaria_usuarios1_idx` (`usuario_id`);

--
-- Indexes for table `deudas`
--
ALTER TABLE `deudas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_deudas_usuarios1_idx` (`usuario_id`),
  ADD KEY `fk_deudas_deudores1_idx` (`deudor`);

--
-- Indexes for table `deudores`
--
ALTER TABLE `deudores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tipo_cuenta_bancaria`
--
ALTER TABLE `tipo_cuenta_bancaria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_user_idx` (`user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cuenta_bancaria`
--
ALTER TABLE `cuenta_bancaria`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tipo_cuenta_bancaria`
--
ALTER TABLE `tipo_cuenta_bancaria`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `cuentas`
--
ALTER TABLE `cuentas`
  ADD CONSTRAINT `fk_cuentas_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `cuenta_bancaria`
--
ALTER TABLE `cuenta_bancaria`
  ADD CONSTRAINT `fk_cuenta_bancaria_tipo_cuenta_bancaria1` FOREIGN KEY (`tipo`) REFERENCES `tipo_cuenta_bancaria` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_cuenta_bancaria_usuarios1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `deudas`
--
ALTER TABLE `deudas`
  ADD CONSTRAINT `fk_deudas_deudores1` FOREIGN KEY (`deudor`) REFERENCES `deudores` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_deudas_usuarios1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
