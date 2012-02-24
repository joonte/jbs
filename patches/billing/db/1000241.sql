ALTER TABLE `Groups` ADD `InterfaceID` char(255) default NULL AFTER `Name`;
-- SEPARATOR
UPDATE `Groups` SET `InterfaceID` = 'User' WHERE `ID` = 1;
-- SEPARATOR
UPDATE `Groups` SET `InterfaceID` = 'Administrator' WHERE `ID` = 3000000;