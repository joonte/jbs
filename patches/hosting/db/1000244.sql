
ALTER TABLE `HostingSchemes` ADD `MinDaysProlong` INT(6) NOT NULL AFTER `MinDaysPay`;

-- SEPARATOR

UPDATE `HostingSchemes` SET `MinDaysProlong` = `MinDaysPay` WHERE `MinDaysProlong` = 0;

-- SEPARATOR

ALTER TABLE `VPSSchemes` ADD `MinDaysProlong` INT(6) NOT NULL AFTER `MinDaysPay`;

-- SEPARATOR

UPDATE `VPSSchemes` SET `MinDaysProlong` = `MinDaysPay` WHERE `MinDaysProlong` = 0;


