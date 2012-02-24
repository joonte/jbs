ALTER TABLE `DomainsOrders` ADD `Ns4Name` char(50) default '' AFTER `Ns3IP`;
-- SEPARATOR
ALTER TABLE `DomainsOrders` ADD `Ns4IP` char(16) default '' AFTER `Ns4Name`;
-- SEPARATOR
ALTER TABLE `HostingServers` ADD `Ns4Name` char(30) default '' AFTER `Ns3Name`;
-- SEPARATOR
ALTER TABLE `Registrators` ADD `Ns4Name` char(50) default '' AFTER `Ns3Name`;