-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-03-2024 a las 14:09:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rh`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arl`
--

CREATE TABLE `arl` (
  `id_arl` int(10) NOT NULL,
  `tipo` varchar(30) DEFAULT NULL,
  `cotizacion` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `arl`
--

INSERT INTO `arl` (`id_arl`, `tipo`, `cotizacion`) VALUES
(1, 'Riesgo mínimo', 0.522),
(2, 'Riesgo bajo', 1.044),
(3, 'Riesgo medio	', 2.436),
(4, 'Riesgo alto', 4.350),
(5, 'Riesgo máximo', 6.960);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auxtransporte`
--

CREATE TABLE `auxtransporte` (
  `id_auxtransporte` int(10) NOT NULL,
  `valor` decimal(10,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auxtransporte`
--

INSERT INTO `auxtransporte` (`id_auxtransporte`, `valor`) VALUES
(1, 162.000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `nit_empresa` int(10) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `id_licencia` int(10) NOT NULL,
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
(5, 'Inactivo'),
(6, 'En proceso'),
(10, 'Validado'),
(11, 'Aprobado'),
(15, 'En espera');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id_gastos` int(10) NOT NULL,
  `salud` decimal(10,2) DEFAULT NULL,
  `pension` decimal(10,2) DEFAULT NULL,
  `id_arl` int(10) NOT NULL,
  `id_prestamo` int(10) NOT NULL,
  `total_gastos` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `id_ingreso` int(10) NOT NULL,
  `horas_extras` decimal(10,2) DEFAULT NULL,
  `id_auxtransporte` int(10) NOT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licencia`
--

CREATE TABLE `licencia` (
  `id_licencia` int(10) NOT NULL,
  `nit_empresa` int(10) NOT NULL,
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
  `id_usuario` int(10) NOT NULL,
  `mes` varchar(200) DEFAULT NULL,
  `ano` varchar(200) DEFAULT NULL,
  `id_ingreso` int(10) NOT NULL,
  `id_gastos` int(10) NOT NULL,
  `id_estado` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solic_prestamo`
--

CREATE TABLE `solic_prestamo` (
  `id_prestamo` int(10) NOT NULL,
  `id_usuario` int(10) NOT NULL,
  `monto_solicitado` decimal(10,2) DEFAULT NULL,
  `id_estado` int(10) NOT NULL,
  `valor_cuotas` decimal(10,3) DEFAULT NULL,
  `cant_cuotas` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solic_prestamo`
--

INSERT INTO `solic_prestamo` (`id_prestamo`, `id_usuario`, `monto_solicitado`, `id_estado`, `valor_cuotas`, `cant_cuotas`) VALUES
(1, 23433979, 1500000.00, 10, 62.500, 5),
(2, 23433979, 1500000.00, 10, 62.500, 5),
(3, 32332, 500000.00, 6, 83.330, 6),
(5, 48787, 3600000.00, 6, 150.000, 24),
(6, 87877812, 2000000.00, 6, 166.670, 12),
(7, 444448, 20000.00, 6, 3.330, 6),
(8, 454545, 565454.00, 6, 28.270, 20),
(9, 654954, 56556.00, 6, 9.430, 6),
(11, 6555, 2500000.00, 6, 104.167, 24);

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
(4, 'hola');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_cargo`
--

CREATE TABLE `tipo_cargo` (
  `id_tipo_cargo` int(10) NOT NULL,
  `cargo` varchar(30) DEFAULT NULL,
  `salario_base` decimal(10,0) DEFAULT NULL,
  `id_arl` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_cargo`
--

INSERT INTO `tipo_cargo` (`id_tipo_cargo`, `cargo`, `salario_base`, `id_arl`) VALUES
(6, 'Docente', 1358000, 1),
(7, 'Auxuliar de Bodega', 1500000, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_permiso`
--

CREATE TABLE `tipo_permiso` (
  `id_tipo_permiso` int(10) NOT NULL,
  `tipo_permiso` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_permiso`
--

INSERT INTO `tipo_permiso` (`id_tipo_permiso`, `tipo_permiso`) VALUES
(1, 'Calamidad domesticaaa'),
(2, 'licencia de embarazo'),
(3, 'nose');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tram_permiso`
--

CREATE TABLE `tram_permiso` (
  `id_permiso` int(10) NOT NULL,
  `id_usuario` int(10) NOT NULL,
  `id_tipo_permiso` int(10) NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `id_estado` varchar(50) NOT NULL,
  `incapacidad` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tram_permiso`
--

INSERT INTO `tram_permiso` (`id_permiso`, `id_usuario`, `id_tipo_permiso`, `fecha_inicio`, `fecha_fin`, `id_estado`, `incapacidad`) VALUES
(1, 123123, 1, '2024-02-26', '2024-02-27', '', 0x796f796f796f),
(2, 2147483647, 3, '2024-02-27', '2024-04-25', '11', 0x6e6f207365207175652070616f73);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `triggers`
--

CREATE TABLE `triggers` (
  `id_trigger` int(10) NOT NULL,
  `id_usuario` int(10) NOT NULL,
  `contrasena` varchar(500) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(10) NOT NULL,
  `nombre` varchar(250) DEFAULT NULL,
  `id_tipo_cargo` int(10) NOT NULL,
  `id_estado` int(10) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `id_tipo_usuario` int(10) NOT NULL,
  `contrasena` varchar(500) DEFAULT NULL,
  `nit_empresa` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `id_tipo_cargo`, `id_estado`, `correo`, `id_tipo_usuario`, `contrasena`, `nit_empresa`) VALUES
(5494, 'ana', 6, 1, 'ana@gmail.com', 3, '$2y$10$IhoDoIhv6jJmkqjJuVGGROl4PGicxrq9sNsnLmekGpOfCKqaN.qni', 4482022),
(5545, 'hhgg', 6, 1, 'ghfhgg', 1, '$2y$10$.O7y.MnXr1BFhcNj0OMD5uDu2lI6zg9tLwLe0l9/psRGQ7x7rhOI2', 5565),
(42334, 'Claudia Silva', 6, 1, 'klcalderon617@misena.edu.co', 1, '$2y$10$/7NPeoEaj5vem5sD7mnOaeQ4KXnH8YHlcHIafp.l1e1CC/rDJuVHi', 43545),
(23433879, 'Claudia Silvaa', 6, 5, 'claudiaaa@gmail.com', 1, '$2y$10$TXL86/m1iQsd.QXIwnLiS.jCawwOyfYBXcwl5OpPMDRLZ3Zb3l1lS', 5655519);

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
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`nit_empresa`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id_gastos`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`id_ingreso`);

--
-- Indices de la tabla `licencia`
--
ALTER TABLE `licencia`
  ADD PRIMARY KEY (`id_licencia`);

--
-- Indices de la tabla `nomina`
--
ALTER TABLE `nomina`
  ADD PRIMARY KEY (`id_nomina`);

--
-- Indices de la tabla `solic_prestamo`
--
ALTER TABLE `solic_prestamo`
  ADD PRIMARY KEY (`id_prestamo`);

--
-- Indices de la tabla `tipos_usuarios`
--
ALTER TABLE `tipos_usuarios`
  ADD PRIMARY KEY (`id_tipo_usuario`);

--
-- Indices de la tabla `tipo_cargo`
--
ALTER TABLE `tipo_cargo`
  ADD PRIMARY KEY (`id_tipo_cargo`);

--
-- Indices de la tabla `tipo_permiso`
--
ALTER TABLE `tipo_permiso`
  ADD PRIMARY KEY (`id_tipo_permiso`);

--
-- Indices de la tabla `tram_permiso`
--
ALTER TABLE `tram_permiso`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indices de la tabla `triggers`
--
ALTER TABLE `triggers`
  ADD PRIMARY KEY (`id_trigger`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `arl`
--
ALTER TABLE `arl`
  MODIFY `id_arl` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `auxtransporte`
--
ALTER TABLE `auxtransporte`
  MODIFY `id_auxtransporte` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id_gastos` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id_ingreso` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `nomina`
--
ALTER TABLE `nomina`
  MODIFY `id_nomina` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solic_prestamo`
--
ALTER TABLE `solic_prestamo`
  MODIFY `id_prestamo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `tipos_usuarios`
--
ALTER TABLE `tipos_usuarios`
  MODIFY `id_tipo_usuario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_cargo`
--
ALTER TABLE `tipo_cargo`
  MODIFY `id_tipo_cargo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipo_permiso`
--
ALTER TABLE `tipo_permiso`
  MODIFY `id_tipo_permiso` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tram_permiso`
--
ALTER TABLE `tram_permiso`
  MODIFY `id_permiso` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `triggers`
--
ALTER TABLE `triggers`
  MODIFY `id_trigger` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
