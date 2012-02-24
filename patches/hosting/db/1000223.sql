
UPDATE `Orders` SET 
	`StatusID`=(SELECT `HostingOrders`.`StatusID` FROM `HostingOrders` WHERE `HostingOrders`.`OrderID`=`Orders`.`ID`),
	`StatusDate`=(SELECT `HostingOrders`.`StatusDate` FROM `HostingOrders` WHERE `HostingOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=10000;

-- SEPARATOR

UPDATE `Orders` SET 
	`StatusID`=(SELECT `DomainsOrders`.`StatusID` FROM `DomainsOrders` WHERE `DomainsOrders`.`OrderID`=`Orders`.`ID`),
	`StatusDate`=(SELECT `DomainsOrders`.`StatusDate` FROM `DomainsOrders` WHERE `DomainsOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=20000;

-- SEPARATOR

UPDATE `Orders` SET 
	`StatusID`=(SELECT `VPSOrders`.`StatusID` FROM `VPSOrders` WHERE `VPSOrders`.`OrderID`=`Orders`.`ID`),
	`StatusDate`=(SELECT `VPSOrders`.`StatusDate` FROM `VPSOrders` WHERE `VPSOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=30000;

-- SEPARATOR

UPDATE `Orders` SET 
	`StatusID`=(SELECT `DSOrders`.`StatusID` FROM `DSOrders` WHERE `DSOrders`.`OrderID`=`Orders`.`ID`),
	`StatusDate`=(SELECT `DSOrders`.`StatusDate` FROM `DSOrders` WHERE `DSOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=40000;

-- SEPARATOR

UPDATE `Orders` SET 
	`StatusID`=(SELECT `ExtraIPOrders`.`StatusID` FROM `ExtraIPOrders` WHERE `ExtraIPOrders`.`OrderID`=`Orders`.`ID`),
	`StatusDate`=(SELECT `ExtraIPOrders`.`StatusDate` FROM `ExtraIPOrders` WHERE `ExtraIPOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=50000;

-- SEPARATOR

UPDATE `Orders` SET 
	`StatusID`=(SELECT `ISPswOrders`.`StatusID` FROM `ISPswOrders` WHERE `ISPswOrders`.`OrderID`=`Orders`.`ID`),
	`StatusDate`=(SELECT `ISPswOrders`.`StatusDate` FROM `ISPswOrders` WHERE `ISPswOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=51000;

-- SEPARATOR

UPDATE `Orders` SET `StatusID`='Deleted' WHERE `StatusID`='UnSeted';


