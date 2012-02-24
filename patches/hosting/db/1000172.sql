ALTER TABLE `HostingServers` ADD `IPsPool` text AFTER `IP`;
-- SEPARATOR
UPDATE `HostingServers` SET `IPsPool` = `IP`;