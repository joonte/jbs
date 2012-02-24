ALTER TABLE `EdesksMessages` ADD `IsNotify` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no';

-- SEPARATOR

UPDATE `EdesksMessages` SET `IsNotify`='yes';

-- SEPARATOR

ALTER TABLE `EdesksMessages` ADD INDEX (`IsNotify`);

