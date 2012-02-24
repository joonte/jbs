ALTER TABLE `HostingSchemes` ADD `IsProlong` enum('no','yes') default 'yes' AFTER `IsActive`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsSchemeChange` enum('no','yes') default 'yes' AFTER `IsProlong`;
-- SEPARATOR
UPDATE `HostingSchemes` SET `IsProlong` = 'yes',`IsSchemeChange` = 'yes';