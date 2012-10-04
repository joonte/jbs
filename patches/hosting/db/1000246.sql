
UPDATE `Orders` SET `ExpirationDate`=(SELECT `ExpirationDate` FROM `DomainsOrders` WHERE `Orders`.`ID`=`DomainsOrders`.`OrderID`) WHERE `ServiceID`=20000;

