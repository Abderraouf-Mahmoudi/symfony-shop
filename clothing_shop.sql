-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 20, 2025 at 06:18 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clothing_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `phone` varchar(20) DEFAULT NULL,
  `notes` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `customer_name`, `total_amount`, `status`, `created_at`, `updated_at`, `phone`, `notes`) VALUES
(2, 'Ben Romdhane Firas', 49.00, 'done', '2025-06-11 18:54:42', '2025-06-12 15:41:00', '26711439', ''),
(3, 'amir othman', 39.00, 'done', '2025-06-20 10:35:16', '2025-06-20 10:40:47', '50034045', ''),
(4, 'asdad', 39.00, 'canceled', '2025-06-20 10:56:17', '2025-06-20 10:56:25', '0387679911', ''),
(5, 'joseph gatti', 78.00, 'pending', '2025-06-20 13:35:46', NULL, '+33037875823', 'g'),
(6, 'raouf', 196.00, 'done', '2025-06-20 15:31:14', '2025-06-20 15:43:48', '50034045', '');

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `order_ref_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `size` varchar(20) DEFAULT NULL,
  `color_id` int(11) DEFAULT NULL,
  `color_name` varchar(50) DEFAULT NULL,
  `color_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`id`, `order_ref_id`, `product_id`, `quantity`, `price`, `size`, `color_id`, `color_name`, `color_code`) VALUES
(2, 2, 6, 1, 49.00, 'S', 19, 'Noir', '#000000'),
(3, 3, 5, 1, 39.00, 'L', 17, 'Bleu', '#0057b8'),
(4, 4, 5, 1, 39.00, 'L', 16, 'Noir', '#000000'),
(5, 5, 5, 2, 39.00, 'S', 16, 'Noir', '#000000'),
(6, 6, 4, 1, 69.00, 'L', 11, 'Noir', '#000000'),
(7, 6, 6, 1, 49.00, 'L', 19, 'Noir', '#000000'),
(8, 6, 2, 2, 39.00, 'S', 5, 'Noir', '#000000');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `is_new` tinyint(1) NOT NULL DEFAULT 0,
  `free_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `shipping_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `price`, `stock`, `category`, `created_at`, `updated_at`, `is_new`, `free_shipping`, `shipping_price`) VALUES
(1, 'Maillot 4 pièces', 'Maillot 2 pièces tendance avec design croisé au niveau de la taille. Disponible en plusieurs couleurs et tailles. \r\nLivraison : 8 DT.\r\nTailles disponibles : 36, 38, 40, 42, 44, 46\r\n', 35.00, 100, 'women', '2025-06-11 17:15:35', '2025-06-20 15:03:02', 0, 0, 8.00),
(2, 'Robe bretelle côtelée', 'Robe bretelle élégante avec bouton décoratif. Tissu côtelé de haute qualité, confortable et extensible.\r\n\r\nLivraison 8 DT.\r\n\r\nTailles disponibles : 36, 38, 40, 42\r\n\r\nMerci de préciser la taille souhaitée lors de la confirmation de commande.', 39.00, 98, 'women', '2025-06-11 17:17:37', '2025-06-20 14:45:40', 0, 0, 8.00),
(3, 'Robe dentelle demi-manche', 'Robe élégante en dentelle demi-manche, tissu crêpe de qualité supérieure. Style ample et confortable, parfait pour les sorties ou occasions.\r\n\r\nLivraison : 8 DT  \r\nTailles disponibles : 36, 38, 40, 42, 44, 46, 48, 50, 52\r\n\r\nMerci d’indiquer la taille souhaitée lors de la confirmation de commande.', 59.00, 100, 'women', '2025-06-11 17:19:28', NULL, 0, 0, NULL),
(4, 'Set 2 pièces tissu قمراية', 'Ensemble 2 pièces (haut + pantalon) en tissu قمراية  de qualité supérieure. Design élégant, confortable et fluide. Parfait pour les looks chics au quotidien.\r\n\r\nLivraison gratuite\r\nTailles disponibles : 36, 38, 40, 42, 44, 46, 48, 50\r\n\r\nMerci d’indiquer la taille souhaitée lors de la confirmation de commande.\r\n', 69.00, 95, 'women', '2025-06-11 17:24:37', NULL, 0, 0, NULL),
(5, 'Tenue de sport femme 2 pièces', 'Ensemble de sport 2 pièces (haut manches longues + legging). Tissu stretch confortable, idéal pour le sport ou les looks décontractés.\r\n\r\nLivraison : 8 DT  \r\nTailles disponibles : 36, 38, 40, 42\r\n\r\nMerci d’indiquer la taille souhaitée lors de la confirmation de commande.\r\n', 39.00, 99, 'women', '2025-06-11 17:37:04', '2025-06-20 12:57:36', 0, 0, NULL),
(6, 'Robe chemise boutonnée', 'Robe chemise élégante avec boutons sur toute la longueur. Style moderne, tissu fluide et confortable, idéale pour le quotidien ou les sorties.\r\n\r\nTailles disponibles : 36, 38, 40, 42, 44, 46, 48, 50, 52\r\n\r\nMerci de préciser la taille souhaitée lors de la confirmation de commande.\r\n', 49.00, 97, 'women', '2025-06-11 17:41:23', '2025-06-12 14:51:15', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_color`
--

CREATE TABLE `product_color` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_color`
--

INSERT INTO `product_color` (`id`, `product_id`, `name`, `code`) VALUES
(1, 1, 'Noir', '#000000'),
(2, 1, 'Violet', '#ae1bcb'),
(3, 1, 'Rose', '#e51a83'),
(4, 1, 'Jaune', '#fee53f'),
(5, 2, 'Noir', '#000000'),
(6, 2, 'Bleu', '#828e9b'),
(7, 2, 'Orangé', '#cd5c1c'),
(8, 2, 'Jaune', '#e0b037'),
(9, 2, 'Vert', '#5d8a7f'),
(10, 3, 'Noir', '#000000'),
(11, 4, 'Noir', '#000000'),
(12, 4, 'beige', '#c5a080'),
(13, 4, 'Vert', '#8e926a'),
(14, 4, 'Rose', '#cf988b'),
(15, 4, 'Bleu', '#829fbe'),
(16, 5, 'Noir', '#000000'),
(17, 5, 'Bleu', '#0057b8'),
(18, 5, 'Bordeaux', '#c93464'),
(19, 6, 'Noir', '#000000'),
(20, 6, 'Bleu ciel', '#95b1c8'),
(21, 6, 'Fuchsia', '#f92494');

-- --------------------------------------------------------

--
-- Table structure for table `product_image`
--

CREATE TABLE `product_image` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_image`
--

INSERT INTO `product_image` (`id`, `product_id`, `image_path`, `is_primary`, `created_at`) VALUES
(1, 1, '68499d97c648d.jpg', 0, '2025-06-11 17:15:35'),
(2, 1, '68499d97c712a.jpg', 1, '2025-06-11 17:15:35'),
(3, 1, '68499db098834.webp', 0, '2025-06-11 17:16:00'),
(4, 1, '68499db0994a9.jpg', 0, '2025-06-11 17:16:00'),
(5, 1, '68499dc3c6ce8.jpg', 0, '2025-06-11 17:16:19'),
(6, 1, '68499dc3c73fd.jpg', 0, '2025-06-11 17:16:19'),
(7, 1, '68499dd296fda.jpg', 0, '2025-06-11 17:16:34'),
(8, 1, '68499dd2975e1.webp', 0, '2025-06-11 17:16:34'),
(9, 2, '68499e1183252.jpg', 0, '2025-06-11 17:17:37'),
(10, 2, '68499e1183e91.jpg', 1, '2025-06-11 17:17:37'),
(11, 2, '68499e118437e.jpg', 0, '2025-06-11 17:17:37'),
(12, 2, '68499e298e815.jpg', 0, '2025-06-11 17:18:01'),
(13, 2, '68499e298f23f.jpg', 0, '2025-06-11 17:18:01'),
(14, 2, '68499e298f913.jpg', 0, '2025-06-11 17:18:01'),
(15, 2, '68499e4d9b10d.webp', 0, '2025-06-11 17:18:37'),
(16, 2, '68499e4d9bd23.jpg', 0, '2025-06-11 17:18:37'),
(17, 2, '68499e4d9c61b.jpg', 0, '2025-06-11 17:18:37'),
(18, 3, '68499e802904d.jpg', 1, '2025-06-11 17:19:28'),
(19, 3, '68499e80298da.jpg', 0, '2025-06-11 17:19:28'),
(20, 3, '68499e8029dbb.jpg', 0, '2025-06-11 17:19:28'),
(21, 4, '68499fb567e55.jpg', 1, '2025-06-11 17:24:37'),
(22, 4, '68499fb568ad5.jpg', 0, '2025-06-11 17:24:37'),
(23, 4, '68499fb5691f3.jpg', 0, '2025-06-11 17:24:37'),
(24, 4, '68499fb56971a.jpg', 0, '2025-06-11 17:24:37'),
(25, 4, '68499fb569c00.jpg', 0, '2025-06-11 17:24:37'),
(26, 5, '6849a2a0250c3.jpg', 1, '2025-06-11 17:37:04'),
(27, 5, '6849a2a025e27.jpg', 0, '2025-06-11 17:37:04'),
(28, 5, '6849a2a02641c.jpg', 0, '2025-06-11 17:37:04'),
(29, 6, '6849a3a30c681.jpg', 1, '2025-06-11 17:41:23'),
(30, 6, '6849a3a30d5e1.jpg', 0, '2025-06-11 17:41:23'),
(31, 6, '6849a3a30db8c.jpg', 0, '2025-06-11 17:41:23');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
(1, 'admin@badiscount.com', '[\"ROLE_ADMIN\"]', '$2y$13$KTMNr4hNSoHIyPOewAVRqO58NIwTr.yQZWZePMddYvR/l0OVMOeOy'),
(2, 'admin.maynou@badiscount.tn', '[\"ROLE_ADMIN\"]', '$2y$13$OarbvpV5ePoMzkj4.SocY.kQhsK32L.otcchkMq0nE72uLFFCSI4K');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_52EA1F09E238517C` (`order_ref_id`),
  ADD KEY `IDX_52EA1F094584665A` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_color`
--
ALTER TABLE `product_color`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C70A33B54584665A` (`product_id`);

--
-- Indexes for table `product_image`
--
ALTER TABLE `product_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_64617F034584665A` (`product_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_color`
--
ALTER TABLE `product_color`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `product_image`
--
ALTER TABLE `product_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `FK_52EA1F094584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_52EA1F09E238517C` FOREIGN KEY (`order_ref_id`) REFERENCES `order` (`id`);

--
-- Constraints for table `product_color`
--
ALTER TABLE `product_color`
  ADD CONSTRAINT `FK_C70A33B54584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Constraints for table `product_image`
--
ALTER TABLE `product_image`
  ADD CONSTRAINT `FK_64617F034584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
