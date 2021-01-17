-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 17, 2021 at 06:25 PM
-- Server version: 5.7.24
-- PHP Version: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `achievementtracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
CREATE TABLE IF NOT EXISTS `games` (
  `gameID` int(5) NOT NULL AUTO_INCREMENT,
  `userID` int(5) NOT NULL,
  `gameName` varchar(50) NOT NULL,
  `gameAchievementCount` int(5) NOT NULL,
  `gameAchievementMax` int(5) NOT NULL,
  PRIMARY KEY (`gameID`),
  KEY `games__userID` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`gameID`, `userID`, `gameName`, `gameAchievementCount`, `gameAchievementMax`) VALUES
(3, 1, 'Crash Bandicoot 3: Warped', 26, 26),
(4, 1, 'Crash Bandicoot 2: Cortex Strikes Back', 26, 26),
(5, 1, 'Crash Bandicoot 1', 23, 24),
(12, 1, 'Hades', 19, 48),
(13, 2, 'Test Game for Second User', 12, 48),
(14, 4, 'Game for fourth user', 10, 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userID` int(5) NOT NULL AUTO_INCREMENT,
  `userName` varchar(25) NOT NULL,
  `userPassword` varchar(250) NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `userName`, `userPassword`) VALUES
(1, 'stnrtch1', '$2y$10$Krzhr2lxQ/XnpbtQOsSe5uYTvEGPTq64y54u8h.BqTI7pCwcPU70e'),
(2, 'TestUser', '$2y$10$WEKNljWigyXnJm2x6w0XyO1FWLy1xvAShhjnXd2hv7uJY46GAyWJG'),
(3, 'stnrtch2', '$2y$10$Qrr8aSRjh2/QSLyXx4vsM.mLqa9XprBg6dZNV8hqo8ytdSQ.FPzxW'),
(4, 'stnrtch3', '$2y$10$KO6WFDycwFZRLqe4b2iAKOeFbY6orRqV3ty8wRbppuKhFTxqC.oia');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games__userID` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
