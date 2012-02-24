SET FOREIGN_KEY_CHECKS=0;
-- SEPARATOR
ALTER TABLE `Services` ADD `Emblem` mediumblob AFTER `Name`;
-- SEPARATOR
ALTER TABLE `Services` CHANGE `ConsiderTypeID` `ConsiderTypeID` char(30) default 'Upon';
-- SEPARATOR
ALTER TABLE `ServicesFields` ADD `IsKey` enum('no','yes') default 'yes' AFTER `IsDuty`;
-- SEPARATOR
ALTER TABLE `Orders` ADD `ExpirationDate` int(11) default '0' AFTER `ServiceID`;
-- SEPARATOR
ALTER TABLE `Orders` ADD `Keys` char(255) default '' AFTER `ExpirationDate`;
-- SEPARATOR
ALTER TABLE `Services` CHANGE `Code` `Code` char(255) default 'Default';
-- SEPARATOR
UPDATE `Services` SET `Code` = 'Default' WHERE `Code` = 'Services';
-- SEPARATOR
ALTER TABLE `Accounts` RENAME `Invoices`;
-- SEPARATOR
DROP VIEW `AccountsOwners`;
-- SEPARATOR
DROP TRIGGER `AccountsOnInsert`;
-- SEPARATOR
DROP TRIGGER `AccountsItemsOnInserted`;
-- SEPARATOR
DROP TRIGGER `AccountsItemsOnUpdated`;
-- SEPARATOR
ALTER TABLE `AccountsItems` RENAME `InvoicesItems`;
-- SEPARATOR
ALTER TABLE `InvoicesItems` DROP FOREIGN KEY `AccountsItemsAccountID`;
-- SEPARATOR
ALTER TABLE `InvoicesItems` DROP KEY `AccountsItemsAccountID`;
-- SEPARATOR
ALTER TABLE `InvoicesItems` CHANGE `AccountID` `InvoiceID` int(11) NOT NULL;
-- SEPARATOR
ALTER TABLE `InvoicesItems` ADD INDEX `InvoicesItemsInvoiceID` (`InvoiceID`);
-- SEPARATOR
ALTER TABLE `InvoicesItems` ADD CONSTRAINT `InvoicesItemsInvoiceID` FOREIGN KEY (`InvoiceID`) REFERENCES `Invoices` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
-- SEPARATOR
DROP TABLE IF EXISTS `OrdersFields`;
-- SEPARATOR
CREATE TABLE `OrdersFields` (
  `ID` int(11) NOT NULL auto_increment,
  `OrderID` int(11) NOT NULL,
  `ServiceFieldID` int(11) NOT NULL,
  `Value` text NOT NULL,
  `FileName` varchar(255) default '',
  PRIMARY KEY  (`ID`),
  KEY `OrdersFieldsOrderID` (`OrderID`),
  CONSTRAINT `OrdersFieldsOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `OrdersFieldsServiceFieldID` (`ServiceFieldID`),
  CONSTRAINT `OrdersFieldsServiceFieldID` FOREIGN KEY (`ServiceFieldID`) REFERENCES `ServicesFields` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- SEPARATOR
SET FOREIGN_KEY_CHECKS=1;