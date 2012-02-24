ALTER TABLE `HostingSchemes` ADD `MinDaysPay` int(6) default '0' AFTER `IsActive`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `MaxDaysPay` int(6) default '0' AFTER `MinDaysPay`;
-- SEPARATOR
UPDATE `HostingSchemes` SET `MinDaysPay` = 31, `MaxDaysPay` = 1460;