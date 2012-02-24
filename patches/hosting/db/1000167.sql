ALTER TABLE `HostingServers` ADD `Domain` char(255) NOT NULL AFTER `Address`;
-- SEPARATOR
UPDATE `HostingServers` SET `Domain` = `Address`;