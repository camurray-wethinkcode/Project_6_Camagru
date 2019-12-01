-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 30, 2019 at 03:37 PM
-- Server version: 8.0.17
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `socialnetwork`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `comment` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `posted_at` datetime NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `comment`, `user_id`, `posted_at`, `post_id`) VALUES
(1, 'hvjkmm', 4, '2019-11-10 18:57:24', 50),
(2, 'hgjnm', 4, '2019-11-10 18:57:34', 50),
(3, 'erdtyhb', 4, '2019-11-10 19:04:13', 50),
(4, 'retshbg', 4, '2019-11-10 19:04:48', 50),
(5, 'dfghbd', 4, '2019-11-10 19:04:50', 50),
(6, 'dfghbd', 4, '2019-11-10 19:07:52', 50),
(7, 'yhgftnbj', 4, '2019-11-10 19:07:56', 50),
(8, 'n bv', 4, '2019-11-10 19:07:58', 50),
(9, 'gfhnb', 4, '2019-11-10 19:08:00', 50),
(10, 'awesome', 5, '2019-11-22 22:38:53', 333),
(11, 'awesomepossom!', 5, '2019-11-22 22:39:00', 331),
(12, 'coolbeanz', 7, '2019-11-22 23:38:18', 331),
(13, 'yo!! :)', 4, '2019-11-24 15:30:43', 334);

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `follower_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`id`, `user_id`, `follower_id`) VALUES
(5, 4, 5),
(6, 5, 4),
(7, 4, 7),
(8, 7, 4);

-- --------------------------------------------------------

--
-- Table structure for table `login_tokens`
--

CREATE TABLE `login_tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `token` char(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `login_tokens`
--

INSERT INTO `login_tokens` (`id`, `token`, `user_id`) VALUES
(55, '1c38a684bd475d79d5eea8a8eb8aa83aee2cb872', 4),
(56, '80c8211a3675d2342eb962b2b81ea5cb1e385fa6', 4),
(57, '463a9fe97d352563e254ad630a62105d764d8a52', 4),
(58, '58830e8ca3a4b016fd3bc23bdf8d039c7083d55f', 4),
(81, 'efda34dbc4cc8aa1da811dfd98897cffd6c3f046', 4);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `body` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `sender` int(11) UNSIGNED NOT NULL,
  `receiver` int(11) UNSIGNED NOT NULL,
  `isread` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `body`, `sender`, `receiver`, `isread`) VALUES
(1, 'heeyy', 4, 4, 0),
(2, 'hi', 4, 4, 0),
(3, 'hi', 4, 4, 0),
(4, 'heeyyooo!', 7, 4, 1),
(5, 'hey', 4, 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` int(11) UNSIGNED NOT NULL,
  `receiver` int(11) UNSIGNED NOT NULL,
  `sender` int(11) UNSIGNED NOT NULL,
  `extra` text CHARACTER SET latin1 COLLATE latin1_swedish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `receiver`, `sender`, `extra`) VALUES
(1, 1, 4, 5, NULL),
(2, 1, 5, 4, NULL),
(4, 1, 4, 4, ' { \"postbody\": \"@test heeyy\" } '),
(5, 1, 4, 4, ' { \"postbody\": \"@test fuckyaaaaaaahhh\" } ');

-- --------------------------------------------------------

--
-- Table structure for table `password_tokens`
--

CREATE TABLE `password_tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `token` char(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `password_tokens`
--

INSERT INTO `password_tokens` (`id`, `token`, `user_id`) VALUES
(13, '93b66d2da94fe4d868148e1f40530ded28eacff0', 4);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) UNSIGNED NOT NULL,
  `body` varchar(160) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `posted_at` datetime NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `likes` int(11) UNSIGNED NOT NULL,
  `postimg` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `topics` varchar(400) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `body`, `posted_at`, `user_id`, `likes`, `postimg`, `topics`) VALUES
(319, 'a #a', '2019-11-22 21:36:32', 4, 0, NULL, '#a'),
(320, 'b #b', '2019-11-22 21:36:37', 4, 0, NULL, '#b'),
(321, 'c #c', '2019-11-22 21:36:40', 4, 1, NULL, '#c'),
(322, 'ab #a #b', '2019-11-22 21:36:46', 4, 0, NULL, '#a, #b'),
(323, 'abc #a #b #c', '2019-11-22 21:36:56', 4, 0, NULL, '#a, #b, #c'),
(324, 'abc #a #b #c', '2019-11-22 21:40:34', 4, 0, NULL, '#a, #b, #c'),
(325, 'abc #a #b #c', '2019-11-22 21:42:39', 4, 0, NULL, '#a, #b, #c'),
(326, 'abc #a #b #c', '2019-11-22 21:42:55', 4, 0, NULL, '#a, #b, #c'),
(327, 'abc #a #b #c', '2019-11-22 21:48:37', 4, 1, NULL, '#a, #b, #c'),
(328, 'abc #a #b #c', '2019-11-22 21:51:25', 4, 0, NULL, '#a, #b, #c'),
(329, 'abc #a #b #c', '2019-11-22 21:54:59', 4, 1, NULL, '#a, #b, #c'),
(330, 'abc #a #b #c', '2019-11-22 21:57:06', 4, 1, NULL, '#a, #b, #c'),
(331, 'fuck my life #a', '2019-11-22 22:07:59', 4, 2, NULL, '#a'),
(332, 'fuck my life #a', '2019-11-22 22:10:48', 4, 3, NULL, '#a'),
(333, 'fuck my life #a', '2019-11-22 22:12:18', 4, 3, NULL, '#a'),
(334, 'yo!', '2019-11-24 15:29:58', 7, 1, NULL, ''),
(335, 'rdlkfkgjv;wlaej', '2019-11-30 21:07:32', 4, 0, NULL, ''),
(337, 'dfzgvdsfgv', '2019-11-30 21:38:53', 4, 0, '/uploads/5de2c54d531897.41590036.jpg', ''),
(338, 'sdffvcsdcf', '2019-11-30 21:39:14', 4, 0, '/uploads/5de2c5623aaa90.73019601.jpg', ''),
(339, 'sdFCeEASFC', '2019-11-30 21:39:50', 4, 0, '/uploads/5de2c5869f54a3.92558603.png', ''),
(340, 'SDEFCEWASD', '2019-11-30 21:40:00', 4, 0, '/uploads/5de2c5902bad71.43295801.jpg', ''),
(341, 'dfzvbsdf', '2019-11-30 21:40:35', 4, 0, '/uploads/5de2c5b304d3f4.39781074.jpg', ''),
(342, 'drfgv', '2019-11-30 21:46:16', 4, 0, NULL, ''),
(343, 'ftgbh', '2019-11-30 21:46:42', 4, 0, '/uploads/5de2c72286d356.18603978.jpg', ''),
(344, 'fxgnb fg', '2019-11-30 21:49:07', 4, 0, '/uploads/5de2c7b32046e3.27307712.jpg', ''),
(345, '', '2019-11-30 23:27:28', 4, 0, NULL, ''),
(346, '', '2019-11-30 23:29:06', 4, 0, NULL, ''),
(347, '', '2019-11-30 23:34:40', 4, 0, NULL, ''),
(348, '', '2019-11-30 23:35:51', 4, 0, NULL, ''),
(349, '', '2019-11-30 23:36:38', 4, 0, NULL, ''),
(350, '', '2019-11-30 23:40:28', 4, 0, NULL, ''),
(351, '', '2019-11-30 23:41:06', 4, 0, NULL, ''),
(352, '', '2019-11-30 23:45:22', 4, 0, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) UNSIGNED NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`) VALUES
(51, 67, 4),
(52, 66, 4),
(55, 57, 4),
(57, 58, 4),
(62, 64, 4),
(64, 65, 4),
(65, 331, 4),
(66, 332, 4),
(67, 333, 4),
(69, 332, 5),
(70, 333, 5),
(71, 331, 5),
(72, 333, 7),
(73, 330, 7),
(74, 329, 7),
(75, 327, 7),
(76, 332, 7),
(77, 321, 7),
(78, 334, 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `password` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `email` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `profileimg` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `verified`, `profileimg`) VALUES
(1, 'verified', '$2y$10$pN3q5.iJlBwWKOamOnf0Y.bCoBhQHwpTJlCMyKvpVp4QoXQCa4Ivm', 'verified@verified.com', 0, NULL),
(4, 'test', '$2y$10$lkx6EVqwesTUtXa2.ZKqW.pwT/FA3TggNVds9W1QYL.hN2f0ib0XG', 'test@gmail.com', 0, 'uploads/1575156756.jpeg'),
(5, 'help', '$2y$10$c4m1dDaeVhxvEa6jhqAJtOAj/VvZbn73Fa7H.yg53olHR0oVDI/mK', 'help@gmail.com', 0, NULL),
(6, 'justin', '$2y$10$gFtqpgp9uC934hCcPlwncOElelLRF.jBttZp4rJQM3E8kkcfbtSbW', 'justin@gmail.com', 0, NULL),
(7, 'stuff', '$2y$10$x41nG2LFAibVpL4J96peruV/x4SoAbkoXsrGZAlXIxihPhJOR.0WK', 'stuff@gmail.com', 0, NULL),
(28, 'catherine', '$2y$10$VEYF7CQdUwDJdORbKetVG.lJFhaaUgRA/8.TIdUP5Ga2n4pGWJ6q6', 'murraycatherine044@gmail.com', 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_tokens`
--
ALTER TABLE `login_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_tokens`
--
ALTER TABLE `password_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `login_tokens`
--
ALTER TABLE `login_tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_tokens`
--
ALTER TABLE `password_tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=353;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
