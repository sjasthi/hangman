-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2022 at 02:04 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quotes_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `pref`
--

CREATE TABLE `pref` (
  `value` int(10) NOT NULL COMMENT 'How many rows we can have',
  `Language` varchar(15) NOT NULL COMMENT 'What language we should assume a quote is in.',
  `display` varchar(12) NOT NULL COMMENT 'puzzles, quotes or both for main page dispaly',
  `Chunks` int(1) NOT NULL DEFAULT 3,
  `ID` int(1) NOT NULL DEFAULT 1,
  `NAME` varchar(30) NOT NULL,
  `COMMENTS` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pref`
--

INSERT INTO `pref` (`value`, `Language`, `display`, `Chunks`, `ID`, `NAME`, `COMMENTS`) VALUES
(10, 'Telugu', 'Puzzles', 3, 1, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

CREATE TABLE `preferences` (
  `id` int(2) NOT NULL,
  `name` varchar(40) NOT NULL,
  `value` varchar(10) NOT NULL,
  `comments` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='for storing model and UI preferences';

--
-- Dumping data for table `preferences`
--

INSERT INTO `preferences` (`id`, `name`, `value`, `comments`) VALUES
(1, 'DEFAULT_COLUMN_COUNT', '15', 'this is the default column count for the puzzle'),
(2, 'DEFAULT_LANGUAGE', 'Telugu', 'Telugu and English are two possible values'),
(3, 'DEFAULT_HOME_PAGE_DISPLAY', 'Puzzles', 'Puzzles, Quote, Both - are three possible values'),
(4, 'DEFAULT_CHUNK_SIZE', '3', 'For  SplitQuote, how many characters in each block'),
(5, 'NO_OF_QUOTES_TO_DISPLAY', '10', 'The quotes are ordered by the ID in des order'),
(6, 'FEELING_LUCKY_MODE', 'LAST', 'Feeling Lucky --> brings up LAST, FIRST or RANDOM quote for puzzle playing'),
(7, 'FEELING_LUCKY_TYPE', 'DropQuote', 'DropQuote, FloatQuote, DropFloat, Scrambler, Splitter, Slider16 (used by Feeling Lucky)');

-- --------------------------------------------------------

--
-- Table structure for table `quote_table`
--

CREATE TABLE `quote_table` (
  `id` int(5) NOT NULL,
  `author` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topic` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quote` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quote_date` date NOT NULL,
  `quote_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quote_table`
--

INSERT INTO `quote_table` (`id`, `author`, `topic`, `quote`, `quote_date`, `quote_time`) VALUES
(280, 'Soldier', 'Migrating Coconuts', 'Are you suggesting coconuts migrate', '2022-10-20', '08:00:00'),
(281, 'సైనికుడు', 'వలస కొబ్బరికాయలు', 'కొబ్బరికాయలు వలస వెళ్లాలని మీరు సూచిస్తున్నారా', '2022-10-20', '20:00:00'),
(282, 'null', 'null', 'test no author or topic', '2022-10-21', '08:00:00'),
(283, 'null', 'null', 'null', '2022-10-21', '20:00:00'),
(286, 'test name', 'test topic', 'test phrase for testing purposes', '2022-10-22', '08:00:00'),
(287, 'a', 'a', 'a', '2022-10-22', '20:00:00'),
(288, 'sdfsf', 'sfsf', 'sfsf', '2022-10-23', '08:00:00'),
(289, 'ics499', 'slider', 'web app development is fun', '2022-10-23', '20:00:00'),
(290, 'mario', 'demo', 'everything is good', '2022-10-24', '08:00:00'),
(291, 'testing', 'testing', 'programming in php is fun', '2022-10-24', '20:00:00'),
(292, 'రచయిత', 'అంశం', 'నమూనా పదబంధం', '2022-10-25', '08:00:00'),
(293, 'author1', 'topic2', 'test phrase', '2022-10-25', '20:00:00'),
(294, 'యాదృచ్ఛిక వ్యక్తి', 'జంతువు', 'ఏనుగు', '2022-10-26', '08:00:00'),
(295, 'యాదృచ్ఛిక వ్యక్తి', 'జంతువు', 'బ్లాక్ పాంథర్', '2022-10-26', '20:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `preferences`
--
ALTER TABLE `preferences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quote_table`
--
ALTER TABLE `quote_table`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quote_table`
--
ALTER TABLE `quote_table`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=296;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
