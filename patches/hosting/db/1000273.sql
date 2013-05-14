
DROP TABLE IF EXISTS `ClausesGroups`;
-- SEPARATOR
CREATE TABLE IF NOT EXISTS `ClausesGroups` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`PublicDate` int(11) NOT NULL,
	`ChangedDate` int(11) NOT NULL,
	`AuthorID` int(11) NOT NULL,
	`EditorID` int(11) NOT NULL,
	`Name` varchar(1023),
	`Notice` text,
	`IsProtected` enum('no','yes') DEFAULT 'no',
	`IsPublish` enum('no','yes') DEFAULT 'no',
	PRIMARY KEY (`ID`),
	KEY `ClausesGroupsPublicDate` (`PublicDate`),
	KEY `ClausesGroupsAuthorID` (`AuthorID`),
	KEY `ClausesGroupsEditorID` (`EditorID`),
	CONSTRAINT `ClausesGroupsAuthorID` FOREIGN KEY (`AuthorID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `ClausesGroupsEditorID` FOREIGN KEY (`EditorID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (1,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Без категории','Статьи не вошедшие ни в какие категории','yes','yes');
-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (2,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Новости','Новости хостинга, RSS','yes','yes');
-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (3,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Соглашения','Шаблоны соглашений','yes','yes');
-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (4,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Договора','Шаблоны договоров','yes','yes');
-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (5,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Конверты','Шаблоны конвертов','yes','yes');
-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (6,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Платёжные системы','Шаблоны счетов','yes','yes');
-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (7,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Регистраторы','Шаблоны писем о переносе доменов к регистраторам','yes','yes');
-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (8,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Подсказки','Подсказки платёжных систем и т.п.','yes','yes');
-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (9,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Описания страниц','Описания страниц биллинговой системы','yes','yes');
-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (10,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Шаблоны тикетов','Шаблоны для создания тикета','yes','yes');
-- SEPARATOR
INSERT INTO `ClausesGroups` (`ID`,`PublicDate`,`ChangedDate`,`AuthorID`,`EditorID`,`Name`,`Notice`,`IsProtected`,`IsPublish`)
	VALUES (11,UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),100,100,'Кнопки тикетницы','Кнопки быстрого ответа в тикетнице','yes','yes');

-- SEPARATOR
ALTER TABLE `Clauses` ADD `GroupID` INT(11) NOT NULL DEFAULT '1' AFTER `ID`, ADD KEY `ClausesGroupID` (`GroupID`);
-- SEPARATOR
ALTER TABLE `Clauses` ADD CONSTRAINT `ClausesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `ClausesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE ;

-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 1 WHERE `Partition` = 'User/Panel';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 4 WHERE `Partition` = '/Documents';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 8 WHERE `Partition` = '/Help/Services/Paying';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 2 WHERE `Partition` = 'News';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 1 WHERE `Partition` = '/Contacts';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 8 WHERE `Partition` = '/Help/Services/QIWIPhone';


-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 2 WHERE `Partition` LIKE '%News%';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 4 WHERE `Partition` LIKE 'Contracts%';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 3 WHERE `Partition` LIKE '%Agreement%';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 5 WHERE `Partition` LIKE 'Envelope%';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 6 WHERE `Partition` LIKE 'Invoices/%';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 7 WHERE `Partition` LIKE 'Registrators/%';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 9 WHERE `Partition` LIKE 'Header:%';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 10 WHERE `Partition` LIKE 'CreateTicket/%';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 10 WHERE `Partition` LIKE '/Administrator/ButtonsNew:%';
-- SEPARATOR
UPDATE `Clauses` SET `GroupID` = 11 WHERE `Partition` LIKE '/Administrator/Buttons:%';


