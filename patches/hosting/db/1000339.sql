DROP TABLE IF EXISTS `ResourceCharts`;

-- SEPARATOR

CREATE TABLE `ResourceCharts` (
	`ID` int(11) NOT NULL auto_increment,	-- идентфикатор
	`CreateDate` int(11) default '0',	-- дата создания записи
	`OrderID` int(11) NOT NULL,		-- номер заказа
	`GraphID` char(16) NOT NULL,		-- идентфикатор графика: CPU, MEM, ...
	`Data` char(32) NOT NULL,		-- показания
	`Limit` char(32) NOT NULL,		-- ограничение
	PRIMARY KEY(`ID`),
	KEY `ResourceChartsCreateDate` (`CreateDate`),  -- ключ на дату создания записи
	KEY `ResourceChartsOrderID` (`OrderID`),        -- внешний ключ на таблицу заказов
	KEY `ResourceChartsGraphID` (`GraphID`),        -- ключ на тип графика
	CONSTRAINT `ResourceChartsOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


