
ALTER TABLE `DNSmanagerOrders` ADD `Domain` char(255) AFTER `OldSchemeID`;
-- SEPARATOR
ALTER TABLE `DNSmanagerOrders` ADD `Parked` text AFTER `Domain`;

