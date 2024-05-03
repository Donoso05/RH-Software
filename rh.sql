-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2024 at 06:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rh`
--

-- --------------------------------------------------------

--
-- Table structure for table `arl`
--

CREATE TABLE `arl` (
  `id_arl` int(10) NOT NULL,
  `tipo` varchar(30) DEFAULT NULL,
  `cotizacion` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `arl`
--

INSERT INTO `arl` (`id_arl`, `tipo`, `cotizacion`) VALUES
(1, 'Riesgo mínimo', 0.522),
(2, 'Riesgo bajo', 1.044),
(3, 'Riesgo medio	', 2.436),
(4, 'Riesgo alto', 4.350),
(5, 'Riesgo máximo', 6.960);

-- --------------------------------------------------------

--
-- Table structure for table `auxtransporte`
--

CREATE TABLE `auxtransporte` (
  `id_auxtransporte` int(10) NOT NULL,
  `valor` decimal(10,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auxtransporte`
--

INSERT INTO `auxtransporte` (`id_auxtransporte`, `valor`) VALUES
(1, 162.000);

-- --------------------------------------------------------

--
-- Table structure for table `empresas`
--

CREATE TABLE `empresas` (
  `nit_empresa` int(10) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `id_licencia` int(10) NOT NULL,
  `correo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `estado`
--

CREATE TABLE `estado` (
  `id_estado` int(10) NOT NULL,
  `estado` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `estado`
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
-- Table structure for table `ingresos`
--

CREATE TABLE `ingresos` (
  `id_ingreso` int(10) NOT NULL,
  `horas_extras` decimal(10,2) DEFAULT NULL,
  `id_auxtransporte` int(10) NOT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `licencia`
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
-- Table structure for table `nomina`
--

CREATE TABLE `nomina` (
  `id_nomina` int(10) NOT NULL,
  `id_usuario` int(10) NOT NULL,
  `mes` varchar(200) DEFAULT NULL,
  `anio` varchar(200) DEFAULT NULL,
  `id_estado` int(10) NOT NULL,
  `id_arl` int(11) NOT NULL,
  `id_salud` int(11) NOT NULL,
  `id_pension` int(11) NOT NULL,
  `parafiscales` int(10) NOT NULL,
  `id_auxtransporte` int(11) NOT NULL,
  `horas_extras` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pension`
--

CREATE TABLE `pension` (
  `id_pension` int(11) NOT NULL,
  `porcentaje_p` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salud`
--

CREATE TABLE `salud` (
  `id_salud` int(11) NOT NULL,
  `porcentaje_s` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `solic_prestamo`
--

CREATE TABLE `solic_prestamo` (
  `id_prestamo` int(10) NOT NULL,
  `id_usuario` int(10) NOT NULL,
  `monto_solicitado` decimal(10,2) DEFAULT NULL,
  `id_estado` int(10) NOT NULL,
  `valor_cuotas` decimal(10,3) DEFAULT NULL,
  `cant_cuotas` int(10) DEFAULT NULL,
  `mes` varchar(20) NOT NULL,
  `anio` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `solic_prestamo`
--

INSERT INTO `solic_prestamo` (`id_prestamo`, `id_usuario`, `monto_solicitado`, `id_estado`, `valor_cuotas`, `cant_cuotas`, `mes`, `anio`) VALUES
(1, 23433979, 1500000.00, 10, 62.500, 5, '', ''),
(2, 23433979, 1500000.00, 10, 62.500, 5, '', ''),
(3, 32332, 500000.00, 6, 83.330, 6, '', ''),
(5, 48787, 3600000.00, 6, 150.000, 24, '', ''),
(6, 87877812, 2000000.00, 6, 166.670, 12, '', ''),
(7, 444448, 20000.00, 6, 3.330, 6, '', ''),
(8, 454545, 565454.00, 6, 28.270, 20, '', ''),
(9, 654954, 56556.00, 6, 9.430, 6, '', ''),
(11, 6555, 2500000.00, 6, 104.167, 24, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tipos_usuarios`
--

CREATE TABLE `tipos_usuarios` (
  `id_tipo_usuario` int(10) NOT NULL,
  `tipo_usuario` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipos_usuarios`
--

INSERT INTO `tipos_usuarios` (`id_tipo_usuario`, `tipo_usuario`) VALUES
(1, 'Administrador'),
(2, 'Contador'),
(3, 'Empleado'),
(4, 'hola');

-- --------------------------------------------------------

--
-- Table structure for table `tipo_cargo`
--

CREATE TABLE `tipo_cargo` (
  `id_tipo_cargo` int(10) NOT NULL,
  `cargo` varchar(30) DEFAULT NULL,
  `salario_base` decimal(10,0) DEFAULT NULL,
  `id_arl` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipo_cargo`
--

INSERT INTO `tipo_cargo` (`id_tipo_cargo`, `cargo`, `salario_base`, `id_arl`) VALUES
(6, 'Docente', 1358000, 1),
(7, 'Auxuliar de Bodega', 1500000, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tipo_permiso`
--

CREATE TABLE `tipo_permiso` (
  `id_tipo_permiso` int(10) NOT NULL,
  `tipo_permiso` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipo_permiso`
--

INSERT INTO `tipo_permiso` (`id_tipo_permiso`, `tipo_permiso`) VALUES
(1, 'Calamidad domesticaaa'),
(2, 'licencia de embarazo'),
(3, 'nose');

-- --------------------------------------------------------

--
-- Table structure for table `tram_permiso`
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
-- Dumping data for table `tram_permiso`
--

INSERT INTO `tram_permiso` (`id_permiso`, `id_usuario`, `id_tipo_permiso`, `fecha_inicio`, `fecha_fin`, `id_estado`, `incapacidad`) VALUES
(1, 123123, 1, '2024-02-26', '2024-02-27', '', 0x796f796f796f),
(2, 2147483647, 3, '2024-02-27', '2024-04-25', '11', 0x6e6f207365207175652070616f73);

-- --------------------------------------------------------

--
-- Table structure for table `triggers`
--

CREATE TABLE `triggers` (
  `id_trigger` int(10) NOT NULL,
  `id_usuario` int(10) NOT NULL,
  `contrasena` varchar(500) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
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
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `id_tipo_cargo`, `id_estado`, `correo`, `id_tipo_usuario`, `contrasena`, `nit_empresa`) VALUES
(1016, 'andreaa', 1, 1, 'andreasilva23433@gmail.com', 1, '$2y$10$L/7CnXztkOVSPduaiYe5cumkuppkSRbcVs63SskndTM13peHSOVSG', 545458),
(5494, 'ana', 6, 1, 'ana@gmail.com', 3, '$2y$10$IhoDoIhv6jJmkqjJuVGGROl4PGicxrq9sNsnLmekGpOfCKqaN.qni', 4482022),
(5545, 'hhgg', 6, 1, 'ghfhgg', 1, '$2y$10$.O7y.MnXr1BFhcNj0OMD5uDu2lI6zg9tLwLe0l9/psRGQ7x7rhOI2', 5565),
(42334, 'Claudia Silva', 6, 1, 'klcalderon617@misena.edu.co', 1, '$2y$10$/7NPeoEaj5vem5sD7mnOaeQ4KXnH8YHlcHIafp.l1e1CC/rDJuVHi', 43545),
(23433879, 'Claudia Silvaa', 6, 5, 'claudiaaa@gmail.com', 1, '$2y$10$TXL86/m1iQsd.QXIwnLiS.jCawwOyfYBXcwl5OpPMDRLZ3Zb3l1lS', 5655519),
(1016007855, 'andrea silva', 1, 1, 'ashajhsgmail.com', 1, '1016007855', 2525);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arl`
--
ALTER TABLE `arl`
  ADD PRIMARY KEY (`id_arl`);

--
-- Indexes for table `auxtransporte`
--
ALTER TABLE `auxtransporte`
  ADD PRIMARY KEY (`id_auxtransporte`);

--
-- Indexes for table `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`nit_empresa`);

--
-- Indexes for table `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indexes for table `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`id_ingreso`);

--
-- Indexes for table `licencia`
--
ALTER TABLE `licencia`
  ADD PRIMARY KEY (`id_licencia`);

--
-- Indexes for table `nomina`
--
ALTER TABLE `nomina`
  ADD PRIMARY KEY (`id_nomina`);

--
-- Indexes for table `pension`
--
ALTER TABLE `pension`
  ADD PRIMARY KEY (`id_pension`);

--
-- Indexes for table `salud`
--
ALTER TABLE `salud`
  ADD PRIMARY KEY (`id_salud`);

--
-- Indexes for table `solic_prestamo`
--
ALTER TABLE `solic_prestamo`
  ADD PRIMARY KEY (`id_prestamo`);

--
-- Indexes for table `tipos_usuarios`
--
ALTER TABLE `tipos_usuarios`
  ADD PRIMARY KEY (`id_tipo_usuario`);

--
-- Indexes for table `tipo_cargo`
--
ALTER TABLE `tipo_cargo`
  ADD PRIMARY KEY (`id_tipo_cargo`);

--
-- Indexes for table `tipo_permiso`
--
ALTER TABLE `tipo_permiso`
  ADD PRIMARY KEY (`id_tipo_permiso`);

--
-- Indexes for table `tram_permiso`
--
ALTER TABLE `tram_permiso`
  ADD PRIMARY KEY (`id_permiso`);

--
-- Indexes for table `triggers`
--
ALTER TABLE `triggers`
  ADD PRIMARY KEY (`id_trigger`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arl`
--
ALTER TABLE `arl`
  MODIFY `id_arl` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `auxtransporte`
--
ALTER TABLE `auxtransporte`
  MODIFY `id_auxtransporte` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id_ingreso` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nomina`
--
ALTER TABLE `nomina`
  MODIFY `id_nomina` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pension`
--
ALTER TABLE `pension`
  MODIFY `id_pension` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salud`
--
ALTER TABLE `salud`
  MODIFY `id_salud` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tipos_usuarios`
--
ALTER TABLE `tipos_usuarios`
  MODIFY `id_tipo_usuario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tipo_cargo`
--
ALTER TABLE `tipo_cargo`
  MODIFY `id_tipo_cargo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tipo_permiso`
--
ALTER TABLE `tipo_permiso`
  MODIFY `id_tipo_permiso` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tram_permiso`
--
ALTER TABLE `tram_permiso`
  MODIFY `id_permiso` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `triggers`
--
ALTER TABLE `triggers`
  MODIFY `id_trigger` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
