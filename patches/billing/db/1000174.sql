CREATE TABLE `Spider` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `Source` varchar(1000) NOT NULL,
  `UpdateDate` int(11) default '0',
  `Title` varchar(100),
  `Body` text,
  `Errors` text,
   PRIMARY KEY  (`ID`),
   FULLTEXT(`Body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;