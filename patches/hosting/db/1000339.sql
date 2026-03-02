DROP TABLE IF EXISTS `ResourceCharts`;

-- SEPARATOR

CREATE TABLE `ResourceCharts` (
	`ID` int(11) NOT NULL auto_increment,	-- идентфикатор
	`CreateDate` int(11) default '0',	-- дата создания записи
	`OrderID` int(11) NOT NULL,		-- номер заказа
	`GraphID` char(16) NOT NULL,		-- идентфикатор графика: CPU, MEM, ...
	`Data` DOUBLE NOT NULL DEFAULT '0',	-- показания
	PRIMARY KEY(`ID`),
	/* внешний ключ на таблицу заказов */
	KEY `ResourceChartsOrderID` (`OrderID`),
	CONSTRAINT `ResourceChartsOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

