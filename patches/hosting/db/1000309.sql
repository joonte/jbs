

ALTER TABLE `ExtraIPSchemes` DROP `IsAutomatic`;
-- SEPARATOR
ALTER TABLE `ExtraIPSchemes` DROP `HostingGroupID`;
-- SEPARATOR
ALTER TABLE `ExtraIPSchemes` DROP `VPSGroupID`;
-- SEPARATOR
ALTER TABLE `ExtraIPSchemes` DROP `DSGroupID`;
-- SEPARATOR
ALTER TABLE `ExtraIPSchemes` ADD `Params` varchar(1024) AFTER `MinOrdersPeriod`;
-- SEPARATOR
ALTER TABLE `ExtraIPOrders` DROP `OrderType`;
-- SEPARATOR
ALTER TABLE `ExtraIPOrders` DROP `ServerID`;



