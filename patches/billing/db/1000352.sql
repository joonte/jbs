
ALTER TABLE `Contacts` ADD `IsImmediate` enum('no','yes') default 'yes' AFTER `IsSendFiles`;

