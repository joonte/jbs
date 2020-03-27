SET FOREIGN_KEY_CHECKS=0;

--
-- Table structure for table `Config`
--

DROP TABLE IF EXISTS `Config`;
CREATE TABLE `Config` (
  `ID` int(11) NOT NULL auto_increment,
  `HostID` char(50) NOT NULL,
  `Param` char(255) NOT NULL,
  `Value` text CHARACTER SET utf8mb4,
  PRIMARY KEY  (`ID`),
  KEY (`Param`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS=1;
