

ALTER TABLE `ExtraIPSchemes` DROP `IsAutomatic`;

-- SEPARATOR

ALTER TABLE `ExtraIPSchemes` ADD `Params` LONGTEXT NOT NULL AFTER `MinOrdersPeriod`;



