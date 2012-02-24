CREATE TABLE `ClausesRating` (
  `ID` int(11) NOT NULL auto_increment,
  `ClauseID` int(11) NOT NULL,
  `Rating` int(1) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `ClausesRatingClauseID` (`ClauseID`),
  CONSTRAINT `ClausesRatingClauseID` FOREIGN KEY (`ClauseID`) REFERENCES `Clauses` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;