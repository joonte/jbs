
ALTER TABLE `HostingServers` ADD `MySQL` CHAR(30) NOT NULL AFTER `Ns4Name`;
-- SEPARATOR
UPDATE `HostingServers` SET `MySQL` = 'localhost';


