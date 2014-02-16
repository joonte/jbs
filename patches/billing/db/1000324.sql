
ALTER TABLE `Services` ADD `Params` LONGTEXT NOT NULL AFTER `IsActive`;
-- SEPARATOR
UPDATE `Services` SET `Params` = '[]' WHERE `Params` = '';
-- SEPARATOR
ALTER TABLE `Services` DROP `IsNoActionProlong`;
-- SEPARATOR
ALTER TABLE `Services` DROP `IsNoActionSuspend`;
-- SEPARATOR
ALTER TABLE `Services` DROP `IsNoActionDelete`;

