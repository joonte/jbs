CREATE TABLE `HostingServersGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(30) NOT NULL,
  `Comment` char(255) default '',
  PRIMARY KEY(`ID`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;