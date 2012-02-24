CREATE TABLE `Basket` (
  `ID` int(11) NOT NULL auto_increment,
  `OrderID` int(11) NOT NULL,
  `Amount` int(11) NOT NULL,
  `Summ` float(7,2) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `BasketOrderID` (`OrderID`),
  CONSTRAINT `BasketOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;