
ALTER TABLE `HostingServers` ADD `IsAutoBalancing` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'yes' AFTER `IsDefault` ,
ADD `BalancingFactor` DOUBLE NOT NULL DEFAULT '1' AFTER `IsAutoBalancing` 

