CREATE TABLE `Notifies` (
  `ID` int(11) NOT NULL auto_increment,
  `UserID` int(11) NOT NULL,
  `MethodID` char(30) NOT NULL,
  `TypeID` char(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `NotifiesUserID` (`UserID`),
  CONSTRAINT `NotifiesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;