
ALTER TABLE `HostingSchemes` ADD `MinDaysProlong` INT(6) NOT NULL AFTER `MinDaysPay`;

-- SEPARATOR

UPDATE `HostingSchemes` SET `MinDaysProlong` = `MinDaysPay` WHERE `MinDaysProlong` = 0;

