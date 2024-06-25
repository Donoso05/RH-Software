-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 24-06-2024 a las 16:17:47
-- Versión del servidor: 10.11.7-MariaDB-cll-lve
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rh2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arl`
--

CREATE TABLE `arl` (
  `id_arl` int(10) NOT NULL,
  `tipo` varchar(30) DEFAULT NULL,
  `porcentaje` decimal(10,0) NOT NULL,
  `nit_empresa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `arl`
--

INSERT INTO `arl` (`id_arl`, `tipo`, `porcentaje`, `nit_empresa`) VALUES
(1, 'Riesgo minimo', 4, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auxtransporte`
--

CREATE TABLE `auxtransporte` (
  `id_auxtransporte` int(10) NOT NULL,
  `valor` int(5) DEFAULT NULL,
  `nit_empresa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle`
--

CREATE TABLE `detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_usuario` varchar(20) NOT NULL,
  `id_nomina` int(11) NOT NULL,
  `fecha_li` datetime NOT NULL,
  `salario_total` int(11) NOT NULL,
  `dias_trabajados` int(11) NOT NULL,
  `horas_extras` int(11) NOT NULL,
  `precio_arl` int(11) NOT NULL,
  `deduccion_salud` int(11) NOT NULL,
  `deduccion_pension` int(11) NOT NULL,
  `total_deducciones` int(11) NOT NULL,
  `valor_horas_extras` int(11) NOT NULL,
  `aux_transporte_valor` int(11) NOT NULL,
  `total_ingresos` int(11) NOT NULL,
  `valor_neto` int(11) NOT NULL,
  `valor_cuotas` int(11) DEFAULT 0,
  `monto_solicitado` int(11) DEFAULT 0,
  `nit_empresa` varchar(15) NOT NULL,
  `mes` varchar(200) NOT NULL,
  `anio` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `nit_empresa` varchar(15) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `id_licencia` int(10) DEFAULT NULL,
  `correo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id_estado` int(10) NOT NULL,
  `estado` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`id_estado`, `estado`) VALUES
(1, 'Activo'),
(2, 'Inactivo'),
(3, 'En espera'),
(4, 'Liquidado'),
(5, 'Aprobado'),
(7, 'Rechazado'),
(8, 'Pagado'),
(9, 'Finalizado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licencia`
--

CREATE TABLE `licencia` (
  `id_licencia` int(10) NOT NULL,
  `nit_empresa` varchar(15) NOT NULL,
  `licencia` varchar(50) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_final` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomina`
--

CREATE TABLE `nomina` (
  `id_nomina` int(10) NOT NULL,
  `id_usuario` varchar(20) NOT NULL,
  `mes` varchar(200) DEFAULT NULL,
  `anio` varchar(200) DEFAULT NULL,
  `id_estado` int(10) NOT NULL DEFAULT 3,
  `precio_arl` int(10) NOT NULL,
  `deduccion_salud` int(10) NOT NULL,
  `deduccion_pension` int(10) NOT NULL,
  `valor_cuotas` int(11) NOT NULL,
  `total_deducciones` int(10) NOT NULL,
  `aux_transporte_valor` int(10) NOT NULL,
  `horas_extras` int(10) NOT NULL,
  `monto_solicitado` int(11) NOT NULL,
  `salario_total` int(11) NOT NULL,
  `salario_base` int(11) NOT NULL,
  `dias_trabajados` int(5) NOT NULL,
  `valor_horas_extras` int(11) NOT NULL,
  `total_ingresos` int(11) NOT NULL,
  `valor_neto` int(11) NOT NULL,
  `fecha_li` datetime NOT NULL,
  `nit_empresa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `observaciones`
--

CREATE TABLE `observaciones` (
  `id_observacion` int(11) NOT NULL,
  `observacion` varchar(200) NOT NULL,
  `nit_empresa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pension`
--

CREATE TABLE `pension` (
  `id_pension` int(11) NOT NULL,
  `porcentaje_p` decimal(10,0) NOT NULL,
  `nit_empresa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salud`
--

CREATE TABLE `salud` (
  `id_salud` int(11) NOT NULL,
  `porcentaje_s` decimal(10,0) NOT NULL,
  `nit_empresa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solic_prestamo`
--

CREATE TABLE `solic_prestamo` (
  `id_prestamo` varchar(20) NOT NULL,
  `id_usuario` varchar(20) NOT NULL,
  `monto_solicitado` decimal(10,0) DEFAULT NULL,
  `id_estado` int(10) NOT NULL,
  `motivo_rechazo` varchar(200) DEFAULT NULL,
  `valor_cuotas` decimal(10,0) DEFAULT NULL,
  `cant_cuotas` int(10) DEFAULT NULL,
  `mes` varchar(20) NOT NULL,
  `anio` varchar(20) NOT NULL,
  `nit_empresa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_usuarios`
--

CREATE TABLE `tipos_usuarios` (
  `id_tipo_usuario` int(10) NOT NULL,
  `tipo_usuario` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_usuarios`
--

INSERT INTO `tipos_usuarios` (`id_tipo_usuario`, `tipo_usuario`) VALUES
(1, 'Administrador'),
(2, 'Contador'),
(3, 'Empleado'),
(6, 'Desarrollador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_cargo`
--

CREATE TABLE `tipo_cargo` (
  `id_tipo_cargo` int(10) NOT NULL,
  `id_tipo_usuario` int(10) NOT NULL,
  `cargo` varchar(30) DEFAULT NULL,
  `salario_base` decimal(10,0) DEFAULT NULL,
  `id_arl` int(10) DEFAULT NULL,
  `nit_empresa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_cargo`
--

INSERT INTO `tipo_cargo` (`id_tipo_cargo`, `id_tipo_usuario`, `cargo`, `salario_base`, `id_arl`, `nit_empresa`) VALUES
(1, 1, 'Administrador', 2000000, 1, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_permiso`
--

CREATE TABLE `tipo_permiso` (
  `id_tipo_permiso` int(10) NOT NULL,
  `tipo_permiso` varchar(50) DEFAULT NULL,
  `dias` int(11) NOT NULL,
  `nit_empresa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tram_permiso`
--

CREATE TABLE `tram_permiso` (
  `id_permiso` int(10) NOT NULL,
  `descripcion` text NOT NULL,
  `id_usuario` varchar(20) NOT NULL,
  `id_tipo_permiso` int(10) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `id_estado` int(10) NOT NULL DEFAULT 3,
  `motivo_rechazo` int(11) DEFAULT NULL,
  `incapacidad` varchar(500) DEFAULT NULL,
  `nit_empresa` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `triggers`
--

CREATE TABLE `triggers` (
  `id_trigger` int(10) NOT NULL,
  `id_usuario` varchar(20) NOT NULL,
  `contrasena` varchar(500) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` varchar(20) NOT NULL,
  `nombre` varchar(250) DEFAULT NULL,
  `id_tipo_cargo` int(10) NOT NULL,
  `id_estado` int(10) NOT NULL DEFAULT 1,
  `correo` varchar(100) DEFAULT NULL,
  `id_tipo_usuario` int(10) NOT NULL,
  `contrasena` varchar(500) DEFAULT NULL,
  `nit_empresa` varchar(15) NOT NULL,
  `codigo_barras` varchar(200) NOT NULL,
  `foto` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `id_tipo_cargo`, `id_estado`, `correo`, `id_tipo_usuario`, `contrasena`, `nit_empresa`, `codigo_barras`, `foto`) VALUES
('934934', 'Alirio Donoso', 4, 1, 'ali@gmail.com', 6, '$2y$10$XYnhrV7SBuWpHXElBaU2P.XKjE3h6zLekCjV6xqsmX6s3QRG.4vPi', '', '', 'uploads/20426137.jpg-c_310_420_x-f_jpg-q_x-xxyxx.jpg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `arl`
--
ALTER TABLE `arl`
  ADD PRIMARY KEY (`id_arl`);

--
-- Indices de la tabla `auxtransporte`
--
ALTER TABLE `auxtransporte`
  ADD PRIMARY KEY (`id_auxtransporte`);

--
-- Indices de la tabla `detalle`
--
ALTER TABLE `detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `detalle_ibfk_1` (`id_usuario`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`nit_empresa`),
  ADD KEY `empresas_ibfk_1` (`id_licencia`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `licencia`
--
ALTER TABLE `licencia`
  ADD PRIMARY KEY (`id_licencia`),
  ADD KEY `licencia_ibfk_1` (`nit_empresa`);

--
-- Indices de la tabla `nomina`
--
ALTER TABLE `nomina`
  ADD PRIMARY KEY (`id_nomina`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_estado` (`id_estado`),
  ADD KEY `id_auxtransporte` (`aux_transporte_valor`);

--
-- Indices de la tabla `observaciones`
--
ALTER TABLE `observaciones`
  ADD PRIMARY KEY (`id_observacion`);

--
-- Indices de la tabla `pension`
--
ALTER TABLE `pension`
  ADD PRIMARY KEY (`id_pension`);

--
-- Indices de la tabla `salud`
--
ALTER TABLE `salud`
  ADD PRIMARY KEY (`id_salud`);

--
-- Indices de la tabla `solic_prestamo`
--
ALTER TABLE `solic_prestamo`
  ADD PRIMARY KEY (`id_prestamo`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `tipos_usuarios`
--
ALTER TABLE `tipos_usuarios`
  ADD PRIMARY KEY (`id_tipo_usuario`);

--
-- Indices de la tabla `tipo_cargo`
--
ALTER TABLE `tipo_cargo`
  ADD PRIMARY KEY (`id_tipo_cargo`),
  ADD KEY `id_arl` (`id_arl`);

--
-- Indices de la tabla `tipo_permiso`
--
ALTER TABLE `tipo_permiso`
  ADD PRIMARY KEY (`id_tipo_permiso`);

--
-- Indices de la tabla `tram_permiso`
--
ALTER TABLE `tram_permiso`
  ADD PRIMARY KEY (`id_permiso`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_tipo_permiso` (`id_tipo_permiso`),
  ADD KEY `fk_estado` (`id_estado`);

--
-- Indices de la tabla `triggers`
--
ALTER TABLE `triggers`
  ADD PRIMARY KEY (`id_trigger`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_tipo_cargo` (`id_tipo_cargo`),
  ADD KEY `id_estado` (`id_estado`),
  ADD KEY `id_tipo_usuario` (`id_tipo_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `arl`
--
ALTER TABLE `arl`
  MODIFY `id_arl` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `auxtransporte`
--
ALTER TABLE `auxtransporte`
  MODIFY `id_auxtransporte` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle`
--
ALTER TABLE `detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id_estado` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `licencia`
--
ALTER TABLE `licencia`
  MODIFY `id_licencia` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `observaciones`
--
ALTER TABLE `observaciones`
  MODIFY `id_observacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pension`
--
ALTER TABLE `pension`
  MODIFY `id_pension` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `salud`
--
ALTER TABLE `salud`
  MODIFY `id_salud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipos_usuarios`
--
ALTER TABLE `tipos_usuarios`
  MODIFY `id_tipo_usuario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipo_cargo`
--
ALTER TABLE `tipo_cargo`
  MODIFY `id_tipo_cargo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipo_permiso`
--
ALTER TABLE `tipo_permiso`
  MODIFY `id_tipo_permiso` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tram_permiso`
--
ALTER TABLE `tram_permiso`
  MODIFY `id_permiso` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `triggers`
--
ALTER TABLE `triggers`
  MODIFY `id_trigger` int(10) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle`
--
ALTER TABLE `detalle`
  ADD CONSTRAINT `detalle_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD CONSTRAINT `empresas_ibfk_1` FOREIGN KEY (`id_licencia`) REFERENCES `licencia` (`id_licencia`);

--
-- Filtros para la tabla `licencia`
--
ALTER TABLE `licencia`
  ADD CONSTRAINT `licencia_ibfk_1` FOREIGN KEY (`nit_empresa`) REFERENCES `empresas` (`nit_empresa`);

--
-- Filtros para la tabla `solic_prestamo`
--
ALTER TABLE `solic_prestamo`
  ADD CONSTRAINT `solic_prestamo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `solic_prestamo_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `tipo_cargo`
--
ALTER TABLE `tipo_cargo`
  ADD CONSTRAINT `tipo_cargo_ibfk_1` FOREIGN KEY (`id_arl`) REFERENCES `arl` (`id_arl`);

--
-- Filtros para la tabla `triggers`
--
ALTER TABLE `triggers`
  ADD CONSTRAINT `triggers_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
