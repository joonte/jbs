
DROP TABLE IF EXISTS `OrdersHistory`;
-- SEPARATOR
CREATE TABLE IF NOT EXISTS `OrdersHistory` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,
	`Email` char(255) NOT NULL,
	`ServiceID` int(11) NOT NULL,
	`ServiceName` char(255) NOT NULL,
	`SchemeID` int(11) NOT NULL,
	`SchemeName` char(255) NOT NULL,
	`OrderID` int(11) NOT NULL,
	`CreateDate` int(11) NOT NULL,
	`StatusDate` int(11) NOT NULL,
	PRIMARY KEY (`ID`),
	KEY `OrdersHistoryUserID` (`UserID`),
	KEY `OrdersHistoryEmail` (`Email`),
	KEY `OrdersHistoryServiceID` (`ServiceID`),
	KEY `OrdersHistorySchemeID` (`SchemeID`),
	KEY `OrdersHistoryOrderID` (`OrderID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `ISPswSchemes` ADD `MaxOrders` INT(6) DEFAULT '0' AFTER `MaxDaysPay` ;

