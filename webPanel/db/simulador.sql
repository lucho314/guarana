-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 26-11-2016 a las 22:31:00
-- Versión del servidor: 10.1.19-MariaDB
-- Versión de PHP: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `simulador`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_instructor`
--

CREATE TABLE `alumno_instructor` (
  `id` int(11) NOT NULL,
  `usuario_instructor_id` int(11) NOT NULL,
  `usuario_alumno_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clase`
--

CREATE TABLE `clase` (
  `id` int(11) NOT NULL,
  `usuario_instructor_id` int(11) NOT NULL,
  `usuario_alumno_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `inicio` varchar(15) NOT NULL,
  `fin` varchar(15) NOT NULL,
  `comentario` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clima`
--

CREATE TABLE `clima` (
  `id` int(11) NOT NULL,
  `metar` text NOT NULL,
  `hora` int(11) NOT NULL,
  `clase_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fallas`
--

CREATE TABLE `fallas` (
  `id` int(11) NOT NULL,
  `instrumento_id` int(11) NOT NULL,
  `evento` enum('activo','inactivo','','') NOT NULL,
  `clase_id` int(11) NOT NULL,
  `hora` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `instructores`
--
CREATE TABLE `instructores` (
`id` int(11)
,`nombre` varchar(100)
,`apellido` varchar(100)
,`dni` int(11)
,`usuario` varchar(100)
,`password` varchar(100)
,`tipo_usuario_id` int(11)
,`estado` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instrumentos`
--

CREATE TABLE `instrumentos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `instrumentos`
--

INSERT INTO `instrumentos` (`id`, `descripcion`) VALUES
(1, 'airspeed'),
(2, 'Altimetro'),
(3, 'Horizonte'),
(4, 'Nav1'),
(5, 'Ladeo'),
(6, 'Velocidad Vertical'),
(7, 'ADF'),
(8, 'Nav2');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `pilotos`
--
CREATE TABLE `pilotos` (
`id` int(11)
,`nombre` varchar(100)
,`apellido` varchar(100)
,`dni` int(11)
,`usuario` varchar(100)
,`password` varchar(100)
,`tipo_usuario_id` int(11)
,`estado` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_usuarios`
--

CREATE TABLE `tipo_usuarios` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipo_usuarios`
--

INSERT INTO `tipo_usuarios` (`id`, `descripcion`) VALUES
(1, 'profesor'),
(2, 'alumno');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `dni` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `tipo_usuario_id` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura para la vista `instructores`
--
DROP TABLE IF EXISTS `instructores`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `instructores`  AS  select `usuarios`.`id` AS `id`,`usuarios`.`nombre` AS `nombre`,`usuarios`.`apellido` AS `apellido`,`usuarios`.`dni` AS `dni`,`usuarios`.`usuario` AS `usuario`,`usuarios`.`password` AS `password`,`usuarios`.`tipo_usuario_id` AS `tipo_usuario_id`,`usuarios`.`estado` AS `estado` from `usuarios` where (`usuarios`.`tipo_usuario_id` = 1) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `pilotos`
--
DROP TABLE IF EXISTS `pilotos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `pilotos`  AS  select `usuarios`.`id` AS `id`,`usuarios`.`nombre` AS `nombre`,`usuarios`.`apellido` AS `apellido`,`usuarios`.`dni` AS `dni`,`usuarios`.`usuario` AS `usuario`,`usuarios`.`password` AS `password`,`usuarios`.`tipo_usuario_id` AS `tipo_usuario_id`,`usuarios`.`estado` AS `estado` from `usuarios` where (`usuarios`.`tipo_usuario_id` = 2) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumno_instructor`
--
ALTER TABLE `alumno_instructor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_instructor_id` (`usuario_instructor_id`),
  ADD KEY `usuario_alumno_id` (`usuario_alumno_id`);

--
-- Indices de la tabla `clase`
--
ALTER TABLE `clase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_instructor_id` (`usuario_instructor_id`),
  ADD KEY `usuario_alumno_id` (`usuario_alumno_id`);

--
-- Indices de la tabla `clima`
--
ALTER TABLE `clima`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clase_id` (`clase_id`);

--
-- Indices de la tabla `fallas`
--
ALTER TABLE `fallas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instrumento_id` (`instrumento_id`),
  ADD KEY `clase_id` (`clase_id`);

--
-- Indices de la tabla `instrumentos`
--
ALTER TABLE `instrumentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_usuarios`
--
ALTER TABLE `tipo_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_usuario_id` (`tipo_usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumno_instructor`
--
ALTER TABLE `alumno_instructor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `clase`
--
ALTER TABLE `clase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;
--
-- AUTO_INCREMENT de la tabla `clima`
--
ALTER TABLE `clima`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `fallas`
--
ALTER TABLE `fallas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `instrumentos`
--
ALTER TABLE `instrumentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `tipo_usuarios`
--
ALTER TABLE `tipo_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno_instructor`
--
ALTER TABLE `alumno_instructor`
  ADD CONSTRAINT `alumno_instructor_ibfk_1` FOREIGN KEY (`usuario_instructor_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `alumno_instructor_ibfk_2` FOREIGN KEY (`usuario_alumno_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `fallas`
--
ALTER TABLE `fallas`
  ADD CONSTRAINT `fallas_ibfk_1` FOREIGN KEY (`instrumento_id`) REFERENCES `instrumentos` (`id`),
  ADD CONSTRAINT `fallas_ibfk_2` FOREIGN KEY (`clase_id`) REFERENCES `clase` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`tipo_usuario_id`) REFERENCES `tipo_usuarios` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
