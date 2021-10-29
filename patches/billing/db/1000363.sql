
ALTER TABLE `Statistics` CHANGE `TableID` `TableID` char(64) DEFAULT NULL;
-- SEPARATOR
ALTER TABLE `Statistics` CHANGE `PackageID` `PackageID` char(64) DEFAULT NULL;
-- SEPARATOR
ALTER TABLE `Statistics` CHANGE `Total` `Total` int(11) DEFAULT '0';
-- SEPARATOR
ALTER TABLE `Statistics` CHANGE `Active` `Active` int(11) DEFAULT '0';
-- SEPARATOR
ALTER TABLE `Statistics` CHANGE `New` `New` int(11) DEFAULT '0';
-- SEPARATOR
ALTER TABLE `Statistics` CHANGE `Waiting` `Waiting` int(11) DEFAULT '0';
-- SEPARATOR
ALTER TABLE `Statistics` CHANGE `Suspended` `Suspended` int(11) DEFAULT '0';



