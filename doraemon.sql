-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2017 at 06:28 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `doraemon`
--

-- --------------------------------------------------------

--
-- Table structure for table `employee_attendances`
--

CREATE TABLE `employee_attendances` (
  `UserId` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `ShiftId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Dumping data for table `employee_attendances`
--

INSERT INTO `employee_attendances` (`UserId`, `Date`, `Time`, `ShiftId`) VALUES
(2, '2017-09-10', '12:45:30', 4);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `Id` int(11) NOT NULL,
  `Price` int(11) NOT NULL,
  `Name` varchar(100) COLLATE utf8_vietnamese_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `Id` int(11) NOT NULL,
  `Date_start` date NOT NULL,
  `Date_end` date NOT NULL,
  `Name` varchar(100) COLLATE utf8_vietnamese_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`Id`, `Date_start`, `Date_end`, `Name`) VALUES
(1, '2017-09-04', '2017-09-10', 'Tháng 8 tuần 1');

-- --------------------------------------------------------

--
-- Table structure for table `schedule_details`
--

CREATE TABLE `schedule_details` (
  `Schedule_id` int(11) NOT NULL,
  `DayOfWeek` int(11) NOT NULL,
  `ShiftId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Dumping data for table `schedule_details`
--

INSERT INTO `schedule_details` (`Schedule_id`, `DayOfWeek`, `ShiftId`, `UserId`, `Date`) VALUES
(1, 2, 2, 2, '2017-09-04'),
(1, 3, 2, 2, '2017-09-05'),
(1, 8, 1, 2, '2017-09-10'),
(1, 8, 4, 2, '2017-09-10');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `Id` int(11) NOT NULL,
  `Name` varchar(100) COLLATE utf8_vietnamese_ci NOT NULL,
  `Time_start` time NOT NULL,
  `Time_end` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`Id`, `Name`, `Time_start`, `Time_end`) VALUES
(1, 'Ca sáng', '07:30:00', '12:00:00'),
(2, 'Ca Chiều', '15:00:00', '18:00:00'),
(3, 'Ca Tối', '18:00:00', '22:30:00'),
(4, 'Ca trưa', '12:00:00', '15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `storages`
--

CREATE TABLE `storages` (
  `Id` int(11) NOT NULL,
  `Name` varchar(100) COLLATE utf8_vietnamese_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `storage_contents`
--

CREATE TABLE `storage_contents` (
  `Id` int(11) NOT NULL,
  `StorageId` int(11) NOT NULL,
  `ProductId` int(11) NOT NULL,
  `Amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `storage_export_history`
--

CREATE TABLE `storage_export_history` (
  `Id` int(11) NOT NULL,
  `StorageId` int(11) NOT NULL,
  `ProductId` int(11) NOT NULL,
  `Export_date` date NOT NULL,
  `Amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `storage_import_history`
--

CREATE TABLE `storage_import_history` (
  `Id` int(11) NOT NULL,
  `StorageId` int(11) NOT NULL,
  `ProductId` int(11) NOT NULL,
  `Import_date` date NOT NULL,
  `Amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL,
  `UserName` varchar(30) COLLATE utf8_vietnamese_ci NOT NULL,
  `PassWord` varchar(40) COLLATE utf8_vietnamese_ci NOT NULL,
  `DisplayName` varchar(50) COLLATE utf8_vietnamese_ci NOT NULL,
  `Role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Id`, `UserName`, `PassWord`, `DisplayName`, `Role`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', 1),
(2, 'tung', '0f043c901ac151f0e881bb1428b7d8af', 'Minh Tùng', 2),
(3, 'thao', 'bf32d197f35684b9c075b9eb9823ee0c', 'Cô giáo thảo', 2);

-- --------------------------------------------------------

--
-- Table structure for table `wages`
--

CREATE TABLE `wages` (
  `Id` int(11) NOT NULL,
  `Amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wage_list`
--

CREATE TABLE `wage_list` (
  `UserId` int(11) NOT NULL,
  `WageId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employee_attendances`
--
ALTER TABLE `employee_attendances`
  ADD KEY `Fk_User_work_Id` (`UserId`),
  ADD KEY `Fk_Shift_work_Id` (`ShiftId`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `schedule_details`
--
ALTER TABLE `schedule_details`
  ADD KEY `Fk_ScheduleId` (`Schedule_id`),
  ADD KEY `Fk_ShiftId` (`ShiftId`),
  ADD KEY `Fk_UserId` (`UserId`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `storages`
--
ALTER TABLE `storages`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `storage_contents`
--
ALTER TABLE `storage_contents`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Fk_storageId` (`StorageId`),
  ADD KEY `Fk_ProductId` (`ProductId`);

--
-- Indexes for table `storage_export_history`
--
ALTER TABLE `storage_export_history`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Fk_storageId` (`StorageId`),
  ADD KEY `Fk_ProductId` (`ProductId`);

--
-- Indexes for table `storage_import_history`
--
ALTER TABLE `storage_import_history`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Fk_storageId` (`StorageId`),
  ADD KEY `Fk_ProductId` (`ProductId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `wages`
--
ALTER TABLE `wages`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `wage_list`
--
ALTER TABLE `wage_list`
  ADD KEY `Fk_User_wage_Id` (`UserId`),
  ADD KEY `Fk_Wage_wage_Id` (`WageId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `storages`
--
ALTER TABLE `storages`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `storage_export_history`
--
ALTER TABLE `storage_export_history`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `storage_import_history`
--
ALTER TABLE `storage_import_history`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `wages`
--
ALTER TABLE `wages`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee_attendances`
--
ALTER TABLE `employee_attendances`
  ADD CONSTRAINT `Fk_Shift_work_Id` FOREIGN KEY (`ShiftId`) REFERENCES `shifts` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Fk_User_work_Id` FOREIGN KEY (`UserId`) REFERENCES `users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedule_details`
--
ALTER TABLE `schedule_details`
  ADD CONSTRAINT `Fk_ScheduleId` FOREIGN KEY (`Schedule_id`) REFERENCES `schedules` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Fk_ShiftId` FOREIGN KEY (`ShiftId`) REFERENCES `shifts` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Fk_UserId` FOREIGN KEY (`UserId`) REFERENCES `users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `storage_contents`
--
ALTER TABLE `storage_contents`
  ADD CONSTRAINT `Fk_Product_storageContent_Id` FOREIGN KEY (`ProductId`) REFERENCES `products` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Fk_storage_storageContent_Id` FOREIGN KEY (`StorageId`) REFERENCES `storages` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `storage_export_history`
--
ALTER TABLE `storage_export_history`
  ADD CONSTRAINT `Fk_Product_export_Id` FOREIGN KEY (`ProductId`) REFERENCES `products` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Fk_storage_export_Id` FOREIGN KEY (`StorageId`) REFERENCES `storages` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `storage_import_history`
--
ALTER TABLE `storage_import_history`
  ADD CONSTRAINT `Fk_Product_import_Id` FOREIGN KEY (`ProductId`) REFERENCES `products` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Fk_storage_import_Id` FOREIGN KEY (`StorageId`) REFERENCES `storages` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wage_list`
--
ALTER TABLE `wage_list`
  ADD CONSTRAINT `Fk_User_wage_Id` FOREIGN KEY (`UserId`) REFERENCES `users` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Fk_Wage_wage_Id` FOREIGN KEY (`WageId`) REFERENCES `wages` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
