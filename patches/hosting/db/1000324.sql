
ALTER TABLE `Orders` CHANGE `DaysRemainded` `DaysRemainded` INT(11) DEFAULT '0';
-- SEPARATOR
ALTER TABLE `Orders` CHANGE `AdminNotice` `AdminNotice` TEXT;
-- SEPARATOR
ALTER TABLE `Orders` CHANGE `UserNotice` `UserNotice` TEXT;
-- SEPARATOR
ALTER TABLE `Edesks` CHANGE `SeenByPersonal` `SeenByPersonal` INT(11) DEFAULT '0';
-- SEPARATOR
ALTER TABLE `Edesks` CHANGE `LastSeenBy` `LastSeenBy` INT(11) DEFAULT '0';
-- SEPARATOR
ALTER TABLE `Edesks` CHANGE `SeenByUser` `SeenByUser` INT(11) DEFAULT '0';

