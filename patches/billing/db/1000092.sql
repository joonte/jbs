CREATE TABLE `Events` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `UserID` int(11) NOT NULL,
  `Text` text NOT NULL,
  `PriorityID` int(1) default '0',
  PRIMARY KEY  (`ID`),
  KEY `EventsUserID` (`UserID`),
  CONSTRAINT `EventsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;