ALTER TABLE `HostingServersGroups` ADD `SortID` int(11) default '10' AFTER `Comment`;
-- SEPARATOR
ALTER TABLE `DomainsSchemes` ADD `SortID` int(11) default '10' AFTER `RegistratorID`;
-- SEPARATOR
ALTER TABLE `Registrators` ADD `SortID` int(11) default '10' AFTER `Comment`;