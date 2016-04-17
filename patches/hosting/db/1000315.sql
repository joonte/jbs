

ALTER TABLE `DSSchemes` DROP `NumServers`;

-- SEPARATOR

ALTER TABLE `DSSchemes` DROP `RemainServers`;

-- SEPARATOR

ALTER TABLE `DSSchemes` DROP `IsCalculateNumServers`;

-- SEPARATOR

ALTER TABLE `DSSchemes` ADD `Switch` char(255) AFTER `OS`;

-- SEPARATOR

ALTER TABLE `DSSchemes` ADD `IsBroken` enum('no','yes') NOT NULL DEFAULT 'no' AFTER `IsActive`;

