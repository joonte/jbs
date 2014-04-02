
ALTER TABLE `VPSOrders` ADD `IP` char(60) NULL AFTER `Login`;
-- SEPARATOR
UPDATE `VPSOrders` SET `IP` = `Login` WHERE `StatusID` = 'Active' OR `StatusID` = 'Suspended';
-- SEPARATOR
ALTER TABLE `VPSSchemes` DROP `disktempl`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` ADD `blkiotune` int(9) NOT NULL AFTER `maxdesc`;

