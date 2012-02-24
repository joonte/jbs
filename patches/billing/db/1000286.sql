
ALTER TABLE `Edesks` ADD `Flags` ENUM('no', 'Closed', 'CloseOnSee', 'DenyClose') NOT NULL DEFAULT 'no';

-- SEPARATOR

ALTER TABLE `Edesks` ADD INDEX (`Flags`);

