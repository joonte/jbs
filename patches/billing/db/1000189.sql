CREATE TABLE `Events` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `UserID` int(11) NOT NULL,
  `Text` text NOT NULL,
  `PriorityID` int(1) default '1',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;