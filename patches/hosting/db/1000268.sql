
ALTER TABLE `HostingSchemes` CHANGE `QuotaCPU` `MaxExecutionTime` INT(7);
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `QuotaCPU` FLOAT(7,2) NOT NULL AFTER `QuotaDBs`;
-- SEPARATOR
UPDATE `HostingSchemes` SET `QuotaCPU` = 60;


