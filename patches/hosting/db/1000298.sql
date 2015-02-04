
UPDATE `Services` SET `Code` = 'Domain' WHERE `Code` = 'Domains';

-- SEPARATOR
DROP TABLE IF EXISTS `DomainsBonuses`;
-- SEPARATOR
DROP TABLE IF EXISTS `DSDomainsPolitics`;
-- SEPARATOR
DROP TABLE IF EXISTS `HostingDomainsPolitics`;
-- SEPARATOR
DROP TABLE IF EXISTS `ExtraIPDomainsPolitics`;
-- SEPARATOR
DROP TABLE IF EXISTS `ISPswDomainsPolitics`;
-- SEPARATOR
DROP TABLE IF EXISTS `DomainsSchemesGroupsItems`;
-- SEPARATOR
DROP TABLE IF EXISTS `DomainsSchemesGroups`;
-- SEPARATOR
DROP TABLE IF EXISTS `DSBonuses`;
-- SEPARATOR
DROP TABLE IF EXISTS `ExtraIPBonuses`;
-- SEPARATOR
DROP TABLE IF EXISTS `ISPswPolitics`;

-- SEPARATOR
UPDATE `Tasks` SET `TypeID` = 'DomainNoticeSuspend' WHERE `TypeID` = 'DomainsNoticeSuspend';
-- SEPARATOR
UPDATE `Tasks` SET `TypeID` = 'DomainNoticeDelete' WHERE `TypeID` = 'DomainsNoticeDelete';
-- SEPARATOR
UPDATE `Tasks` SET `TypeID` = 'DomainForDelete' WHERE `TypeID` = 'DomainsForDelete';
-- SEPARATOR
UPDATE `Tasks` SET `TypeID` = 'DomainOrdersWhoIsUpdate' WHERE `TypeID` = 'DomainsOrdersWhoIsUpdate';
-- SEPARATOR
UPDATE `StatusesHistory` SET `ModeID` = 'DomainOrders' WHERE `ModeID` = 'DomainsOrders';

-- SEPARATOR
ALTER TABLE `DomainsConsider` DROP FOREIGN KEY `DomainsConsiderDomainOrderID`;
-- SEPARATOR
ALTER TABLE `DomainsOrders` DROP FOREIGN KEY `DomainsOrdersSchemeID`;

-- SEPARATOR
ALTER TABLE `DomainsSchemes` RENAME TO `DomainSchemes`;
-- SEPARATOR
ALTER TABLE `DomainSchemes` DROP INDEX `DomainsSchemesGroupID`, ADD INDEX `DomainSchemesGroupID` (`GroupID`);
-- SEPARATOR
ALTER TABLE `DomainSchemes` DROP FOREIGN KEY `DomainsSchemesGroupID`;
-- SEPARATOR
ALTER TABLE `DomainSchemes` ADD CONSTRAINT `DomainSchemesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
-- SEPARATOR
ALTER TABLE `DomainSchemes` DROP INDEX `DomainsSchemesUserID`, ADD INDEX `DomainSchemesUserID` (`UserID`);
-- SEPARATOR
ALTER TABLE `DomainSchemes` DROP FOREIGN KEY `DomainsSchemesUserID`;
-- SEPARATOR
ALTER TABLE `DomainSchemes` ADD CONSTRAINT `DomainSchemesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
-- SEPARATOR
ALTER TABLE `DomainSchemes` DROP INDEX `DomainsSchemesRegistratorID`, ADD INDEX `DomainSchemesRegistratorID` (`RegistratorID`);
-- SEPARATOR
ALTER TABLE `DomainSchemes` DROP FOREIGN KEY `DomainsSchemesRegistratorID`;
-- SEPARATOR
ALTER TABLE `DomainSchemes` ADD CONSTRAINT `DomainSchemesRegistratorID` FOREIGN KEY (`RegistratorID`) REFERENCES `Registrators`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR
ALTER TABLE `DomainsOrders` RENAME TO `DomainOrders`;
-- SEPARATOR
ALTER TABLE `DomainOrders` DROP INDEX `DomainsOrdersOrderID`, ADD INDEX `DomainOrdersOrderID` (`OrderID`);
-- SEPARATOR
ALTER TABLE `DomainOrders` DROP FOREIGN KEY `DomainsOrdersOrderID`;
-- SEPARATOR
ALTER TABLE `DomainOrders` ADD CONSTRAINT `DomainOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
-- SEPARATOR
ALTER TABLE `DomainOrders` DROP INDEX `DomainsOrdersProfileID`, ADD INDEX `DomainOrdersProfileID` (`ProfileID`);
-- SEPARATOR
ALTER TABLE `DomainOrders` DROP FOREIGN KEY `DomainsOrdersProfileID`;
-- SEPARATOR
ALTER TABLE `DomainOrders` ADD CONSTRAINT `DomainOrdersProfileID` FOREIGN KEY (`ProfileID`) REFERENCES `Profiles`(`ID`) ON DELETE SET NULL ON UPDATE CASCADE;
-- SEPARATOR
ALTER TABLE `DomainOrders` DROP INDEX `DomainsOrdersSchemeID`, ADD INDEX `DomainOrdersSchemeID` (`SchemeID`);
-- SEPARATOR
ALTER TABLE `DomainOrders` ADD CONSTRAINT `DomainOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DomainSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR
ALTER TABLE `DomainsConsider` RENAME TO `DomainConsider`;
-- SEPARATOR
ALTER TABLE `DomainConsider` DROP INDEX `DomainsConsiderDomainOrderID`, ADD INDEX `DomainConsiderDomainOrderID` (`DomainOrderID`);
-- SEPARATOR
ALTER TABLE `DomainConsider` ADD CONSTRAINT `DomainConsiderDomainOrderID` FOREIGN KEY (`DomainOrderID`) REFERENCES `DomainOrders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;





