
DROP TABLE IF EXISTS `UsersIPs`;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `UsersIPs` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,		-- идентфикатор
	`CreateDate` int(11) default '0',		-- дата создания записи
	`UserID` int(11) NOT NULL,			-- идентификатор юзера
	`EdesksMessageID` int(11) NOT NULL,		-- идентификатор сообщения в системе поддержки (если это IP из сообщения)
	`IP` CHAR(40) NOT NULL DEFAULT '127.0.0.127',	-- IP адрес
	`UA` TEXT NOT NULL,				-- юзер-агент браузера
	PRIMARY KEY (`ID`),				-- первичный ключ
	/* ключ и внешний ключ на таблицу юзеров */
	KEY `UsersIPsUserID` (`UserID`),
	CONSTRAINT `UsersIPsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `UsersIPsCreateDate` (`CreateDate`)		-- ключ на дату создания записи
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR

DROP TRIGGER IF EXISTS `UsersIPsOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `UsersIPsOnInsert` BEFORE INSERT ON `UsersIPs`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;

-- SEPARATOR

INSERT INTO `UsersIPs` (`CreateDate`,`UserID`,`EdesksMessageID`,`IP`,`UA`) SELECT `CreateDate`,`UserID`,`ID`,`IP`,`UA` FROM `EdesksMessages`;

-- SEPARATOR

ALTER TABLE `EdesksMessages` DROP `IP`;

-- SEPARATOR

ALTER TABLE `EdesksMessages` DROP `IP`;

-- SEPARATOR

DELETE FROM `UsersIPs` WHERE `IP` LIKE '127.%';

