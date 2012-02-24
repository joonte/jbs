
ALTER TABLE `HostingServers` ADD `NoRestartCreate`	ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `IsDefault`;
-- SEPARATOR
ALTER TABLE `HostingServers` ADD `NoRestartActive`	ENUM('yes', 'no') NOT NULL DEFAULT 'no' AFTER `NoRestartCreate`;
-- SEPARATOR
ALTER TABLE `HostingServers`  ADD `NoRestartSuspend`	ENUM('yes', 'no') NOT NULL DEFAULT 'no' AFTER `NoRestartActive`;
-- SEPARATOR
ALTER TABLE `HostingServers`  ADD `NoRestartDelete`	ENUM('yes', 'no') NOT NULL DEFAULT 'no' AFTER `NoRestartSuspend`;
-- SEPARATOR
ALTER TABLE `HostingServers`  ADD `NoRestartSchemeChange` ENUM('yes', 'no') NOT NULL DEFAULT 'no' AFTER `NoRestartDelete`;


