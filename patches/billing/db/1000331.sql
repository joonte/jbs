
SET NAMES UTF8;

-- SEPARATOR

DELETE FROM `Services` WHERE `ID` IN (2100);

-- SEPARATOR

INSERT INTO `Services` (`ID`,`ServicesGroupID`,`GroupID`,`UserID`,`Name`,`NameShort`,`OperationSign`,`Measure`,`IsHidden`,`IsProtected`,`IsActive`)
	VALUES (2100,1000,2000000,1,'Хранение информации пользователя','Хранение информации','-','шт.','yes','yes','no');


