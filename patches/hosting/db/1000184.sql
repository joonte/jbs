CREATE TABLE `DomainsSchemesGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(255) default '',
  PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- SEPARATOR
CREATE TABLE `DomainsSchemesGroupsItems` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DomainsSchemesGroupID` int(11) NOT NULL,
  `SchemeID` int(11) NULL,
  PRIMARY KEY(`ID`),
  KEY `DomainsSchemesGroupsItemsDomainsSchemesGroupID` (`DomainsSchemesGroupID`),
  CONSTRAINT `DomainsSchemesGroupsItemsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainsSchemesGroupsItemsID` (`SchemeID`),
  CONSTRAINT `DomainsSchemesGroupsItemsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DomainsSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- SEPARATOR
CREATE TABLE `HostingDomainsPolitics` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `SchemeID` int(11) NULL,
  `DomainsSchemesGroupID` int(11) NOT NULL,
  `DaysPay` int(11) NOT NULL,
  `Discont` float(5,2) NOT NULL,
  PRIMARY KEY(`ID`),
  KEY `HostingDomainsPoliticsGroupID` (`GroupID`),
  CONSTRAINT `HostingDomainsPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingDomainsPoliticsUserID` (`UserID`),
  CONSTRAINT `HostingDomainsPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingDomainsPoliticsSchemeID` (`SchemeID`),
  CONSTRAINT `HostingDomainsPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `HostingSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingDomainsPoliticsDomainsSchemesGroupID` (`DomainsSchemesGroupID`),
  CONSTRAINT `HostingDomainsPoliticsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;