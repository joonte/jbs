CREATE TABLE `Registrators` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(30) NOT NULL,
  `TypeID` char(30) NOT NULL,
  `Address` char(30) NOT NULL,
  `Port` int(5) NOT NULL,
  `Protocol` enum('tcp','ssl') default 'tcp',
  `Login` char(60) NOT NULL,
  `Password` char(60) NOT NULL,
  PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;