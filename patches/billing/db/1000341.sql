
set names utf8;

-- SEPARATOR

DROP TABLE IF EXISTS `Contacts`;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `Contacts` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,		-- идентфикатор
	`CreateDate` int(11) default '0',		-- дата создания записи
	`UserID` int(11) NOT NULL,			-- идентификатор юзера, владельца контакта
	`MethodID` char(30) default '',			-- метод (тип) оповещения: SMS, Email, Jabber...
	`Address` char(255) default '',			-- собственно контактный адрес
	`ExternalID` char(255) default '',		-- внешний идентификатор, для Telegramm и т.п.
	`Confirmed` INT(12) default '0',		-- подтверждён ил нет адрес, если подвтерждён - тут штамп времени когда это сделано
	`Confirmation` char(32) default '',		-- код подтверждения отосланный юзеру. вопрос о сроках его действия пока оставим открытым...
	`TimeBegin` char(30) default '00',		-- время начала рассылок по этому контакту
	`TimeEnd` char(30) default '00',		-- время конца рассылок по этому контакту
	`IsPrimary` enum('no','yes') DEFAULT 'no',	-- это первичный адрес, используется для входа в биллинг. пока, первичным может быть только почтовый адрес
	`IsActive` enum('no','yes') DEFAULT 'no',	-- можно использовать для оповещений
	`UserNotice` text,				-- примечание пользователя о этом контакте
	PRIMARY KEY (`ID`),
	KEY `Confirmation` (`Confirmation`),
	KEY `ContactsUserID` (`UserID`),
	/* внешний ключ на таблицу юзеров */
	CONSTRAINT `ContactsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	/* уникальный ключ по юзеру-методу-адресу, чтобы не дублировались записи у одного пользователя */
	UNIQUE KEY `UserMethodAddress` (`UserID`, `MethodID`, `Address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


