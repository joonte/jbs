
ALTER TABLE `ISPswLicenses` ADD `pricelist_id` INT(12) NOT NULL AFTER `ID`;
-- SEPARATOR
ALTER TABLE `ISPswLicenses` ADD `period` VARCHAR(32) NOT NULL AFTER `pricelist_id`;
-- SEPARATOR
ALTER TABLE `ISPswLicenses` ADD `addon` INT(4) NOT NULL AFTER `period`;

