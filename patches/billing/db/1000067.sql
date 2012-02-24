CREATE TABLE `InvoicesItems` (
  `ID` int(11) NOT NULL auto_increment,
  `InvoiceID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `Amount` int(11) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `InvoicesItemsOrderID` (`OrderID`),
  CONSTRAINT `InvoicesItemsOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `InvoicesItemsInvoiceID` (`InvoiceID`),
  CONSTRAINT `InvoicesItemsInvoiceID` FOREIGN KEY (`InvoiceID`) REFERENCES `Invoices` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;