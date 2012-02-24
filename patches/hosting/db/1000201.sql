ALTER TABLE `HostingSchemes` ADD `mysqluserconnectlimit` INT(12) NOT NULL AFTER `QuotaMPMworkers`;
-- SEPARATOR
ALTER TABLE `HostingSchemes`  ADD `mysqlconnectlimit` INT(12) NOT NULL AFTER `QuotaMPMworkers`;
-- SEPARATOR
ALTER TABLE `HostingSchemes`  ADD `mysqlupdateslimit` INT(12) NOT NULL AFTER `QuotaMPMworkers`;
-- SEPARATOR
ALTER TABLE `HostingSchemes`  ADD `mysqlquerieslimit` INT(12) NOT NULL AFTER `QuotaMPMworkers`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `field1` VARCHAR(255) NOT NULL ;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `field2` VARCHAR(255) NOT NULL ;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `field3` VARCHAR(255) NOT NULL ;

