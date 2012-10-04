
UPDATE `Orders` SET `ExpirationDate`=(SELECT `ExpirationDate` FROM `DomainsOrders` WHERE `Orders`.`ID`=`DomainsOrders`.`ID`) WHERE `ServiceID`=20000;

