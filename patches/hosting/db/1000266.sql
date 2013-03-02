
ALTER TABLE `EdesksMessages` ADD `IsVisible` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'yes' AFTER `IsNotify` ;
-- SEPARATOR
ALTER TABLE `Edesks` CHANGE `Flags` `Flags` CHAR(32) NOT NULL DEFAULT 'No';

