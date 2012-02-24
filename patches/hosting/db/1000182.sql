ALTER TABLE `HostingServers` ADD  `Theme` char(30) NOT NULL AFTER `Password`;
-- SEPARATOR
ALTER TABLE `HostingServers` ADD  `Language` char(30) NOT NULL AFTER `Theme`;
-- SEPARATOR
UPDATE `HostingServers` SET `Theme` = 'x3',`Language` = 'ru' WHERE `SystemID` = 'Cpanel';
-- SEPARATOR
UPDATE `HostingServers` SET `Theme` = 'sirius',`Language` = 'ru' WHERE `SystemID` = 'IspManager';