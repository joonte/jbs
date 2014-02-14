
ALTER TABLE `Services`  ADD `Params` LONGTEXT NOT NULL AFTER `IsActive`;
-- SEPARATOR
UPDATE `Services` SET `Params` = '[]' WHERE `Params` = '';


