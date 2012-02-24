UPDATE `Clauses` SET `IsPublish` = 'yes';
-- SEPARATOR
ALTER TABLE `Clauses` CHANGE `IsPublish` `IsPublish` enum('no','yes') default 'yes';
