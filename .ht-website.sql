-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2022 at 05:57 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `isak_login_thing`
--

-- --------------------------------------------------------

--
-- Table structure for table `appeals`
--

CREATE TABLE `appeals` (
  `id` int(11) NOT NULL,
  `appealer` int(255) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `punishment` varchar(128) NOT NULL,
  `otherreason` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `bans`
--

CREATE TABLE `bans` (
  `id` int(11) NOT NULL,
  `banner` int(255) NOT NULL,
  `target` int(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `deletedgroupmessages`
--

CREATE TABLE `deletedgroupmessages` (
  `id` int(11) NOT NULL,
  `msgid` int(255) NOT NULL,
  `message` varchar(250) NOT NULL,
  `author` int(255) NOT NULL,
  `replyTo` int(255) NOT NULL,
  `groupId` int(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `deletedmessages`
--

CREATE TABLE `deletedmessages` (
  `id` int(11) NOT NULL,
  `msgid` int(255) NOT NULL,
  `message` varchar(250) NOT NULL,
  `author` int(255) NOT NULL,
  `likes` bigint(255) NOT NULL DEFAULT 0,
  `replyTo` int(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `deletedprivatemessages`
--

CREATE TABLE `deletedprivatemessages` (
  `id` int(11) NOT NULL,
  `msgid` int(255) NOT NULL,
  `message` varchar(250) NOT NULL,
  `author` int(255) NOT NULL,
  `receiver` int(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `friendreq`
--

CREATE TABLE `friendreq` (
  `id` int(11) NOT NULL,
  `user1` int(11) NOT NULL,
  `user2` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `user1` int(11) NOT NULL,
  `user2` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `groupmessages`
--

CREATE TABLE `groupmessages` (
  `id` int(11) NOT NULL,
  `message` varchar(250) NOT NULL,
  `author` int(255) NOT NULL,
  `replyTo` int(255) NOT NULL,
  `groupId` int(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT 'Unnamed',
  `author` int(255) NOT NULL,
  `members` text NOT NULL,
  `mods` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `uid` int(255) NOT NULL,
  `targetsUid` varchar(250) NOT NULL,
  `action` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `messagelikes`
--

CREATE TABLE `messagelikes` (
  `id` int(11) NOT NULL,
  `msgid` int(250) NOT NULL,
  `userid` int(250) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `message` varchar(250) NOT NULL,
  `author` int(255) NOT NULL,
  `likes` bigint(255) NOT NULL DEFAULT 0,
  `replyTo` int(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `modsuggestions`
--

CREATE TABLE `modsuggestions` (
  `id` int(11) NOT NULL,
  `suggester` varchar(255) NOT NULL,
  `targetsUid` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mutes`
--

CREATE TABLE `mutes` (
  `id` int(11) NOT NULL,
  `muter` int(255) NOT NULL,
  `target` int(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `privatemessages`
--

CREATE TABLE `privatemessages` (
  `id` int(11) NOT NULL,
  `message` varchar(250) NOT NULL,
  `author` int(255) NOT NULL,
  `receiver` int(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `reporter` int(255) NOT NULL,
  `target` int(255) NOT NULL,
  `reason` varchar(128) NOT NULL,
  `otherreason` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(128) NOT NULL,
  `uid` varchar(128) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `rank` int(11) NOT NULL DEFAULT 0,
  `verified` int(1) NOT NULL DEFAULT 0,
  `deleted` bit(1) DEFAULT b'0',
  `deletedate` datetime DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appeals`
--
ALTER TABLE `appeals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deletedgroupmessages`
--
ALTER TABLE `deletedgroupmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deletedmessages`
--
ALTER TABLE `deletedmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deletedprivatemessages`
--
ALTER TABLE `deletedprivatemessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friendreq`
--
ALTER TABLE `friendreq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groupmessages`
--
ALTER TABLE `groupmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messagelikes`
--
ALTER TABLE `messagelikes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modsuggestions`
--
ALTER TABLE `modsuggestions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mutes`
--
ALTER TABLE `mutes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privatemessages`
--
ALTER TABLE `privatemessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
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
-- AUTO_INCREMENT for table `appeals`
--
ALTER TABLE `appeals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bans`
--
ALTER TABLE `bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deletedgroupmessages`
--
ALTER TABLE `deletedgroupmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deletedmessages`
--
ALTER TABLE `deletedmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deletedprivatemessages`
--
ALTER TABLE `deletedprivatemessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friendreq`
--
ALTER TABLE `friendreq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groupmessages`
--
ALTER TABLE `groupmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messagelikes`
--
ALTER TABLE `messagelikes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modsuggestions`
--
ALTER TABLE `modsuggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mutes`
--
ALTER TABLE `mutes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privatemessages`
--
ALTER TABLE `privatemessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
