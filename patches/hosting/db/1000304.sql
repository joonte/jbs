
-- SEPARATOR
ALTER TABLE `ISPswSchemes` ADD `MinOrdersPeriod` INT(6) DEFAULT '0' AFTER `MaxOrders` ;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `MinOrdersPeriod` INT(6) DEFAULT '0' AFTER `MaxOrders` ;
-- SEPARATOR
ALTER TABLE `VPSSchemes` ADD `MinOrdersPeriod` INT(6) DEFAULT '0' AFTER `MaxOrders` ;
-- SEPARATOR
ALTER TABLE `DSSchemes` ADD `MinOrdersPeriod` INT(6) DEFAULT '0' AFTER `MaxOrders` ;
-- SEPARATOR
ALTER TABLE `ExtraIPSchemes` ADD `MinOrdersPeriod` INT(6) DEFAULT '0' AFTER `MaxOrders` ;
-- SEPARATOR
ALTER TABLE `DomainSchemes` ADD `MinOrdersPeriod` INT(6) DEFAULT '0' AFTER `MaxOrders`;
-- SEPARATOR
ALTER TABLE `DNSmanagerSchemes` ADD `MinOrdersPeriod` INT(6) DEFAULT '0' AFTER `MaxOrders`;


