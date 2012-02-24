

ALTER TABLE `Orders` ADD `IsAutoProlong` ENUM('no','yes') default 'yes' AFTER `ServiceID` ;

