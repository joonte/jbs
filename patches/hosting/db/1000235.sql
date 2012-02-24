
DROP TABLE IF EXISTS `OrdersConsider`;

-- SEPARATOR

CREATE TABLE `OrdersConsider` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) default '0',
	`OrderID` int(11) NOT NULL,
	`DaysReserved` int(11) default '0',
	`DaysRemainded` int(11) default '0',
	`DaysConsidered` int(11) default '0',
	`Cost` float(11,2) default '0.00',
	`Discont` float(11,2) default '0.00',
	PRIMARY KEY(`ID`),
	KEY `OrdersConsiderOrderID` (`OrderID`),
	CONSTRAINT `OrdersConsiderOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR

ALTER TABLE `Orders` ADD `DaysRemainded` INT(11) NOT NULL AFTER `IsPayed` ;

-- SEPARATOR

INSERT INTO `OrdersConsider` 
	(`CreateDate`,`OrderID`,`DaysReserved`,`DaysRemainded`,`DaysConsidered`,`Cost`,`Discont`) 
SELECT 
	`CreateDate`, (SELECT `OrderID` FROM `HostingOrders` WHERE `HostingOrders`.`ID` = `HostingConsider`.`HostingOrderID`), `DaysReserved`, `DaysRemainded`,`DaysConsidered`,`Cost`,`Discont` FROM `HostingConsider`;

-- SEPARATOR

ALTER TABLE `HostingOrders` DROP `DaysRemainded`;

-- SEPARATOR

INSERT INTO `OrdersConsider` 
	(`CreateDate`,`OrderID`,`DaysReserved`,`DaysRemainded`,`DaysConsidered`,`Cost`,`Discont`) 
SELECT 
	`CreateDate`, (SELECT `OrderID` FROM `VPSOrders` WHERE `VPSOrders`.`ID` = `VPSConsider`.`VPSOrderID`), `DaysReserved`, `DaysRemainded`,`DaysConsidered`,`Cost`,`Discont` FROM `VPSConsider`;

-- SEPARATOR

ALTER TABLE `VPSOrders` DROP `DaysRemainded`;

-- SEPARATOR

INSERT INTO `OrdersConsider` 
	(`CreateDate`,`OrderID`,`DaysReserved`,`DaysRemainded`,`DaysConsidered`,`Cost`,`Discont`) 
SELECT
	`CreateDate`, (SELECT `OrderID` FROM `DSOrders` WHERE `DSOrders`.`ID` = `DSConsider`.`DSOrderID`), `DaysReserved`, `DaysRemainded`,`DaysConsidered`,`Cost`,`Discont` FROM `DSConsider`;

-- SEPARATOR

ALTER TABLE `DSOrders` DROP `DaysRemainded`;

-- SEPARATOR

INSERT INTO `OrdersConsider` 
	(`CreateDate`,`OrderID`,`DaysReserved`,`DaysRemainded`,`DaysConsidered`,`Cost`,`Discont`) 
SELECT
	`CreateDate`, (SELECT `OrderID` FROM `ExtraIPOrders` WHERE `ExtraIPOrders`.`ID` = `ExtraIPConsider`.`ExtraIPOrderID`), `DaysReserved`, `DaysRemainded`,`DaysConsidered`,`Cost`,`Discont` FROM `ExtraIPConsider`;

-- SEPARATOR

ALTER TABLE `ExtraIPOrders` DROP `DaysRemainded`;

-- SEPARATOR

INSERT INTO `OrdersConsider` 
	(`CreateDate`,`OrderID`,`DaysReserved`,`DaysRemainded`,`DaysConsidered`,`Cost`,`Discont`) 
SELECT
	`CreateDate`, (SELECT `OrderID` FROM `ISPswOrders` WHERE `ISPswOrders`.`ID` = `ISPswConsider`.`ISPswOrderID`), `DaysReserved`, `DaysRemainded`,`DaysConsidered`,`Cost`,`Discont` FROM `ISPswConsider`;

-- SEPARATOR

ALTER TABLE `ISPswOrders` DROP `DaysRemainded`;

-- SEPARATOR

DROP TABLE `DSConsider`, `ExtraIPConsider`, `HostingConsider`, `ISPswConsider`, `VPSConsider`;

-- SEPARATOR

UPDATE `Orders` SET `DaysRemainded` = (SELECT SUM(`DaysRemainded`) FROM `OrdersConsider` WHERE `OrdersConsider`.`OrderID` = Orders.`ID` AND `OrdersConsider`.`DaysRemainded` > 0);

