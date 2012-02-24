ALTER TABLE `HostingSchemes` ADD `IsCreateDomains` enum('no','yes') default 'no' AFTER `QuotaWebApp`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageHosting` enum('no','yes') default 'no' AFTER `IsCreateDomains`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageQuota` enum('no','yes') default 'no' AFTER `IsManageHosting`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageSubdomains` enum('no','yes') default 'no' AFTER `IsManageQuota`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsChangeLimits` enum('no','yes') default 'no' AFTER `IsManageSubdomains`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageLog` enum('no','yes') default 'no' AFTER `IsChangeLimits`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageCrontab` enum('no','yes') default 'no' AFTER `IsManageLog`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageAnonFtp` enum('no','yes') default 'no' AFTER `IsManageCrontab`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageWebapps` enum('no','yes') default 'no' AFTER `IsManageAnonFtp`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageMaillists` enum('no','yes') default 'no' AFTER `IsManageWebapps`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageDrWeb` enum('no','yes') default 'no' AFTER `IsManageMaillists`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsMakeDumps` enum('no','yes') default 'no' AFTER `IsManageDrWeb`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsSiteBuilder` enum('no','yes') default 'no' AFTER `IsMakeDumps`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsRemoteInterface` enum('no','yes') default 'no' AFTER `IsSiteBuilder`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManagePerformance` enum('no','yes') default 'no' AFTER `IsRemoteInterface`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsCpAccess` enum('no','yes') default 'no' AFTER `IsManagePerformance`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageDomainAliases` enum('no','yes') default 'no' AFTER `IsCpAccess`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageIISAppPool` enum('no','yes') default 'no' AFTER `IsManageDomainAliases`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsDashBoard` enum('no','yes') default 'no' AFTER `IsManageIISAppPool`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsStdGIU` enum('no','yes') default 'no' AFTER `IsDashBoard`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageDashboard` enum('no','yes') default 'no' AFTER `IsStdGIU`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsManageSubFtp` enum('no','yes') default 'no' AFTER `IsManageDashboard`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `ISManageSpamFilter` enum('no','yes') default 'no' AFTER `IsManageDashboard`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsLocalBackups` enum('no','yes') default 'no' AFTER `ISManageSpamFilter`;
-- SEPARATOR
ALTER TABLE `HostingSchemes` ADD `IsFtpBackups` enum('no','yes') default 'no' AFTER `IsLocalBackups`;