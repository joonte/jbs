ALTER TABLE `Registrators` ADD `PrefixAPI` char(255) default '' AFTER `Protocol`;
-- SEPARATOR
UPDATE `Registrators` SET `PrefixAPI` = '/api/regru' WHERE `TypeID` = 'RegRu';