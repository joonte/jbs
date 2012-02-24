
ALTER TABLE `HostingOrders` CHANGE `AutoProlong` `AutoProlong` ENUM('no','yes') NOT NULL DEFAULT 'yes';
-- SEPARATOR
UPDATE `HostingOrders` SET `AutoProlong`='yes' WHERE `AutoProlong`='no';
-- SEPARATOR
UPDATE `HostingOrders` SET `AutoProlong`='no' WHERE `AutoProlong`!='yes';


-- SEPARATOR
ALTER TABLE `VPSOrders` CHANGE `AutoProlong` `AutoProlong` ENUM('no','yes') NOT NULL DEFAULT 'yes';
-- SEPARATOR
UPDATE `VPSOrders` SET `AutoProlong`='yes' WHERE `AutoProlong`='no';
-- SEPARATOR
UPDATE `VPSOrders` SET `AutoProlong`='no' WHERE `AutoProlong`!='yes';


-- SEPARATOR
ALTER TABLE `DSOrders` CHANGE `AutoProlong` `AutoProlong` ENUM('no','yes') NOT NULL DEFAULT 'yes';
-- SEPARATOR
UPDATE `DSOrders` SET `AutoProlong`='yes' WHERE `AutoProlong`='no';
-- SEPARATOR
UPDATE `DSOrders` SET `AutoProlong`='no' WHERE `AutoProlong`!='yes';

