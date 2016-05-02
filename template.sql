SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de datos: `cuentas_jg`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE `cuentas` (
  `id` int(10) UNSIGNED NOT NULL,
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
-- Estructura de tabla para la tabla `deudas`
--

CREATE TABLE `deudas` (
  `id` int(11) NOT NULL,
  `deudor` int(11) NOT NULL,
  `descripcion` text,
  `monto` float NOT NULL DEFAULT '0',
  `fecha` date NOT NULL,
  `pagada` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deudores`
--

CREATE TABLE `deudores` (
  `id` int(11) NOT NULL,
  `nombre` text NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD UNIQUE KEY `idx_cuentas` (`id`) USING BTREE;

--
-- Indices de la tabla `deudas`
--
ALTER TABLE `deudas`
  ADD UNIQUE KEY `idx_haber` (`id`),
  ADD KEY `idx_deudor_deudores` (`deudor`);

--
-- Indices de la tabla `deudores`
--
ALTER TABLE `deudores`
  ADD UNIQUE KEY `idx_deudores` (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;
--
-- AUTO_INCREMENT de la tabla `deudas`
--
ALTER TABLE `deudas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT de la tabla `deudores`
--
ALTER TABLE `deudores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `deudas`
--
ALTER TABLE `deudas`
  ADD CONSTRAINT `fk_deudor` FOREIGN KEY (`deudor`) REFERENCES `deudores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
