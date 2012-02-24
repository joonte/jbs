
UPDATE `Orders` SET 
	`StatusID`=(SELECT `HostingOrders`.`StatusID` FROM `HostingOrders` WHERE `HostingOrders`.`OrderID`=`Orders`.`ID`),
	`StatusDate`=(SELECT `HostingOrders`.`StatusDate` FROM `HostingOrders` WHERE `HostingOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=10000;

-- SEPARATOR

UPDATE `Orders` SET 
	`StatusID`=(SELECT `DomainsOrders`.`StatusID` FROM `DomainsOrders` WHERE `DomainsOrders`.`OrderID`=`Orders`.`ID`),
	`StatusDate`=(SELECT `DomainsOrders`.`StatusDate` FROM `DomainsOrders` WHERE `DomainsOrders`.`OrderID`=`Orders`.`ID`)
WHERE `ServiceID`=20000;

