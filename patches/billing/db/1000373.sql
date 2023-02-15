
ALTER TABLE `DNSmanagerSchemes` ADD `SchemeParams` VARCHAR(16384) NOT NULL AFTER `SortID`;
-- SEPARATOR
ALTER TABLE `DNSmanagerSchemes` DROP `ViewArea`;
-- SEPARATOR
ALTER TABLE `DNSmanagerSchemes` DROP `DomainLimit`;

