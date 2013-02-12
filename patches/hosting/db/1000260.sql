DROP TABLE IF EXISTS `OrdersTransfer`;
-- SEPARATOR
CREATE TABLE `OrdersTransfer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) DEFAULT '0',
  `UserID` int(11) NOT NULL,
  `ServiceID` int(11) NOT NULL,
  `ServiceOrderID` int(11) NOT NULL,
  `ToUserID` int(11) NOT NULL,
  `IsExecuted` enum('yes','no') DEFAULT 'no',
  PRIMARY KEY (`ID`),
  KEY `OrdersTransferCreateDate` (`CreateDate`),
  KEY `OrdersTransferUserID` (`UserID`),
  KEY `OrdersTransferServiceID` (`ServiceID`),
  KEY `OrdersTransferToUserID` (`ToUserID`),
  KEY `OrdersTransferIsExecuted` (`IsExecuted`),
  CONSTRAINT `OrdersTransferToUserID` FOREIGN KEY (`ToUserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `OrdersTransferUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `OrdersTransferServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



