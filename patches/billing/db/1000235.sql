DROP TABLE IF EXISTS `ServicesGroups`;
-- SEPARATOR
CREATE TABLE `ServicesGroups` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL,
  `IsProtected` enum('no','yes') default 'no',
  `IsActive` enum('no','yes') default 'yes',
  `SortID` int(11) default '1',
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- SEPARATOR
INSERT INTO `ServicesGroups`
  (`ID`,`Name`,`IsProtected`,`IsActive`,`SortID`)
VALUES
(1000,'Базовые услуги','yes','yes',10);
-- SEPARATOR
ALTER TABLE `Services` ADD `GroupID` int(11) NOT NULL AFTER `ID`;
-- SEPARATOR
UPDATE `Services` SET `GroupID` = 2000000;
-- SEPARATOR
ALTER TABLE `Services` ADD `UserID` int(11) NOT NULL AFTER `GroupID`;
-- SEPARATOR
UPDATE `Services` SET `UserID` = 1;
-- SEPARATOR
ALTER TABLE `Services` ADD `ServicesGroupID` int(11) NOT NULL AFTER `UserID`;
-- SEPARATOR
UPDATE `Services` SET `ServicesGroupID` = 1000;
-- SEPARATOR
ALTER TABLE `Services` ADD `Code` char(255) default 'Services' AFTER `Name`;
-- SEPARATOR
ALTER TABLE `Services` ADD `Item` char(255) NOT NULL AFTER `Code`;
-- SEPARATOR
ALTER TABLE `Services` ADD `ConsiderTypeID` char(30) NOT NULL AFTER `Measure`;
-- SEPARATOR
ALTER TABLE `Services` ADD `Cost` float(7,2) NOT NULL AFTER `ConsiderTypeID`;
-- SEPARATOR
ALTER TABLE `Services` ADD `IsHidden` enum('no','yes') default 'no' AFTER `Cost`;
-- SEPARATOR
ALTER TABLE `Services` ADD `IsProtected` enum('no','yes') default 'no' AFTER `IsHidden`;
-- SEPARATOR
UPDATE `Services` SET `IsProtected` = 'yes';
-- SEPARATOR
ALTER TABLE `Services` ADD `SortID` int(11) default '10' AFTER `IsActive`;
-- SEPARATOR
ALTER TABLE `Services` DROP `IsShow`;
-- SEPARATOR
ALTER TABLE `Services` ADD KEY `ServicesServicesGroupID` (`ServicesGroupID`);
-- SEPARATOR
ALTER TABLE `Services` ADD CONSTRAINT `ServicesServicesGroupID` FOREIGN KEY (`ServicesGroupID`) REFERENCES `ServicesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
-- SEPARATOR
ALTER TABLE `Services` ADD KEY `ServicesGroupID` (`GroupID`);
-- SEPARATOR
ALTER TABLE `Services` ADD CONSTRAINT `ServicesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
-- SEPARATOR
ALTER TABLE `Services` ADD KEY `ServicesUserID` (`UserID`);
-- SEPARATOR
ALTER TABLE `Services` ADD CONSTRAINT `ServicesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
-- SEPARATOR
UPDATE `Services` SET `IsHidden` = 'yes', `IsProtected` = 'yes', `IsActive` = 'no' WHERE `ID` IN(1000,2000,3000);
-- SEPARATOR
UPDATE `Services` SET `Code` = 'Hosting', `Item` = 'Хостинг', `ConsiderTypeID` = 'Daily', `SortID` = 10 WHERE `ID` = 10000;
-- SEPARATOR
UPDATE `Services` SET `Code` = 'Domains', `Item` = 'Домены', `ConsiderTypeID` = 'Yearly', `SortID` = 20 WHERE `ID` = 20000;
-- SEPARATOR
UPDATE `Services` SET `Code` = 'JBs', `Item` = 'Лицензии JBs', `ConsiderTypeID` = 'Monthly', `SortID` = 20 WHERE `ID` = 100000;