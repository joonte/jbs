ALTER TABLE `Groups` ADD `Comment` char(255) default '' AFTER `IsDepartment`;
-- SEPARATOR
UPDATE `Groups` SET `Comment` = 'Главная корневая группа' WHERE `ID` = 1;
-- SEPARATOR
UPDATE `Groups` SET `Comment` = 'Группа всех клиентов' WHERE `ID` = 2000000;
-- SEPARATOR
UPDATE `Groups` SET `Comment` = 'Группа сотрудников компании' WHERE `ID` = 3000000;
-- SEPARATOR
UPDATE `Groups` SET `Name` = 'Служба технической поддержки',`Comment` = 'Вопросы технического характера' WHERE `ID` = 3100000;
-- SEPARATOR
UPDATE `Groups` SET `Comment` = 'Группа разработчиков Joonte' WHERE `ID` = 3900000;
-- SEPARATOR
UPDATE `Groups` SET `Comment` = 'Вопросы документации и бухгалтерской отчетности' WHERE `ID` = 3200000;
-- SEPARATOR
UPDATE `Groups` SET `Comment` = 'Коммерческие предложения и жалобы' WHERE `ID` = 3300000;
-- SEPARATOR
UPDATE `Groups` SET `Comment` = 'Группа всех клиентов' WHERE `ID` = 4000000;