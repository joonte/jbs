CREATE TABLE `Interrogation` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TestDate` int(11) default '0',
  `ServerID` int(11) default '0',
  `Service` char(20) NOT NULL,
  `UpTime` float(11) default '0',
  `Day` int(2) default '0',
  `Month` int(2) default '0',
  `Year` int(4) default '0',
  `Count` int(11) default '0',
  PRIMARY KEY(`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;