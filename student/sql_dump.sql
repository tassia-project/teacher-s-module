-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 31, 2014 at 02:12 PM
-- Server version: 5.5.40
-- PHP Version: 5.4.35-0+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `student_registration_form`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `id` char(36) NOT NULL,
  `name` varchar(225) NOT NULL,
  `description` text NOT NULL,
  `credits` int(11) NOT NULL,
  `teacher` varchar(225) NOT NULL,
  `class_days` varchar(225) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `max_size` int(11) NOT NULL DEFAULT '20',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `credits`, `teacher`, `class_days`, `start_time`, `end_time`, `max_size`) VALUES
('3b69f82b-9048-11e4-bfaa-f8a96323acf8', 'CSC 309: Mobile App Dev for Android', 'Introduction to developing mobile device web sites and applications for Android. Topics include development tools, APIs, user interfaces, mobile-specific technologies and application  design.', 3, 'Dr.Styer', 'M,W,F', '12:00:00', '13:15:00', 10),
('6a691806-9047-11e4-bfaa-f8a96323acf8', 'CSC 185:  Intro to Computer Concepts', 'Prerequisite: MAT 098 or higher, or a minimum score of 22 on the mathematics portion of the ACT, or a minimum score of 510 on the math portion of the SAT. Fundamental concepts and skills needed to design computer programs using class diagrams, flowcharts, pseudo-code, and general purpose programming tools;  analysis of target problems; object-oriented design; algorithm design and verification prior to implementation.', 3, 'Dr. Chang', 'M,W,F', '08:15:00', '09:30:00', 30),
('81ea9566-9048-11e4-bfaa-f8a96323acf8', 'CSC 322: Computer Forensics II', 'Introductory course on computer forensics. Topics include digital evidence, digital forensics investigation procedure, evidence identification, data acquisition, crime scene  processing, digital forensics tools, quality assurance, evidence processing, investigation report, and court testimony.', 3, 'Dr.Li', 'M,W,F', '14:30:00', '15:45:00', 20),
('c114fd26-9047-11e4-bfaa-f8a96323acf8', 'CSC 190:Object-Oriented Programming I', 'Introduction to problem solving with computers using a object-oriented programming language. Concepts include data types, input/output, classes, control structures,  and arrays.  2 Lec/2 Lab.', 3, 'Dr.Rhee', 'T,TR', '08:15:00', '09:30:00', 10),
('e294dd60-9047-11e4-bfaa-f8a96323acf8', 'CSC 191: Object-Oriented Programming II', 'Object-oriented programming, recursion, arrays, inheritance, file input/output, exception handling, multi-thread programming, GUI, object-oriented analysis and design.  2 Lec/2 Lab.', 3, 'Dr.Styer', 'T,TR', '10:00:00', '11:15:00', 20);

-- --------------------------------------------------------

--
-- Table structure for table `course_registrations`
--

CREATE TABLE IF NOT EXISTS `course_registrations` (
  `id` char(36) NOT NULL,
  `student_id` char(36) NOT NULL,
  `course_id` char(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course_registrations`
--

INSERT INTO `course_registrations` (`id`, `student_id`, `course_id`) VALUES
('0360862e-911c-11e4-8f25-f8a96323acf8', '0351bbf8-911c-11e4-8f25-f8a96323acf8', '3b69f82b-9048-11e4-bfaa-f8a96323acf8'),
('104d252b-911d-11e4-8f25-f8a96323acf8', '103df8b3-911d-11e4-8f25-f8a96323acf8', '3b69f82b-9048-11e4-bfaa-f8a96323acf8'),
('10558994-911d-11e4-8f25-f8a96323acf8', '103df8b3-911d-11e4-8f25-f8a96323acf8', '6a691806-9047-11e4-bfaa-f8a96323acf8'),
('105989d2-911d-11e4-8f25-f8a96323acf8', '103df8b3-911d-11e4-8f25-f8a96323acf8', '81ea9566-9048-11e4-bfaa-f8a96323acf8'),
('27f817fd-911c-11e4-8f25-f8a96323acf8', '27df3f86-911c-11e4-8f25-f8a96323acf8', '3b69f82b-9048-11e4-bfaa-f8a96323acf8'),
('2eb855c4-911b-11e4-8f25-f8a96323acf8', '2ea52952-911b-11e4-8f25-f8a96323acf8', '3b69f82b-9048-11e4-bfaa-f8a96323acf8'),
('497f3478-911a-11e4-8f25-f8a96323acf8', '4974c21f-911a-11e4-8f25-f8a96323acf8', '3b69f82b-9048-11e4-bfaa-f8a96323acf8'),
('4cf858d4-911c-11e4-8f25-f8a96323acf8', '4ce9fe71-911c-11e4-8f25-f8a96323acf8', '3b69f82b-9048-11e4-bfaa-f8a96323acf8'),
('7823c293-9120-11e4-8f25-f8a96323acf8', '781973b5-9120-11e4-8f25-f8a96323acf8', '3b69f82b-9048-11e4-bfaa-f8a96323acf8'),
('782e1d12-9120-11e4-8f25-f8a96323acf8', '781973b5-9120-11e4-8f25-f8a96323acf8', 'c114fd26-9047-11e4-bfaa-f8a96323acf8'),
('ae50a31f-911c-11e4-8f25-f8a96323acf8', 'ae41c915-911c-11e4-8f25-f8a96323acf8', '3b69f82b-9048-11e4-bfaa-f8a96323acf8'),
('b4abaf3f-911b-11e4-8f25-f8a96323acf8', 'b49bb7a1-911b-11e4-8f25-f8a96323acf8', '3b69f82b-9048-11e4-bfaa-f8a96323acf8'),
('dd0204c2-911c-11e4-8f25-f8a96323acf8', 'dcf17cdd-911c-11e4-8f25-f8a96323acf8', '3b69f82b-9048-11e4-bfaa-f8a96323acf8');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `id` char(36) NOT NULL,
  `student_id` int(9) NOT NULL,
  `first_name` varchar(225) NOT NULL,
  `middle_name` varchar(225) NOT NULL,
  `last_name` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `street_address` varchar(225) NOT NULL,
  `city` varchar(225) NOT NULL,
  `state` varchar(225) NOT NULL,
  `zip` int(5) NOT NULL,
  `home_phone` int(10) NOT NULL,
  `cell_phone` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `first_name`, `middle_name`, `last_name`, `email`, `street_address`, `city`, `state`, `zip`, `home_phone`, `cell_phone`) VALUES
('0351bbf8-911c-11e4-8f25-f8a96323acf8', 123456777, 'a', 'b', 'c', 'a@b.com', '123 ABC st.', 'Berea', 'KY', 40403, 0, 0),
('103df8b3-911d-11e4-8f25-f8a96323acf8', 569874655, 'Michael-James', 'Bond', 'Parsons', 'mjay.parsons@gmail.com', '144 Mountain View Dr.', 'Berea', 'KY', 40403, 2147483647, 2147483647),
('27df3f86-911c-11e4-8f25-f8a96323acf8', 654873221, 'a', 'b', 'c', 'a@b.com', '123 ABC st.', 'Berea', 'KY', 40403, 0, 0),
('2ea52952-911b-11e4-8f25-f8a96323acf8', 546987123, 'John', 'A', 'Doe', 'john@doe.com', '144 Mountain View Dr.', 'Berea', 'KY', 40403, 0, 0),
('4974c21f-911a-11e4-8f25-f8a96323acf8', 123456789, 'John', '', 'Doe', 'john@doe.com', '123 ABC st.', 'Richmond', 'KY', 40403, 0, 0),
('4ce9fe71-911c-11e4-8f25-f8a96323acf8', 569874521, 'a', 'b', 'c', 'a@b.com', '123 ABC st.', 'Berea', 'KY', 40403, 0, 0),
('781973b5-9120-11e4-8f25-f8a96323acf8', 124456789, 'John', 'L', 'Do', 'john@doe.com', '123 ABC st.', 'Berea', 'KY', 40403, 1234567890, 1234567890),
('ae41c915-911c-11e4-8f25-f8a96323acf8', 123859632, 'a', 'b', 'c', 'a@b.com', '123 ABC st.', 'Berea', 'KY', 40403, 0, 0),
('b49bb7a1-911b-11e4-8f25-f8a96323acf8', 147852369, 'Bob', '', 'Dillin', 'bob@dillin.com', '123 ABC st.', 'Berea', 'KY', 40403, 0, 0),
('dcf17cdd-911c-11e4-8f25-f8a96323acf8', 234568987, 'a', 'b', 'c', 'a@b.com', '123 ABC st.', 'Berea', 'KY', 40403, 1234567890, 6549876);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
