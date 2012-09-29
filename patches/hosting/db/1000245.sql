
ALTER TABLE `Services` ADD `IsConditionally` ENUM('no','yes') NOT NULL DEFAULT 'no' AFTER `IsProlong`;

