CREATE TABLE `HostingServers` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SystemID` char(30) NOT NULL,
  `ServersGroupID` int(11) NOT NULL,
  `IsDefault` enum('no','yes') default 'no',
  `Address` char(30) NOT NULL,
  `Port` int(5) NOT NULL,
  `Protocol` enum('tcp','ssl') default 'tcp',
  `Url` char(60) NOT NULL,
  `Login` char(60) NOT NULL,
  `Password` char(60) NOT NULL,
  `IP` char(60) default '127.0.0.1',
  PRIMARY KEY(`ID`),
  KEY `HostingServersServersGroupID` (`ServersGroupID`),
  CONSTRAINT `HostingServersServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `HostingServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;