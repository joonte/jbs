CREATE TABLE `HostingPolitics` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `SchemeID` int(11) NOT NULL,
  `DaysPay` int(11) NOT NULL,
  `Discont` float(5,2) NOT NULL,
  PRIMARY KEY(`ID`),
  KEY `HostingPoliticsGroupID` (`GroupID`),
  CONSTRAINT `HostingPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingPoliticsUserID` (`UserID`),
  CONSTRAINT `HostingPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingPoliticsSchemeID` (`SchemeID`),
  CONSTRAINT `HostingPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `HostingSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = InnoDB;