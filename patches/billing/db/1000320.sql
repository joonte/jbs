
ALTER TABLE `Users`  ADD `Params` LONGTEXT NOT NULL AFTER `IsProtected`;
-- SEPARATOR
UPDATE `Users` SET `Params` = '[]' WHERE `Params` = '';

