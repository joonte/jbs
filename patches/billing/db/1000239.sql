SET FOREIGN_KEY_CHECKS=0;
-- SEPARATOR
DROP TABLE IF EXISTS `ServicesFields`;
-- SEPARATOR
CREATE TABLE `ServicesFields` (
  `ID` int(11) NOT NULL auto_increment,
  `ServiceID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Prompt` text NOT NULL,
  `TypeID` varchar(255) NOT NULL,
  `Options` varchar(255) NOT NULL,
  `Default` text NOT NULL,
  `IsDuty` enum('no','yes') default 'no',
  `ValidatorID` varchar(255) NOT NULL,
  `SortID` int(11) default '10',
  PRIMARY KEY  (`ID`),
  KEY `ServicesFieldsServiceID` (`ServiceID`),
  CONSTRAINT `ServicesFieldsServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- SEPARATOR
DROP TABLE IF EXISTS `OrdersFields`;
-- SEPARATOR
CREATE TABLE `OrdersFields` (
  `ID` int(11) NOT NULL auto_increment,
  `OrderID` int(11) NOT NULL,
  `ServiceFieldID` int(11) NOT NULL,
  `Value` text NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `OrdersFieldsOrderID` (`OrderID`),
  CONSTRAINT `OrdersFieldsOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `OrdersFieldsServiceFieldID` (`ServiceFieldID`),
  CONSTRAINT `OrdersFieldsServiceFieldID` FOREIGN KEY (`ServiceFieldID`) REFERENCES `ServicesFields` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- SEPARATOR
ALTER TABLE `Orders` ADD `StatusID` char(30) default 'UnSeted' AFTER `IsPayed`;
-- SEPARATOR
ALTER TABLE `Orders` ADD `StatusDate` int(11) default '0' AFTER `StatusID`;
-- SEPARATOR
SET FOREIGN_KEY_CHECKS=1;