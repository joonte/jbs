
ALTER TABLE `Services` ADD `IsNoActionProlong` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `IsConditionally`;
-- SEPARATOR
ALTER TABLE `Services` ADD `IsNoActionSuspend` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `IsNoActionProlong`;
-- SEPARATOR
ALTER TABLE `Services` ADD `IsNoActionDelete` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `IsNoActionSuspend`;

