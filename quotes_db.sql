-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2020 at 11:20 PM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
  `quote` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quote_table`
--

INSERT INTO `quote_table` (`id`, `author`, `topic`, `quote`) VALUES

(1, 'ics499', 'slider', 'web app development is fun'),
(2, 'mario', 'demo', 'everything is good'),
(3, 'testing', 'testing', 'programming in php is fun'),
(4, 'test', 'test', 'test'),
(5, 'junk2', 'junk', 'junk'),
(6, 'junk2', 'junk', 'sample quote');

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
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
