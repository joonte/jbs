
ALTER TABLE `VPSServers` ADD `IsAutoBalancing` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'yes' AFTER `IsDefault` ;

-- SEPARATOR

INSERT INTO `Tasks` (`ID`, `UserID`, `TypeID`, `Params`, `IsActive`) VALUES (65, 1, 'VPSSetPrimaryServer', '[]', 'yes');



