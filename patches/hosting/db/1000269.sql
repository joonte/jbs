
ALTER TABLE `ISPswSchemes` ADD `ConsiderTypeID` CHAR(30) DEFAULT 'Daily' AFTER `MaxDaysPay`;
-- SEPARATOR
UPDATE `ISPswSchemes` SET `ConsiderTypeID` = 'Upon' WHERE `IsTimeManage` = 'no';
-- SEPARATOR
ALTER TABLE `ISPswSchemes` DROP `IsTimeManage`;

