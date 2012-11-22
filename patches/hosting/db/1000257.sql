
ALTER TABLE `Services` ADD `NameShort` CHAR( 32 ) NOT NULL AFTER `Name`;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'Пополнение' WHERE `ID` = 1000;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'Начисление' WHERE `ID` = 1100;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'Списание' WHERE `ID` = 2000;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'Возврат на баланс' WHERE `ID` = 3000;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'Возврат на реквизиты' WHERE `ID` = 4000;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'Хостинг' WHERE `ID` = 10000;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'Домены' WHERE `ID` = 20000;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'Виртуальный сервер' WHERE `ID` = 30000;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'Выделенный сервер' WHERE `ID` = 40000;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'IP адрес' WHERE `ID` = 50000;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = 'ПО ISPsystem' WHERE `ID` = 51000;
-- SEPARATOR
UPDATE `Services` SET `NameShort` = `Name` WHERE `ID` NOT IN (1000,1100,2000,3000,4000,10000,20000,30000,40000,50000,51000);


