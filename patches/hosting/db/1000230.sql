
UPDATE `Orders` SET
	`IsAutoProlong`=(SELECT `HostingOrders`.`AutoProlong` FROM `HostingOrders` WHERE `HostingOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=10000;

-- SEPARATOR

UPDATE `Orders` SET
	`IsAutoProlong`=(SELECT `VPSOrders`.`AutoProlong` FROM `VPSOrders` WHERE `VPSOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=30000;

-- SEPARATOR

UPDATE `Orders` SET
	`IsAutoProlong`=(SELECT `DSOrders`.`AutoProlong` FROM `DSOrders` WHERE `DSOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=40000;

-- SEPARATOR

UPDATE `Orders` SET
	`IsAutoProlong`=(SELECT `ExtraIPOrders`.`AutoProlong` FROM `ExtraIPOrders` WHERE `ExtraIPOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=50000;

-- SEPARATOR

UPDATE `Orders` SET
	`IsAutoProlong`=(SELECT `ISPswOrders`.`AutoProlong` FROM `ISPswOrders` WHERE `ISPswOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=51000;

-- SEPARATOR

ALTER TABLE `HostingOrders`
	DROP `AutoProlong`;

-- SEPARATOR

ALTER TABLE `VPSOrders`
	DROP `AutoProlong`;

-- SEPARATOR

ALTER TABLE `DSOrders`
	DROP `AutoProlong`;

-- SEPARATOR

ALTER TABLE `ExtraIPOrders`
	DROP `AutoProlong`;

-- SEPARATOR

ALTER TABLE `ISPswOrders`
	DROP `AutoProlong`;


