ALTER TABLE `HostingSchemes` ADD `IsActive` enum('no','yes') default 'yes' AFTER `IsPHPFastCGIAccess`;