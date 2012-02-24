
ALTER TABLE `ISPswOrders` ADD `LicenseID` INT(12) DEFAULT NULL AFTER `IP` ;

-- SEPARATOR

ALTER TABLE `ISPswOrders`
  ADD CONSTRAINT `ISPswOrdersID` FOREIGN KEY (`LicenseID`) REFERENCES `ISPswLicenses` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

update `ISPswLicenses` set `IsUsed`='no' WHERE `IsUsed`!='yes';

-- SEPARATOR

DELETE FROM `Clauses` WHERE `Partition`='Header:/Administrator/ISPswOrders';

-- SEPARATOR

UPDATE `ISPswOrders` SET `LicenseID`=(SELECT `ISPswLicenses`.`ID` FROM `ISPswLicenses` WHERE `ISPswOrders`.`IP`=`ISPswLicenses`.`IP`);

