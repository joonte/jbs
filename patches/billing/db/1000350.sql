
ALTER TABLE `Contacts` ADD `IsSendFiles` enum('no','yes') default 'yes' AFTER `IsActive`;

