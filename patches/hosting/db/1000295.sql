

ALTER TABLE `ISPswLicenses` ADD `remoteip` CHAR(64) NOT NULL AFTER `IP`;
-- SEPARATOR
ALTER TABLE `ISPswLicenses` ADD `ip_change_date` INT(11) NOT NULL AFTER `CreateDate`;
-- SEPARATOR
ALTER TABLE `ISPswLicenses` CHANGE `UpdateDate` `lickey_change_date` INT(11) NOT NULL;


