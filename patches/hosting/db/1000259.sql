
ALTER TABLE `DomainsSchemes` ADD `CostTransfer` FLOAT(6,2) default '0.00' AFTER `CostProlong`;
-- SEPARATOR
UPDATE `DomainsSchemes` SET `CostTransfer` = `CostOrder` WHERE `Name` NOT IN ('ru','su','рф');
-- SEPARATOR
UPDATE `DomainsOrders` SET `StatusID` = 'ForTransfer' WHERE `StatusID` = 'OnTransfer';

