ALTER TABLE `Edesks` CHANGE `Flags` `Flags` ENUM( 'No', 'Closed', 'CloseOnSee', 'DenyClose' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'No';

