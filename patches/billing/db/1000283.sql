
ALTER TABLE `HostingOrders` ADD `UserNotice` TEXT NOT NULL, ADD `AdminNotice` TEXT NOT NULL;
-- SEPARATOR
ALTER TABLE `DomainsOrders`  ADD `UserNotice` TEXT NOT NULL,  ADD `AdminNotice` TEXT NOT NULL;


