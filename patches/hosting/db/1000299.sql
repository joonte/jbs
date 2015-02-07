
ALTER TABLE `DomainSchemes` ADD `IsTransfer` ENUM('no','yes') NOT NULL DEFAULT 'no' AFTER `IsProlong`;

-- SEPARATOR

UPDATE `DomainSchemes` SET `IsTransfer` = `IsActive`;


