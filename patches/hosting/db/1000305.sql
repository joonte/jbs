

ALTER TABLE `DSSchemes` ADD `disks` VARCHAR(128) NOT NULL AFTER `raid`;
-- SEPARATOR
UPDATE `DSSchemes` SET `disks` = CONCAT(`disk1`,' + ',`disk2`,' + ',`disk3`,' + ',`disk4`);
-- SEPARATOR
ALTER TABLE `DSSchemes` DROP `disk1`;
-- SEPARATOR
ALTER TABLE `DSSchemes` DROP `disk2`;
-- SEPARATOR
ALTER TABLE `DSSchemes` DROP `disk3`;
-- SEPARATOR
ALTER TABLE `DSSchemes` DROP `disk4`;

