ALTER TABLE `DomainsSchemes` ADD `IsActive` enum('no','yes') default 'no' AFTER `Name`;
-- SEPARATOR
ALTER TABLE `DomainsSchemes` ADD `IsProlong` enum('no','yes') default 'no' AFTER `IsActive`;
-- SEPARATOR
UPDATE `DomainsSchemes` SET `IsActive` = 'yes', `IsProlong` = 'yes';