CREATE TABLE `Services` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` char(255) NOT NULL,
  `Measure` char(30) NOT NULL,
  `IsActive` enum('no','yes') default 'yes',
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;