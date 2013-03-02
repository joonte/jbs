ALTER TABLE `EdesksMessages` ADD `IsVisible` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'yes' AFTER `IsNotify` ;

