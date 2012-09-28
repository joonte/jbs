
ALTER TABLE `HostingSchemes` ADD `HardServerID` int(11) NULL AFTER `ServersGroupID`;

-- SEPARATOR

ALTER TABLE `HostingSchemes` ADD INDEX `HostingSchemesServerID` (`HardServerID`);

-- SEPARATOR

ALTER TABLE `HostingSchemes` ADD CONSTRAINT `HostingSchemesHardServerID` FOREIGN KEY (`HardServerID`) REFERENCES `HostingServers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

