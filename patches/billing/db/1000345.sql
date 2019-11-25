
DROP TABLE IF EXISTS `Files`;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `Files` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,		-- идентфикатор
	`CreateDate` int(11) default '0',		-- дата создания записи
	`TableID` char(30) default '',			-- таблица к которой относятся вложения
	`RowID` int(11) default '0',			-- строка в таблице, к которой относится вложение
	`Name` char(255) default '',			-- имя файла
	`Size` INT(8) default '0',			-- размер файла, в байтах 1.000.000.
	`Mime` char(255) default '',			-- mime тип файла (image/jpeg, text/html, application/pdf)
	PRIMARY KEY (`ID`),				-- первичный ключ
	KEY `TableRowID` (`TableID`,`RowID`)		-- ключ по таблице-строке - собственно по нему и будут искаться по большей части...
	/* ключ и внешний ключ на таблицу юзеров
	KEY `FilesUserID` (`UserID`),
	CONSTRAINT `FilesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE */
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


