
ALTER TABLE `Contacts` ADD `IsHidden` enum('no','yes') default 'no' AFTER `IsActive`;

