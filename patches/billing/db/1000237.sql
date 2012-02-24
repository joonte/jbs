DROP TABLE IF EXISTS `ServicesFields`;
-- SEPARATOR
CREATE TABLE `ServicesFields` (
  `ID` int(11) NOT NULL auto_increment,
  `ServiceID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `TypeID` varchar(255) NOT NULL,
  `ValidatorID` varchar(255) NOT NULL,
  `Options` varchar(255) NOT NULL,
  `Length` int(11) NOT NULL,
  `Default` text NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `ServicesFieldsServiceID` (`ServiceID`),
  CONSTRAINT `ServicesFieldsServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;