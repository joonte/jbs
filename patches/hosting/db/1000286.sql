
ALTER TABLE `VPSOrders` ADD `IP` char(60) NULL AFTER `Login`;
-- SEPARATOR
UPDATE `VPSOrders` SET `IP` = `Login` WHERE `StatusID` = 'Active' OR `StatusID` = 'Suspended';

