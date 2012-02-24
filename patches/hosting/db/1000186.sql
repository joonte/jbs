ALTER TABLE `DomainsBonuses` ADD `DomainsSchemesGroupID` int(11) NULL AFTER `SchemeID`;
-- SEPARATOR
ALTER TABLE `DomainsBonuses` ADD KEY `DomainsBonusesDomainsSchemesGroupID` (`DomainsSchemesGroupID`);
-- SEPARATOR
ALTER TABLE `DomainsBonuses` ADD CONSTRAINT `DomainsBonusesDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;