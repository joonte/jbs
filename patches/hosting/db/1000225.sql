
ALTER TABLE `Orders`  ADD `UserNotice` TEXT NOT NULL,  ADD `AdminNotice` TEXT NOT NULL;

-- SEPARATOR

UPDATE `Orders` SET
	`UserNotice`=(SELECT `HostingOrders`.`UserNotice` FROM `HostingOrders` WHERE `HostingOrders`.`OrderID`=`Orders`.`ID`),
	`AdminNotice`=(SELECT `HostingOrders`.`AdminNotice` FROM `HostingOrders` WHERE `HostingOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=10000;

-- SEPARATOR

UPDATE `Orders` SET
	`UserNotice`=(SELECT `DomainsOrders`.`UserNotice` FROM `DomainsOrders` WHERE `DomainsOrders`.`OrderID`=`Orders`.`ID`),
	`AdminNotice`=(SELECT `DomainsOrders`.`AdminNotice` FROM `DomainsOrders` WHERE `DomainsOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=20000;

-- SEPARATOR

UPDATE `Orders` SET
	`UserNotice`=(SELECT `VPSOrders`.`UserNotice` FROM `VPSOrders` WHERE `VPSOrders`.`OrderID`=`Orders`.`ID`),
	`AdminNotice`=(SELECT `VPSOrders`.`AdminNotice` FROM `VPSOrders` WHERE `VPSOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=30000;

-- SEPARATOR

UPDATE `Orders` SET
	`UserNotice`=(SELECT `DSOrders`.`UserNotice` FROM `DSOrders` WHERE `DSOrders`.`OrderID`=`Orders`.`ID`),
	`AdminNotice`=(SELECT `DSOrders`.`AdminNotice` FROM `DSOrders` WHERE `DSOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=40000;

-- SEPARATOR

UPDATE `Orders` SET
	`UserNotice`=(SELECT `ExtraIPOrders`.`UserNotice` FROM `ExtraIPOrders` WHERE `ExtraIPOrders`.`OrderID`=`Orders`.`ID`),
	`AdminNotice`=(SELECT `ExtraIPOrders`.`AdminNotice` FROM `ExtraIPOrders` WHERE `ExtraIPOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=50000;

-- SEPARATOR

UPDATE `Orders` SET
	`UserNotice`=(SELECT `ISPswOrders`.`UserNotice` FROM `ISPswOrders` WHERE `ISPswOrders`.`OrderID`=`Orders`.`ID`),
	`AdminNotice`=(SELECT `ISPswOrders`.`AdminNotice` FROM `ISPswOrders` WHERE `ISPswOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=51000;

-- SEPARATOR

ALTER TABLE `HostingOrders`
	DROP `UserNotice`,
	DROP `AdminNotice`;

-- SEPARATOR

ALTER TABLE `DomainsOrders`
	DROP `UserNotice`,
	DROP `AdminNotice`;

-- SEPARATOR

ALTER TABLE `VPSOrders`
	DROP `UserNotice`,
	DROP `AdminNotice`;

-- SEPARATOR

ALTER TABLE `DSOrders`
	DROP `UserNotice`,
	DROP `AdminNotice`;

-- SEPARATOR

ALTER TABLE `ExtraIPOrders`
	DROP `UserNotice`,
	DROP `AdminNotice`;

-- SEPARATOR

ALTER TABLE `ISPswOrders`
	DROP `UserNotice`,
	DROP `AdminNotice`;


