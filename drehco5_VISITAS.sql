-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-11-2024 a las 22:32:23
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
-- Base de datos: `drehco5_VISITAS`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_menu`
--

CREATE TABLE `admin_menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `title` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `uri` varchar(191) DEFAULT NULL,
  `permission` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_menu`
--

INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 'Dashboard', 'icon-chart-bar', '/', NULL, NULL, NULL),
(2, 0, 2, 'Admin', 'icon-server', '', NULL, NULL, NULL),
(3, 2, 3, 'Users', 'icon-users', 'auth/users', NULL, NULL, NULL),
(4, 2, 4, 'Roles', 'icon-user', 'auth/roles', NULL, NULL, NULL),
(5, 2, 5, 'Permission', 'icon-ban', 'auth/permissions', NULL, NULL, NULL),
(6, 2, 6, 'Menu', 'icon-bars', 'auth/menu', NULL, NULL, NULL),
(7, 2, 7, 'Operation log', 'icon-history', 'auth/logs', NULL, NULL, NULL),
(8, 0, 8, 'Helpers', 'icon-cogs', '', NULL, '2024-11-08 02:08:21', '2024-11-19 01:18:25'),
(9, 8, 9, 'Scaffold', 'icon-keyboard', 'helpers/scaffold', NULL, '2024-11-08 02:08:21', '2024-11-19 01:18:25'),
(10, 8, 10, 'Database terminal', 'icon-database', 'helpers/terminal/database', NULL, '2024-11-08 02:08:21', '2024-11-19 01:18:25'),
(11, 8, 11, 'Laravel artisan', 'icon-terminal', 'helpers/terminal/artisan', NULL, '2024-11-08 02:08:21', '2024-11-19 01:18:25'),
(12, 8, 12, 'Routes', 'icon-list-alt', 'helpers/routes', NULL, '2024-11-08 02:08:22', '2024-11-19 01:18:25'),
(13, 0, 13, 'visitas', 'icon-user', 'visitas', '*', '2024-11-19 01:18:02', '2024-11-19 01:18:25'),
(14, 0, 15, 'recesos', 'icon-clock', 'recesos', '*', '2024-11-19 01:20:46', '2024-11-19 01:23:13'),
(15, 0, 14, 'modificador-visitas', 'icon-user-cog', 'modificador-visitas', '*', '2024-11-19 01:23:04', '2024-11-19 01:23:13'),
(16, 0, 16, 'modificador-recesos', 'icon-user-clock', 'modificador-recesos', '*', '2024-11-19 01:23:46', '2024-11-19 01:23:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_operation_log`
--

CREATE TABLE `admin_operation_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `path` varchar(191) NOT NULL,
  `method` varchar(10) NOT NULL,
  `ip` varchar(191) NOT NULL,
  `input` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_operation_log`
--

INSERT INTO `admin_operation_log` (`id`, `user_id`, `path`, `method`, `ip`, `input`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'GET', '127.0.0.1', '[]', '2024-11-08 02:06:40', '2024-11-08 02:06:40'),
(2, 1, 'admin', 'GET', '127.0.0.1', '[]', '2024-11-08 02:08:27', '2024-11-08 02:08:27'),
(3, 1, 'admin/helpers/scaffold', 'GET', '127.0.0.1', '[]', '2024-11-08 02:08:31', '2024-11-08 02:08:31'),
(4, 1, 'admin/auth/users', 'GET', '127.0.0.1', '[]', '2024-11-08 02:08:41', '2024-11-08 02:08:41'),
(5, 1, 'admin/auth/users/create', 'GET', '127.0.0.1', '[]', '2024-11-08 02:08:44', '2024-11-08 02:08:44'),
(6, 1, 'admin/auth/users', 'GET', '127.0.0.1', '[]', '2024-11-08 02:08:49', '2024-11-08 02:08:49'),
(7, 1, 'admin/auth/users/create', 'GET', '127.0.0.1', '[]', '2024-11-08 02:10:15', '2024-11-08 02:10:15'),
(8, 1, 'admin/auth/users/create', 'GET', '127.0.0.1', '[]', '2024-11-08 02:10:16', '2024-11-08 02:10:16'),
(9, 1, 'admin', 'GET', '127.0.0.1', '[]', '2024-11-08 03:24:11', '2024-11-08 03:24:11'),
(10, 1, 'admin/auth/users', 'GET', '127.0.0.1', '[]', '2024-11-08 03:24:20', '2024-11-08 03:24:20'),
(11, 1, 'admin/auth/roles', 'GET', '127.0.0.1', '[]', '2024-11-08 03:24:24', '2024-11-08 03:24:24'),
(12, 1, 'admin/auth/permissions', 'GET', '127.0.0.1', '[]', '2024-11-08 03:24:25', '2024-11-08 03:24:25'),
(13, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-08 03:24:27', '2024-11-08 03:24:27'),
(14, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-08 03:24:30', '2024-11-08 03:24:30'),
(15, 1, 'admin/helpers/scaffold', 'GET', '127.0.0.1', '[]', '2024-11-08 03:24:50', '2024-11-08 03:24:50'),
(16, 1, 'admin/helpers/terminal/database', 'GET', '127.0.0.1', '[]', '2024-11-08 03:25:00', '2024-11-08 03:25:00'),
(17, 1, 'admin/helpers/terminal/artisan', 'GET', '127.0.0.1', '[]', '2024-11-08 03:25:04', '2024-11-08 03:25:04'),
(18, 1, 'admin/helpers/routes', 'GET', '127.0.0.1', '[]', '2024-11-08 03:25:08', '2024-11-08 03:25:08'),
(19, 1, 'admin', 'GET', '127.0.0.1', '[]', '2024-11-13 03:26:25', '2024-11-13 03:26:25'),
(20, 1, 'admin', 'GET', '127.0.0.1', '[]', '2024-11-13 03:27:43', '2024-11-13 03:27:43'),
(21, 1, 'admin', 'GET', '127.0.0.1', '[]', '2024-11-13 03:31:23', '2024-11-13 03:31:23'),
(22, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-13 03:32:22', '2024-11-13 03:32:22'),
(23, 1, 'admin/auth/users', 'GET', '127.0.0.1', '[]', '2024-11-13 03:32:25', '2024-11-13 03:32:25'),
(24, 1, 'admin/auth/users/create', 'GET', '127.0.0.1', '[]', '2024-11-13 03:32:30', '2024-11-13 03:32:30'),
(25, 1, 'admin/auth/users', 'POST', '127.0.0.1', '{\"username\":\"johan\",\"name\":\"johan\",\"password\":\"*****-filtered-out-*****\",\"password_confirmation\":\"123\",\"roles\":[\"1\",null],\"search_terms\":null,\"permissions\":[\"1\",null],\"_token\":\"F0Q6szdbi5wIFBlpcO6JxlmfVAeOf6BhOQ7dQmGE\"}', '2024-11-13 03:33:17', '2024-11-13 03:33:17'),
(26, 1, 'admin/auth/users', 'GET', '127.0.0.1', '[]', '2024-11-13 03:33:19', '2024-11-13 03:33:19'),
(27, 1, 'admin/auth/users/2', 'GET', '127.0.0.1', '[]', '2024-11-13 03:33:24', '2024-11-13 03:33:24'),
(28, 1, 'admin/auth/users', 'GET', '127.0.0.1', '[]', '2024-11-13 03:33:30', '2024-11-13 03:33:30'),
(29, 1, 'admin/auth/roles', 'GET', '127.0.0.1', '[]', '2024-11-13 03:33:31', '2024-11-13 03:33:31'),
(30, 1, 'admin/auth/permissions', 'GET', '127.0.0.1', '[]', '2024-11-13 03:33:42', '2024-11-13 03:33:42'),
(31, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-13 03:33:48', '2024-11-13 03:33:48'),
(32, 1, 'admin/helpers/scaffold', 'GET', '127.0.0.1', '[]', '2024-11-13 03:34:37', '2024-11-13 03:34:37'),
(33, 1, 'admin/helpers/terminal/database', 'GET', '127.0.0.1', '[]', '2024-11-13 03:35:40', '2024-11-13 03:35:40'),
(34, 1, 'admin/helpers/routes', 'GET', '127.0.0.1', '[]', '2024-11-13 03:35:41', '2024-11-13 03:35:41'),
(35, 1, 'admin/helpers/scaffold', 'GET', '127.0.0.1', '[]', '2024-11-13 03:35:43', '2024-11-13 03:35:43'),
(36, 1, 'admin', 'GET', '127.0.0.1', '[]', '2024-11-19 01:14:54', '2024-11-19 01:14:54'),
(37, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:16:52', '2024-11-19 01:16:52'),
(38, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:17:11', '2024-11-19 01:17:11'),
(39, 1, 'admin/auth/menu', 'POST', '127.0.0.1', '{\"parent_id\":\"0\",\"search_terms\":null,\"title\":\"visitas\",\"icon\":\"icon-user\",\"uri\":\"visitas\",\"roles\":[\"1\",null],\"permission\":\"*\",\"_token\":\"a9vFU60OCmYghiUZIKl0EZyp9x2u4w6okroMknkK\"}', '2024-11-19 01:18:02', '2024-11-19 01:18:02'),
(40, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:18:03', '2024-11-19 01:18:03'),
(41, 1, 'admin/auth/menu', 'POST', '127.0.0.1', '{\"_order\":\"[{\\\"id\\\":\\\"1\\\"},{\\\"id\\\":\\\"2\\\",\\\"children\\\":[{\\\"id\\\":\\\"3\\\"},{\\\"id\\\":\\\"4\\\"},{\\\"id\\\":\\\"5\\\"},{\\\"id\\\":\\\"6\\\"},{\\\"id\\\":\\\"7\\\"}]},{\\\"id\\\":\\\"8\\\",\\\"children\\\":[{\\\"id\\\":\\\"9\\\"},{\\\"id\\\":\\\"10\\\"},{\\\"id\\\":\\\"11\\\"},{\\\"id\\\":\\\"12\\\"}]},{\\\"id\\\":\\\"13\\\"}]\",\"_token\":\"a9vFU60OCmYghiUZIKl0EZyp9x2u4w6okroMknkK\"}', '2024-11-19 01:18:25', '2024-11-19 01:18:25'),
(42, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:18:26', '2024-11-19 01:18:26'),
(43, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:18:28', '2024-11-19 01:18:28'),
(44, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:18:30', '2024-11-19 01:18:30'),
(45, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:18:32', '2024-11-19 01:18:32'),
(46, 1, 'admin/visitas', 'GET', '127.0.0.1', '[]', '2024-11-19 01:18:34', '2024-11-19 01:18:34'),
(47, 1, 'admin/visitas', 'GET', '127.0.0.1', '[]', '2024-11-19 01:18:40', '2024-11-19 01:18:40'),
(48, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:20:24', '2024-11-19 01:20:24'),
(49, 1, 'admin/auth/menu', 'POST', '127.0.0.1', '{\"parent_id\":\"0\",\"search_terms\":null,\"title\":\"recesos\",\"icon\":\"icon-clock\",\"uri\":\"recesos\",\"roles\":[\"1\",null],\"permission\":\"*\",\"_token\":\"a9vFU60OCmYghiUZIKl0EZyp9x2u4w6okroMknkK\"}', '2024-11-19 01:20:46', '2024-11-19 01:20:46'),
(50, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:20:46', '2024-11-19 01:20:46'),
(51, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:20:53', '2024-11-19 01:20:53'),
(52, 1, 'admin/auth/menu', 'POST', '127.0.0.1', '{\"_order\":\"[{\\\"id\\\":\\\"1\\\"},{\\\"id\\\":\\\"2\\\",\\\"children\\\":[{\\\"id\\\":\\\"3\\\"},{\\\"id\\\":\\\"4\\\"},{\\\"id\\\":\\\"5\\\"},{\\\"id\\\":\\\"6\\\"},{\\\"id\\\":\\\"7\\\"}]},{\\\"id\\\":\\\"8\\\",\\\"children\\\":[{\\\"id\\\":\\\"9\\\"},{\\\"id\\\":\\\"10\\\"},{\\\"id\\\":\\\"11\\\"},{\\\"id\\\":\\\"12\\\"}]},{\\\"id\\\":\\\"13\\\"},{\\\"id\\\":\\\"14\\\"}]\",\"_token\":\"a9vFU60OCmYghiUZIKl0EZyp9x2u4w6okroMknkK\"}', '2024-11-19 01:20:57', '2024-11-19 01:20:57'),
(53, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:20:57', '2024-11-19 01:20:57'),
(54, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:20:59', '2024-11-19 01:20:59'),
(55, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:21:01', '2024-11-19 01:21:01'),
(56, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:21:03', '2024-11-19 01:21:03'),
(57, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:22:08', '2024-11-19 01:22:08'),
(58, 1, 'admin/recesos', 'GET', '127.0.0.1', '[]', '2024-11-19 01:22:11', '2024-11-19 01:22:11'),
(59, 1, 'admin/recesos', 'GET', '127.0.0.1', '[]', '2024-11-19 01:22:13', '2024-11-19 01:22:13'),
(60, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:22:23', '2024-11-19 01:22:23'),
(61, 1, 'admin/auth/menu', 'POST', '127.0.0.1', '{\"parent_id\":\"0\",\"search_terms\":null,\"title\":\"modificador-visitas\",\"icon\":\"icon-user-cog\",\"uri\":\"modificador-visitas\",\"roles\":[\"1\",null],\"permission\":\"*\",\"_token\":\"a9vFU60OCmYghiUZIKl0EZyp9x2u4w6okroMknkK\"}', '2024-11-19 01:23:04', '2024-11-19 01:23:04'),
(62, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:23:04', '2024-11-19 01:23:04'),
(63, 1, 'admin/auth/menu', 'POST', '127.0.0.1', '{\"_order\":\"[{\\\"id\\\":\\\"1\\\"},{\\\"id\\\":\\\"2\\\",\\\"children\\\":[{\\\"id\\\":\\\"3\\\"},{\\\"id\\\":\\\"4\\\"},{\\\"id\\\":\\\"5\\\"},{\\\"id\\\":\\\"6\\\"},{\\\"id\\\":\\\"7\\\"}]},{\\\"id\\\":\\\"8\\\",\\\"children\\\":[{\\\"id\\\":\\\"9\\\"},{\\\"id\\\":\\\"10\\\"},{\\\"id\\\":\\\"11\\\"},{\\\"id\\\":\\\"12\\\"}]},{\\\"id\\\":\\\"13\\\"},{\\\"id\\\":\\\"15\\\"},{\\\"id\\\":\\\"14\\\"}]\",\"_token\":\"a9vFU60OCmYghiUZIKl0EZyp9x2u4w6okroMknkK\"}', '2024-11-19 01:23:13', '2024-11-19 01:23:13'),
(64, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:23:14', '2024-11-19 01:23:14'),
(65, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:23:15', '2024-11-19 01:23:15'),
(66, 1, 'admin/modificador-visitas', 'GET', '127.0.0.1', '[]', '2024-11-19 01:23:22', '2024-11-19 01:23:22'),
(67, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:23:29', '2024-11-19 01:23:29'),
(68, 1, 'admin/auth/menu', 'POST', '127.0.0.1', '{\"parent_id\":\"0\",\"search_terms\":null,\"title\":\"modificador-recesos\",\"icon\":\"icon-user-clock\",\"uri\":\"modificador-recesos\",\"roles\":[\"1\",null],\"permission\":\"*\",\"_token\":\"a9vFU60OCmYghiUZIKl0EZyp9x2u4w6okroMknkK\"}', '2024-11-19 01:23:46', '2024-11-19 01:23:46'),
(69, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:23:47', '2024-11-19 01:23:47'),
(70, 1, 'admin/auth/menu', 'POST', '127.0.0.1', '{\"_order\":\"[{\\\"id\\\":\\\"1\\\"},{\\\"id\\\":\\\"2\\\",\\\"children\\\":[{\\\"id\\\":\\\"3\\\"},{\\\"id\\\":\\\"4\\\"},{\\\"id\\\":\\\"5\\\"},{\\\"id\\\":\\\"6\\\"},{\\\"id\\\":\\\"7\\\"}]},{\\\"id\\\":\\\"8\\\",\\\"children\\\":[{\\\"id\\\":\\\"9\\\"},{\\\"id\\\":\\\"10\\\"},{\\\"id\\\":\\\"11\\\"},{\\\"id\\\":\\\"12\\\"}]},{\\\"id\\\":\\\"13\\\"},{\\\"id\\\":\\\"15\\\"},{\\\"id\\\":\\\"14\\\"},{\\\"id\\\":\\\"16\\\"}]\",\"_token\":\"a9vFU60OCmYghiUZIKl0EZyp9x2u4w6okroMknkK\"}', '2024-11-19 01:23:52', '2024-11-19 01:23:52'),
(71, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:23:52', '2024-11-19 01:23:52'),
(72, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:23:53', '2024-11-19 01:23:53'),
(73, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:23:54', '2024-11-19 01:23:54'),
(74, 1, 'admin/modificador-recesos', 'GET', '127.0.0.1', '[]', '2024-11-19 01:23:56', '2024-11-19 01:23:56'),
(75, 1, 'admin/modificador-recesos/3/edit', 'GET', '127.0.0.1', '[]', '2024-11-19 01:24:02', '2024-11-19 01:24:02'),
(76, 1, 'admin/modificador-recesos/3', 'PUT', '127.0.0.1', '{\"label\":\"Duraci\\u00f3n del Receso\",\"name\":\"recesoDuration\",\"type\":\"select\",\"search_terms\":null,\"options\":\"[\\\"15 minutos\\\"]\",\"required\":\"1\",\"required_cb\":\"on\",\"_token\":\"a9vFU60OCmYghiUZIKl0EZyp9x2u4w6okroMknkK\",\"_method\":\"PUT\"}', '2024-11-19 01:24:08', '2024-11-19 01:24:08'),
(77, 1, 'admin/modificador-recesos', 'GET', '127.0.0.1', '[]', '2024-11-19 01:24:08', '2024-11-19 01:24:08'),
(78, 1, 'admin/auth/permissions', 'GET', '127.0.0.1', '[]', '2024-11-19 01:24:16', '2024-11-19 01:24:16'),
(79, 1, 'admin/auth/menu', 'GET', '127.0.0.1', '[]', '2024-11-19 01:24:22', '2024-11-19 01:24:22'),
(80, 1, 'admin', 'GET', '192.168.3.247', '[]', '2024-11-26 02:30:09', '2024-11-26 02:30:09'),
(81, 1, 'admin/recesos', 'GET', '192.168.3.247', '[]', '2024-11-26 02:30:19', '2024-11-26 02:30:19'),
(82, 1, 'admin', 'GET', '192.168.3.247', '[]', '2024-11-26 02:31:58', '2024-11-26 02:31:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `http_method` varchar(191) DEFAULT NULL,
  `http_path` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_permissions`
--

INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES
(1, 'All permission', '*', '', '*', NULL, NULL),
(2, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL),
(3, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL),
(4, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL),
(5, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL),
(6, 'Admin helpers', 'ext.helpers', '', '/helpers/*', '2024-11-08 02:08:22', '2024-11-08 02:08:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_roles`
--

INSERT INTO `admin_roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'administrator', '2024-11-08 02:06:03', '2024-11-08 02:06:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_role_menu`
--

CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_role_menu`
--

INSERT INTO `admin_role_menu` (`role_id`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL),
(1, 13, NULL, NULL),
(1, 14, NULL, NULL),
(1, 15, NULL, NULL),
(1, 16, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_role_permissions`
--

CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_role_permissions`
--

INSERT INTO `admin_role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_role_users`
--

CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_role_users`
--

INSERT INTO `admin_role_users` (`role_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL),
(1, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(190) NOT NULL,
  `password` varchar(60) NOT NULL,
  `name` varchar(191) NOT NULL,
  `avatar` varchar(191) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$1fOukCgjs9eYpiq5HjLnfe2A4JraDIBlmggTUukSIDWzg9ls.gxtu', 'Administrator', NULL, NULL, '2024-11-08 02:06:02', '2024-11-08 02:06:02'),
(2, 'johan', '$2y$12$g20vnuLZ4ckUSArfKIUFh.O9povnKwbhbhM15lVpVEGNk6DKKXI/6', 'johan', 'images/images.jfif', NULL, '2024-11-13 03:33:18', '2024-11-13 03:33:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_user_permissions`
--

CREATE TABLE `admin_user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_user_permissions`
--

INSERT INTO `admin_user_permissions` (`user_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(2, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `app_users`
--

CREATE TABLE `app_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `app_users`
--

INSERT INTO `app_users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'johan', 'johan1@gmail.com', '$2y$12$Xp.lDUQvvY9V/QgbgHxINuysII.hRefInGOhUX5R8Z2QzvPsZixTW', NULL, '2024-11-13 02:27:52'),
(4, 'johan1', 'johan12@gmail.com', '$2y$10$x4D3tEvF4fuBq.OTYIwWaeYZfKMQsviR.7BLnL.5zqn24vjE3De7G', NULL, NULL),
(5, 'vigilante', 'vigilante1@gmail.com', '$2y$12$L37z1Ut6tz/wTbCpmYylr.OJ38Z0EQ2LzM3tH3oH0NmYqEjj/G/BG', NULL, '2024-11-13 03:53:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2016_01_04_173148_create_admin_tables', 2),
(5, '2024_11_05_223930_create_visitas_table', 3),
(6, '2024_11_06_044556_create_recesos_table', 3),
(7, '2024_11_06_045723_create_trabajadores_table', 3),
(8, '2024_11_12_212318_create_app_users_table', 4),
(9, '2024_11_13_051326_create_visita_fields_table', 5),
(10, '2024_11_14_033017_create_receso_fields_table', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recesos`
--

CREATE TABLE `recesos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trabajador_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  `hora_receso` datetime NOT NULL DEFAULT current_timestamp(),
  `hora_vuelta` time DEFAULT NULL,
  `estado` enum('activo','finalizado') NOT NULL DEFAULT 'activo',
  `exceso` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recesos`
--

INSERT INTO `recesos` (`id`, `trabajador_id`, `nombre`, `dni`, `duracion`, `hora_receso`, `hora_vuelta`, `estado`, `exceso`, `created_at`, `updated_at`) VALUES
(22, 32, 'ABAD CAMPOS JOHANN CRISTOPHER', '71924247', 300, '2024-11-11 17:52:40', '17:53:02', 'finalizado', 285, NULL, NULL),
(23, 32, 'ABAD CAMPOS JOHANN CRISTOPHER', '71924247', 301, '2024-11-11 17:56:18', '17:56:53', 'finalizado', 286, NULL, NULL),
(24, 32, 'ABAD CAMPOS JOHANN CRISTOPHER', '71924247', 15, '2024-11-11 17:58:55', '17:59:12', 'finalizado', 285, NULL, NULL),
(25, 50, 'AVELINO MARTIN HEYDY BER', '74071171', 15, '2024-11-21 15:29:21', '15:29:28', 'finalizado', 0, NULL, NULL),
(26, 50, 'AVELINO MARTIN HEYDY BER', '74071171', 50, '2024-11-25 15:48:58', '15:49:32', 'finalizado', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receso_fields`
--

CREATE TABLE `receso_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `receso_fields`
--

INSERT INTO `receso_fields` (`id`, `label`, `name`, `type`, `options`, `required`, `created_at`, `updated_at`) VALUES
(1, 'DNI', 'dniWorker', 'text', NULL, 1, '2024-11-14 08:54:19', '2024-11-14 08:54:19'),
(2, 'Nombre del Trabajador', 'worker_name', 'text', NULL, 1, '2024-11-14 08:54:19', '2024-11-14 14:33:36'),
(3, 'Duración del Receso', 'recesoDuration', 'select', '[\"15 minutos\"]', 1, '2024-11-14 08:54:19', '2024-11-19 01:24:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('bQKIrBzHsMqd9jpidbYqrlNUAfWfUrKw4ioFem0N', 1, '192.168.3.247', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMlJCcDB3bXZqTnR0TWZtSXl4bnY4TFhvYlhVOUd6M0t1RGJ0aGh5SyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xOTIuMTY4LjMuMjQ3OjgwMDAvdmlzaXRhcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo1MjoibG9naW5fYWRtaW5fNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1732570322);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `dni` varchar(8) NOT NULL,
  `hora_receso` time DEFAULT NULL,
  `hora_vuelta` time DEFAULT NULL,
  `duracion` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`id`, `nombre`, `dni`, `hora_receso`, `hora_vuelta`, `duracion`, `created_at`, `updated_at`) VALUES
(32, 'ABAD CAMPOS JOHANN CRISTOPHER', '71924247', '17:58:55', '17:59:12', 15, NULL, NULL),
(33, 'ACOSTA CRESPO JESUS', '41159073', NULL, NULL, NULL, NULL, NULL),
(34, 'ALARCON ARANDA MICHEL SILVESTRE', '77057319', NULL, NULL, NULL, NULL, NULL),
(35, 'ALBORNOZ ROSAS COAKLEY ARNALDO', '42152820', NULL, NULL, NULL, NULL, NULL),
(36, 'ALBORNOZ SOTO KATTERINE VANESSA', '46058052', NULL, NULL, NULL, NULL, NULL),
(37, 'ALEGRIA PAREDES JAMEST', '22407176', NULL, NULL, NULL, NULL, NULL),
(38, 'ALTAMIRANO HUAMA SEGUNDO ANTONIO', '43311221', NULL, NULL, NULL, NULL, NULL),
(39, 'ALVARADO ORTEGA OSMIDER', '22403615', NULL, NULL, NULL, NULL, NULL),
(40, 'ALVARADO ROSAS GABRIELA DENY', '71537865', '17:05:31', '17:36:04', 15, NULL, NULL),
(41, 'ALVARADO SANTILLAN ROGER', '22513296', NULL, NULL, NULL, NULL, NULL),
(42, 'ALVAREZ LAZARO LIVIO SANTIAGO', '40766179', NULL, NULL, NULL, NULL, NULL),
(43, 'ANAYA ALVARADO DELFINA', '47430638', NULL, NULL, NULL, NULL, NULL),
(44, 'APAZA CAPIA RAUL', '02424849', NULL, NULL, NULL, NULL, NULL),
(45, 'APOLINARIO MORALES JOSUE NILER', '75771534', NULL, NULL, NULL, NULL, NULL),
(46, 'ARANDA FLORES GENESIS BIANCA', '62054804', NULL, NULL, NULL, NULL, NULL),
(47, 'ASENCIO RAYMUNDO GIANMARCO YONATAN', '71665858', NULL, NULL, NULL, NULL, NULL),
(48, 'ASTO RIVERA KAREN YESMIN', '71305787', NULL, NULL, NULL, NULL, NULL),
(49, 'ATENCIO VILCA DAVID EDUARDO', '73241533', NULL, NULL, NULL, NULL, NULL),
(50, 'AVELINO MARTIN HEYDY BER', '74071171', '15:48:58', '15:49:32', 50, NULL, NULL),
(51, 'AVILA ROJAS ETHEL JESUS', '46424805', NULL, NULL, NULL, NULL, NULL),
(52, 'BASILIO CISNEROS YULY OLINDA', '76641310', NULL, NULL, NULL, NULL, NULL),
(53, 'BENDEZU ROMERO PAULINA MARGOT', '20040778', NULL, NULL, NULL, NULL, NULL),
(54, 'BERNA VENTURO JESSENIA DEL PILAR', '48072211', '18:07:02', '18:17:41', 15, NULL, NULL),
(55, 'BERNAL MEZA VERONIKA', '40359835', NULL, NULL, NULL, NULL, NULL),
(56, 'BERRIOS CACHAY JESUS', '43377884', NULL, NULL, NULL, NULL, NULL),
(57, 'BRAVO JARA SONIA', '41383432', NULL, NULL, NULL, NULL, NULL),
(58, 'BURGA SAAVEDRA FERNANDO JOHN', '16444088', NULL, NULL, NULL, NULL, NULL),
(59, 'CABRERA MANZANO SOFIA OLGA', '22400040', NULL, NULL, NULL, NULL, NULL),
(60, 'CABRERA MENESES JORGE LUIS', '47258302', NULL, NULL, NULL, NULL, NULL),
(61, 'CAJALEON COTRINA FLAVIO', '22499417', NULL, NULL, NULL, NULL, NULL),
(62, 'CAJAS BARRUETA KEILA YOCET', '75841000', NULL, NULL, NULL, NULL, NULL),
(63, 'CAMARA AMASIFUEN CARLOS ENRIQUE', '70061523', '18:07:26', '19:22:07', 15, NULL, NULL),
(64, 'CARRENO SONO ROMULO FERNANDO', '22422080', NULL, NULL, NULL, NULL, NULL),
(65, 'CASIMIRO BENANCIO DEMETRIO', '43033709', NULL, NULL, NULL, NULL, NULL),
(66, 'CESPEDES CRUZ LYLIAM LUZ', '22513992', NULL, NULL, NULL, NULL, NULL),
(67, 'CESPEDES ROLDAN JAIMES DIMAS', '22402570', NULL, NULL, NULL, NULL, NULL),
(68, 'CHAHUA SILVA YAKELIN ARMANDINA', '44342680', NULL, NULL, NULL, NULL, NULL),
(69, 'CHAMORRO VISCAYA DORIS MIRYAM', '22403612', NULL, NULL, NULL, NULL, NULL),
(70, 'CHAVEZ VIN CARLOS GROVER', '40377905', NULL, NULL, NULL, NULL, NULL),
(71, 'CHUQUIYAURI ATENCIO SUSAN', '41909008', NULL, NULL, NULL, NULL, NULL),
(72, 'COPELLO QUINTANA WILLIAM', '22475728', NULL, NULL, NULL, NULL, NULL),
(73, 'CORDOVA CABELLO TANIA', '46773942', NULL, NULL, NULL, NULL, NULL),
(74, 'CORONEL ALVAREZ RONALD ORBAL', '44062785', NULL, NULL, NULL, NULL, NULL),
(75, 'COTRINA TARAZONA ISOLINA DORIS', '22642886', NULL, NULL, NULL, NULL, NULL),
(76, 'COZ FELIX ELIZABETH', '09608004', NULL, NULL, NULL, NULL, NULL),
(77, 'CRUZ MEJIA ERICK PATRICK', '74979993', NULL, NULL, NULL, NULL, NULL),
(78, 'CRUZ VENANCIO MIGUEL ANGEL', '22517037', NULL, NULL, NULL, NULL, NULL),
(79, 'CUELLO ZELAYA YSABEL GENOVEVA', '22509407', NULL, NULL, NULL, NULL, NULL),
(80, 'CUEVA GALIANO MARCIA REGINA', '70015116', NULL, NULL, NULL, NULL, NULL),
(81, 'ESPINOZA GARAY JOSE LUIS', '22408351', NULL, NULL, NULL, NULL, NULL),
(82, 'ESPINOZA GRADOS EMILIANA DOLORES', '46993725', NULL, NULL, NULL, NULL, NULL),
(83, 'ESPINOZA SOLANO PATRICIA', '72120410', NULL, NULL, NULL, NULL, NULL),
(84, 'FALCON OSORIO OLINDA OBDULIA', '22750248', NULL, NULL, NULL, NULL, NULL),
(85, 'FELIX UTUS ADERLI ANTHONY', '77236849', NULL, NULL, NULL, NULL, NULL),
(86, 'FERRER CANCHUMANTA EDIMIR PEDRO', '47341518', '18:05:07', '18:07:07', 15, NULL, NULL),
(87, 'FIGUEREDO ARANDA CARLOS ABNER', '22500044', NULL, NULL, NULL, NULL, NULL),
(88, 'FIGUEREDO CARDENAS ALEJANDRA MARIA', '73671675', NULL, NULL, NULL, NULL, NULL),
(89, 'FIGUEROA SANCHEZ JIM JAMES', '22497400', '18:18:10', '18:19:08', 15, NULL, NULL),
(90, 'GALLARDO DIAZ LINDA ESTEFANI', '47281581', NULL, NULL, NULL, NULL, NULL),
(91, 'GARCIA RAMIREZ JHONN JIMY', '45580686', NULL, NULL, NULL, NULL, NULL),
(92, 'GASPAR LAZARO JENNY ELIZABETH', '22530215', NULL, NULL, NULL, NULL, NULL),
(93, 'GASPAR LAZARO WENDY LISBETH', '71892435', NULL, NULL, NULL, NULL, NULL),
(94, 'GAYOSO RAMOS MADELIN', '22481583', NULL, NULL, NULL, NULL, NULL),
(95, 'GONZALES SANTIAGO JOCSAN ELIAS', '73368358', NULL, NULL, NULL, NULL, NULL),
(96, 'HERRERA LLANOS ARIANY ARACELY', '71919312', NULL, NULL, NULL, NULL, NULL),
(97, 'HERRERA RENGIFO YULIANA', '40518241', NULL, NULL, NULL, NULL, NULL),
(98, 'HIDALGO CONCEPCION BERSY ALEJANDRINA', '40472843', NULL, NULL, NULL, NULL, NULL),
(99, 'HIDALGO GALAN RUBY FABIOLA', '70776110', NULL, NULL, NULL, NULL, NULL),
(100, 'HIDALGO HUAMAN WILSON S', '22513146', NULL, NULL, NULL, NULL, NULL),
(101, 'HUAYANAY FERNANDEZ SENOVIO', '40449772', NULL, NULL, NULL, NULL, NULL),
(102, 'HUAYNATE ORTEGA EDWIN', '04018445', NULL, NULL, NULL, NULL, NULL),
(103, 'JANAMPA CANO JUANA CONSUELO', '22408346', NULL, NULL, NULL, NULL, NULL),
(104, 'JARA SILVA ANTHONY KENNETH', '80158594', NULL, NULL, NULL, NULL, NULL),
(105, 'JIMENEZ CARRION ALEJANDRO', '72098585', NULL, NULL, NULL, NULL, NULL),
(106, 'LEIVA TORRES JOSE ARMANDO', '20008667', NULL, NULL, NULL, NULL, NULL),
(107, 'LEON CHAMOLI SHERLY ANDREA', '45903963', NULL, NULL, NULL, NULL, NULL),
(108, 'LIMAYLLA CECILIO JOEL PATRICK', '72450588', NULL, NULL, NULL, NULL, NULL),
(109, 'LINO MUNGUIA CANDY JANET', '45322747', NULL, NULL, NULL, NULL, NULL),
(110, 'LLANOS DOSANTOS LENING', '43499689', NULL, NULL, NULL, NULL, NULL),
(111, 'LOPEZ LLANOS CAMILO FRANKLIN', '22472391', NULL, NULL, NULL, NULL, NULL),
(112, 'LOPEZ PAJUELO ALDO ELIAS', '22474283', NULL, NULL, NULL, NULL, NULL),
(113, 'LOPEZ RODRIGUEZ SONIA MARIA', '00845365', '21:11:24', '16:11:27', 15, NULL, NULL),
(114, 'LOPEZ SANCHEZ MARIA LUISA DE PILAR', '20721481', NULL, NULL, NULL, NULL, NULL),
(115, 'LOZANO BENANCIO GREGORIO FERNANDO', '22481037', NULL, NULL, NULL, NULL, NULL),
(116, 'MALLQUI HUANCA CARLOS', '22751317', NULL, NULL, NULL, NULL, NULL),
(117, 'MEZA DURAND SOFIA', '40112011', NULL, NULL, NULL, NULL, NULL),
(118, 'NIETO FIGUEREDO ELIZABETT BERTA', '22641578', NULL, NULL, NULL, NULL, NULL),
(119, 'NOLASCO MAGARI SHEYLA MYRELLA', '72462250', NULL, NULL, NULL, NULL, NULL),
(120, 'ORTIZ VARGAS ANGELICA TARCILA', '22418247', NULL, NULL, NULL, NULL, NULL),
(121, 'PADUA AYALA JORDY ESMITH', '75900249', NULL, NULL, NULL, NULL, NULL),
(122, 'PAJUELO QUEDO CESAR ORLANDO', '22405460', NULL, NULL, NULL, NULL, NULL),
(123, 'PANDURO CONTRERAS JUDITH MARGARITA', '48163270', NULL, NULL, NULL, NULL, NULL),
(124, 'PAULINO RAMOS BELKER IVAN', '43880954', NULL, NULL, NULL, NULL, NULL),
(125, 'PENA TAPIA WILLIAM', '19944109', NULL, NULL, NULL, NULL, NULL),
(126, 'PINEDA CORDOVA LUREN', '47528721', NULL, NULL, NULL, NULL, NULL),
(127, 'QUESADA ROQUE ANGELA IVONNE', '40716809', NULL, NULL, NULL, NULL, NULL),
(128, 'QUISPE REYES GLADYS LIZ', '47317427', NULL, NULL, NULL, NULL, NULL),
(129, 'RAMIREZ RAMOS LILY ISABEL', '22422553', NULL, NULL, NULL, NULL, NULL),
(130, 'RAMIREZ VEGA ZARAI GUADALUPE', '74224713', NULL, NULL, NULL, NULL, NULL),
(131, 'RARAZ RIVERA FREDDY WELDER', '04052767', NULL, NULL, NULL, NULL, NULL),
(132, 'REMIGIO FALCON LUZ ELVA', '22488834', NULL, NULL, NULL, NULL, NULL),
(133, 'REYMUNDEZ SANCHEZ AMADEO', '42661858', NULL, NULL, NULL, NULL, NULL),
(134, 'RIOS IBA CLAUDIA SILVANA', '42094516', NULL, NULL, NULL, NULL, NULL),
(135, 'RIVERA TORRES SILVIA ISABEL', '22498481', NULL, NULL, NULL, NULL, NULL),
(136, 'RODRIGUEZ ALVARADO JUDITH', '41358602', '15:02:42', '17:40:07', NULL, NULL, NULL),
(137, 'ROJAS TARAZONA MARCOS RODOLFO', '75661898', '16:36:46', '16:38:09', 5, NULL, NULL),
(138, 'RUBINA SOLORZANO ALEJANDRO MARCIAL', '72802969', '21:11:41', '16:11:44', 15, NULL, NULL),
(139, 'RUBIO NONTOL MARIA DE LOS ANGELES', '22407076', NULL, NULL, NULL, NULL, NULL),
(140, 'SALAZAR RODRIGUEZ ERIKA INDARA', '42000857', NULL, NULL, NULL, NULL, NULL),
(141, 'SALDIVAR LUNA PERCY LIBORIO', '46019673', NULL, NULL, NULL, NULL, NULL),
(142, 'SALVADOR PONCE POLINARIA', '22503473', NULL, NULL, NULL, NULL, NULL),
(143, 'SANCHEZ SINCHE ADRIANA NATHALIA', '40253567', NULL, NULL, NULL, NULL, NULL),
(144, 'SARMIENTO CHAUPIS ROMER', '43361898', NULL, NULL, NULL, NULL, NULL),
(145, 'SILVA SOLIS REY FAUSTO', '46000471', '17:54:39', '00:38:27', NULL, NULL, NULL),
(146, 'SIMON DIAZ BLANCA FLOR', '41711539', NULL, NULL, NULL, NULL, NULL),
(147, 'SOMONTES RAMIREZ JORGE ESTEBAN', '22481125', NULL, NULL, NULL, NULL, NULL),
(148, 'SOTO ALVARADO LUIS ALBERTO', '22465240', NULL, NULL, NULL, NULL, NULL),
(149, 'SUAREZ GONZALES GOMER JAFET', '22514250', NULL, NULL, NULL, NULL, NULL),
(150, 'SUAREZ LUGO LOURDES BENITA', '22426128', NULL, NULL, NULL, NULL, NULL),
(151, 'TACUCHE GO SECI MARIBEL', '22521312', NULL, NULL, NULL, NULL, NULL),
(152, 'TARAZONA AGUIRRE LIU CARMEL', '40383909', NULL, NULL, NULL, NULL, NULL),
(153, 'TARAZONA NEGRETE ALDO ARTURO', '22511004', NULL, NULL, NULL, NULL, NULL),
(154, 'TIXE RIVERA MARIA ELENA', '16142559', NULL, NULL, NULL, NULL, NULL),
(155, 'TORRES MUNGUIA PEDRO GERMAN', '44366428', NULL, NULL, NULL, NULL, NULL),
(156, 'TREJO LUGO TANIA ROSSY', '22489361', NULL, NULL, NULL, NULL, NULL),
(157, 'UGARTE CASTRO JUAN', '22424532', NULL, NULL, NULL, NULL, NULL),
(158, 'VALDERRAMA SALDIVAR RUFO', '43139167', NULL, NULL, NULL, NULL, NULL),
(159, 'VALENTIN TRUJILLO NESTOR', '41433931', NULL, NULL, NULL, NULL, NULL),
(160, 'VALLEJOS SILVA GRECIA MIREYA', '72793848', NULL, NULL, NULL, NULL, NULL),
(161, 'VARGAS HURTADO SEGUNDO LEONCIO', '22657742', NULL, NULL, NULL, NULL, NULL),
(162, 'VEGA ESPINOZA HOUSEN ELVIS', '71716129', NULL, NULL, NULL, NULL, NULL),
(163, 'VERA TOLENTINO LIZ CINTHIA', '61566757', NULL, NULL, NULL, NULL, NULL),
(164, 'VIVAS Y BARRUETA GLADYS MELBA', '22462631', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitas`
--

CREATE TABLE `visitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dni` varchar(8) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipopersona` varchar(50) DEFAULT NULL,
  `nomoficina` varchar(100) DEFAULT NULL,
  `smotivo` varchar(100) DEFAULT NULL,
  `lugar` varchar(100) DEFAULT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `hora_ingreso` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `visitas`
--

INSERT INTO `visitas` (`id`, `dni`, `nombre`, `tipopersona`, `nomoficina`, `smotivo`, `lugar`, `fecha`, `hora_ingreso`, `hora_salida`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, '72132601', 'JHANN MARCO CAMPOS TOLEDO', 'Entidad Publica', NULL, 'Reunion de trabajo', 'ABASTECIMIENTO', '2024-11-11', '15:10:37', '15:10:46', NULL, NULL, NULL),
(2, '12345678', 'asd', NULL, NULL, NULL, 'oficina', '2024-11-25', NULL, '15:24:12', NULL, '2024-11-26 01:24:05', '2024-11-26 01:24:12'),
(3, '12345678', 'asd', NULL, NULL, NULL, 'oficina', '2024-11-25', NULL, '15:56:30', NULL, '2024-11-26 01:56:24', '2024-11-26 01:56:30'),
(4, '71924247', 'JOHANN CRISTOPHER ABAD CAMPOS', 'Persona Natural', NULL, 'Provisión de servicios', 'ABASTECIMIENTO', '2024-11-25', '16:08:58', '16:14:38', NULL, '2024-11-26 02:08:58', '2024-11-26 02:14:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visita_fields`
--

CREATE TABLE `visita_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `visita_fields`
--

INSERT INTO `visita_fields` (`id`, `label`, `name`, `type`, `options`, `required`, `created_at`, `updated_at`) VALUES
(1, 'DNI', 'dni', 'text', NULL, 1, '2024-11-13 20:58:03', '2024-11-13 20:58:03'),
(4, 'Nombre', 'nombre', 'text', NULL, 1, '2024-11-13 21:01:17', '2024-11-13 21:01:17'),
(5, 'Tipo de Persona', 'tipopersona', 'radio', '[\"Persona Natural\", \"Entidad Publica\", \"Entidad Privada\"]', 1, '2024-11-13 21:01:17', '2024-11-13 21:01:17'),
(6, 'Oficina', 'nomoficina', 'select', '[\"SELECCIONE\", \"ABASTECIMIENTO\", \"ALMACEN\", \"ARCHIVO\",\"AUDITORIO PRINCIPAL\",\"AUDITORIO DESPACHO DIRECTORAL\",\"BIENESTAR SOCIAL\",\"CONTABILIDAD\",\"CONSTANCIA DE PAGO\",\"DIRECCION DE ASESORIA JURIDICA\",\"DIRECCION DE GESTION ADMINISTRATIVA\",\"DIRECCION DE GESTION INSTITUCIONAL\",\"DIRECCION DE GESTION PEDAGOGICA\",\"DIRECCION REGIONAL DE EDUCACIÓN-TRAMITE DOCUMENTARIO\",\"DIRECCION REGIONAL\",\"ESCALAFON\",\"ESTADISTICA\",\"INFORMATICA\",\"INFRAESTRUCTURA\",\"OFICINA DE ASESORIA JURIDICA\",\"OFICINA DE CONTROL INSTITUCIONAL\",\"PATRIMONIO\",\"PERSONAL\",\"PLANIFICACION\",\"PLANILLAS\",\"PP 051 - PTCD\",\"PP 068 - PREVAED\",\"PP 0147 - INSTITUTOS TECNOLOGICOS\",\"PP 106 - CONVIVENCIA\",\"PP 107 - ESPECIALISTA SEGUIMIENTO Y MONITOREO\",\"PRESUPUESTO\",\"PROYECTOS\",\"RACIONALIZACION\",\"RELACIONES PUBLICAS\",\"SECRETARIA GENERAL\",\"SECRETARIA TECNICA\",\"SERVICIOS GENERALES\",\"TESORERIA\"]', 1, '2024-11-13 21:01:17', '2024-11-14 10:08:46'),
(7, 'Motivo de Visita', 'smotivo', 'select', '[\"Reunión de trabajo\", \"Provisión de servicios\", \"Gestión de intereses\", \"Motivo personal\", \"Trámite documentario\", \"Otros\"]', 1, '2024-11-13 21:01:17', '2024-11-13 21:01:17'),
(8, 'Lugar', 'lugar', 'text', NULL, 1, '2024-11-13 21:01:17', '2024-11-13 21:01:17');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_menu`
--
ALTER TABLE `admin_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `admin_operation_log`
--
ALTER TABLE `admin_operation_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_operation_log_user_id_index` (`user_id`);

--
-- Indices de la tabla `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_permissions_name_unique` (`name`),
  ADD UNIQUE KEY `admin_permissions_slug_unique` (`slug`);

--
-- Indices de la tabla `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_roles_name_unique` (`name`),
  ADD UNIQUE KEY `admin_roles_slug_unique` (`slug`);

--
-- Indices de la tabla `admin_role_menu`
--
ALTER TABLE `admin_role_menu`
  ADD KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`);

--
-- Indices de la tabla `admin_role_permissions`
--
ALTER TABLE `admin_role_permissions`
  ADD KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`);

--
-- Indices de la tabla `admin_role_users`
--
ALTER TABLE `admin_role_users`
  ADD KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`);

--
-- Indices de la tabla `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_users_username_unique` (`username`);

--
-- Indices de la tabla `admin_user_permissions`
--
ALTER TABLE `admin_user_permissions`
  ADD KEY `admin_user_permissions_user_id_permission_id_index` (`user_id`,`permission_id`);

--
-- Indices de la tabla `app_users`
--
ALTER TABLE `app_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_users_username_unique` (`username`),
  ADD UNIQUE KEY `app_users_email_unique` (`email`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `recesos`
--
ALTER TABLE `recesos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `receso_fields`
--
ALTER TABLE `receso_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trabajadores_dni_unique` (`dni`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `visitas`
--
ALTER TABLE `visitas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `visita_fields`
--
ALTER TABLE `visita_fields`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_menu`
--
ALTER TABLE `admin_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `admin_operation_log`
--
ALTER TABLE `admin_operation_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `app_users`
--
ALTER TABLE `app_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `recesos`
--
ALTER TABLE `recesos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `receso_fields`
--
ALTER TABLE `receso_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `visitas`
--
ALTER TABLE `visitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `visita_fields`
--
ALTER TABLE `visita_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
