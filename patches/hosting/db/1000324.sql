
ALTER TABLE `Orders` CHANGE `DaysRemainded` `DaysRemainded` INT(11) DEFAULT '0';
-- SEPARATOR
ALTER TABLE `Orders` CHANGE `AdminNotice` `AdminNotice` TEXT;
-- SEPARATOR
ALTER TABLE `Orders` CHANGE `UserNotice` `UserNotice` TEXT;


