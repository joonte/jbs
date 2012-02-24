CREATE TABLE `RegistratorsContracts` (
  `ID` int(11) NOT NULL auto_increment,
  `UserID` int(11) NOT NULL,
  `RegistratorID` char(30) NOT NULL,
  `ContractID` char(30) NOT NULL,
  PRIMARY KEY  (`ID`),
  CONSTRAINT `RegistratorsContractsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;