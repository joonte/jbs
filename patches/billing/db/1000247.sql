CREATE TABLE `StatusesHistory` (
  `ID` int(11) NOT NULL auto_increment,
  `StatusDate` int(11) default '0',
  `ModeID` varchar(255) NOT NULL,
  `RowID` int(11) NOT NULL,
  `StatusID` varchar(255) NOT NULL,
  `Initiator` varchar(255) default '',
  `Comment` varchar(255) default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;