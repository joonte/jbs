
ALTER TABLE `VPSSchemes` ADD `fstype` CHAR(16) NOT NULL AFTER `backup`;
-- SEPARATOR
ALTER TABLE `VPSSchemes` ADD `IsTun` enum('no','yes') NOT NULL DEFAULT 'no' AFTER `fstype`;

