
DROP VIEW IF EXISTS `ServersGroupsOwners`;
DROP TABLE IF EXISTS `ServersGroupsOwners`;
CREATE
  VIEW `ServersGroupsOwners` AS
SELECT
  `ServersGroups`.*,
  100 as `UserID`
FROM
  `ServersGroups`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ServersOwners`;
DROP TABLE IF EXISTS `ServersOwners`;
CREATE
  VIEW `ServersOwners` AS
SELECT
  `Servers`.*,
  100 as `UserID`
FROM
  `Servers`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `HostingOrdersOwners`;
DROP TABLE IF EXISTS `HostingOrdersOwners`;
CREATE
  VIEW `HostingOrdersOwners` AS
SELECT
  `HostingOrders`.*,
  (SELECT `DaysRemainded` FROM `OrdersOwners` WHERE `HostingOrders`.`OrderID` = `OrdersOwners`.`ID`) AS `DaysRemainded`,
  `OrdersOwners`.`ServerID`,
  `OrdersOwners`.`OrderDate`,
  `OrdersOwners`.`UserID`,
  `OrdersOwners`.`ContractID`,
  `OrdersOwners`.`AdminNotice`
FROM
  `HostingOrders`
LEFT JOIN `OrdersOwners`
ON (`HostingOrders`.`OrderID` = `OrdersOwners`.`ID`);
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `DomainsOrdersOwners`;
DROP TABLE IF EXISTS `DomainsOrdersOwners`;
CREATE
  VIEW `DomainsOrdersOwners` AS
SELECT
  `DomainsOrders`.*,
  `DomainsSchemes`.`Name`,
  `DomainsSchemes`.`RegistratorID`,
  `OrdersOwners`.`OrderDate`,
  `OrdersOwners`.`UserID`,
  `OrdersOwners`.`ContractID`,
  `OrdersOwners`.`AdminNotice`
FROM
  `DomainsOrders`
LEFT JOIN `DomainsSchemes`
ON (`DomainsOrders`.`SchemeID` = `DomainsSchemes`.`ID`)
LEFT JOIN `OrdersOwners`
ON (`DomainsOrders`.`OrderID` = `OrdersOwners`.`ID`);
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `HostingBonusesOwners`;
#-------------------------------------------------------------------------------
DROP TABLE IF EXISTS `HostingBonusesOwners`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `HostingPoliticsOwners`;
#-------------------------------------------------------------------------------
DROP TABLE IF EXISTS `HostingPoliticsOwners`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `DomainsBonusesOwners`;
DROP TABLE IF EXISTS `DomainsBonusesOwners`;
CREATE
  VIEW `DomainsBonusesOwners` AS
SELECT
  `DomainsBonuses`.*
FROM
  `DomainsBonuses`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `HostingSchemesOwners`;
DROP TABLE IF EXISTS `HostingSchemesOwners`;
CREATE
  VIEW `HostingSchemesOwners` AS
SELECT
  `HostingSchemes`.*
FROM
  `HostingSchemes`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `DomainsSchemesOwners`;
DROP TABLE IF EXISTS `DomainsSchemesOwners`;
CREATE
  VIEW `DomainsSchemesOwners` AS
SELECT
  `DomainsSchemes`.*,
  CONCAT(`Name`,' (',(SELECT `Name` FROM `Registrators` WHERE `Registrators`.`ID` = `DomainsSchemes`.`RegistratorID`),')') AS PackageID
FROM
  `DomainsSchemes`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `HostingServersGroupsOwners`;
DROP TABLE IF EXISTS `HostingServersGroupsOwners`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `HostingServersOwners`;
DROP TABLE IF EXISTS `HostingServersOwners`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `RegistratorsOwners`;
DROP TABLE IF EXISTS `RegistratorsOwners`;
CREATE
  VIEW `RegistratorsOwners` AS
SELECT
  `Registrators`.*,
  100 as `UserID`
FROM
  `Registrators`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `DomainsSchemesGroupsOwners`;
DROP TABLE IF EXISTS `DomainsSchemesGroupsOwners`;
CREATE
  VIEW `DomainsSchemesGroupsOwners` AS
SELECT
  `DomainsSchemesGroups`.*,
  100 as `UserID`
FROM
  `DomainsSchemesGroups`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `DomainsSchemesGroupsItemsOwners`;
DROP TABLE IF EXISTS `DomainsSchemesGroupsItemsOwners`;
CREATE
  VIEW `DomainsSchemesGroupsItemsOwners` AS
SELECT
  `DomainsSchemesGroupsItems`.*,
  100 as `UserID`
FROM
  `DomainsSchemesGroupsItems`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `HostingDomainsPoliticsOwners`;
DROP TABLE IF EXISTS `HostingDomainsPoliticsOwners`;
CREATE
  VIEW `HostingDomainsPoliticsOwners` AS
SELECT
  `HostingDomainsPolitics`.*
FROM
  `HostingDomainsPolitics`;
#-------------------------------------------------------------------------------

/* VPS values added by lissyara 2011-06-22 in 15:52 MSK */

DROP VIEW IF EXISTS `VPSOrdersOwners`;
DROP TABLE IF EXISTS `VPSOrdersOwners`;
CREATE VIEW `VPSOrdersOwners` AS select
	`VPSOrders`.*,
	(SELECT `DaysRemainded` FROM `OrdersOwners` WHERE `VPSOrders`.`OrderID` = `OrdersOwners`.`ID`) AS `DaysRemainded`,
	`OrdersOwners`.`ServerID`,
	`OrdersOwners`.`OrderDate` AS `OrderDate`,
	`OrdersOwners`.`UserID` AS `UserID`,
	`OrdersOwners`.`ContractID` AS `ContractID`,
	`OrdersOwners`.`AdminNotice`
	FROM (`VPSOrders` LEFT JOIN `OrdersOwners` ON((`VPSOrders`.`OrderID` = `OrdersOwners`.`ID`)));

-- SEPARATOR
DROP VIEW IF EXISTS `VPSServersOwners`;
DROP TABLE IF EXISTS `VPSServersOwners`;
-- SEPARATOR
DROP VIEW IF EXISTS `VPSServersGroupsOwners`;
DROP TABLE IF EXISTS `VPSServersGroupsOwners`;
-- SEPARATOR
DROP VIEW IF EXISTS `VPSBonusesOwners`;
-- SEPARATOR
DROP TABLE IF EXISTS `VPSBonusesOwners`;
-- SEPARATOR
DROP VIEW IF EXISTS `VPSPoliticsOwners`;
-- SEPARATOR
DROP TABLE IF EXISTS `VPSPoliticsOwners`;

-- SEPARATOR
DROP VIEW IF EXISTS `VPSDomainsPoliticsOwners`;
DROP TABLE IF EXISTS `VPSDomainsPoliticsOwners`;

-- SEPARATOR
DROP VIEW IF EXISTS `VPSSchemesOwners`;
DROP TABLE IF EXISTS `VPSSchemesOwners`;
CREATE
	VIEW `VPSSchemesOwners` AS
SELECT
	`VPSSchemes`.*
FROM
	`VPSSchemes`;




/* DS values added by lissyara 2011-06-29 in 20:33 MSK */

DROP VIEW IF EXISTS `DSOrdersOwners`;
DROP TABLE IF EXISTS `DSOrdersOwners`;
CREATE VIEW `DSOrdersOwners` AS select
	`DSOrders`.*,
	(SELECT `DaysRemainded` FROM `OrdersOwners` WHERE `DSOrders`.`OrderID` = `OrdersOwners`.`ID`) AS `DaysRemainded`,
	`OrdersOwners`.`OrderDate` AS `OrderDate`,
	`OrdersOwners`.`UserID` AS `UserID`,
	`OrdersOwners`.`ContractID` AS `ContractID`,
	`OrdersOwners`.`AdminNotice`
	FROM (`DSOrders` LEFT JOIN `OrdersOwners` ON((`DSOrders`.`OrderID` = `OrdersOwners`.`ID`)));

-- SEPARATOR
DROP VIEW IF EXISTS `DSServersOwners`;
DROP TABLE IF EXISTS `DSServersOwners`;
CREATE
	VIEW `DSServersOwners` AS
SELECT
	`DSServers`.*,
	100 as `UserID`
FROM
	`DSServers`;

-- SEPARATOR
DROP VIEW IF EXISTS `DSServersGroupsOwners`;
DROP TABLE IF EXISTS `DSServersGroupsOwners`;
CREATE
	VIEW `DSServersGroupsOwners` AS
SELECT
	`DSServersGroups`.*,
	100 as `UserID`
FROM
	`DSServersGroups`;

-- SEPARATOR
DROP VIEW IF EXISTS `DSBonusesOwners`;
-- SEPARATOR
DROP TABLE IF EXISTS `DSBonusesOwners`;
-- SEPARATOR
DROP VIEW IF EXISTS `DSPoliticsOwners`;
-- SEPARATOR
DROP TABLE IF EXISTS `DSPoliticsOwners`;

-- SEPARATOR
DROP VIEW IF EXISTS `DSDomainsPoliticsOwners`;
DROP TABLE IF EXISTS `DSDomainsPoliticsOwners`;
CREATE VIEW `DSDomainsPoliticsOwners` AS select `DSDomainsPolitics`.* from `DSDomainsPolitics`;


-- SEPARATOR
DROP VIEW IF EXISTS `DSSchemesOwners`;
DROP TABLE IF EXISTS `DSSchemesOwners`;
CREATE
	VIEW `DSSchemesOwners` AS
SELECT
	`DSSchemes`.*
FROM
	`DSSchemes`;


/* ExtraIP values added by lissyara 2011-08-04 in 14:42 MSK */

DROP VIEW IF EXISTS `ExtraIPOrdersOwners`;
DROP TABLE IF EXISTS `ExtraIPOrdersOwners`;
CREATE VIEW `ExtraIPOrdersOwners` AS select
	`ExtraIPOrders`.*,
	(SELECT `DaysRemainded` FROM `OrdersOwners` WHERE `ExtraIPOrders`.`OrderID` = `OrdersOwners`.`ID`) AS `DaysRemainded`,
	`OrdersOwners`.`OrderDate` AS `OrderDate`,
	`OrdersOwners`.`UserID` AS `UserID`,
	`OrdersOwners`.`ContractID` AS `ContractID`,
	`OrdersOwners`.`AdminNotice`
	FROM (`ExtraIPOrders` LEFT JOIN `OrdersOwners` ON((`ExtraIPOrders`.`OrderID` = `OrdersOwners`.`ID`)));

-- SEPARATOR
DROP VIEW IF EXISTS `ExtraIPsOwners`;
DROP TABLE IF EXISTS `ExtraIPsOwners`;
CREATE
	VIEW `ExtraIPsOwners` AS
SELECT
	`ExtraIPs`.*,
	100 as `UserID`
FROM
	`ExtraIPs`;

-- SEPARATOR
DROP VIEW IF EXISTS `ExtraIPsGroupsOwners`;
DROP TABLE IF EXISTS `ExtraIPsGroupsOwners`;
CREATE
	VIEW `ExtraIPsGroupsOwners` AS
SELECT
	`ExtraIPsGroups`.*,
	100 as `UserID`
FROM
	`ExtraIPsGroups`;

-- SEPARATOR
DROP VIEW IF EXISTS `ExtraIPBonusesOwners`;
-- SEPARATOR
DROP TABLE IF EXISTS `ExtraIPBonusesOwners`;
-- SEPARATOR
DROP VIEW IF EXISTS `ExtraIPPoliticsOwners`;
-- SEPARATOR
DROP TABLE IF EXISTS `ExtraIPPoliticsOwners`;

-- SEPARATOR
DROP VIEW IF EXISTS `ExtraIPDomainsPoliticsOwners`;
DROP TABLE IF EXISTS `ExtraIPDomainsPoliticsOwners`;
CREATE VIEW `ExtraIPDomainsPoliticsOwners` AS select `ExtraIPDomainsPolitics`.* from `ExtraIPDomainsPolitics`;

-- SEPARATOR
DROP VIEW IF EXISTS `ExtraIPSchemesOwners`;
DROP TABLE IF EXISTS `ExtraIPSchemesOwners`;
CREATE
	VIEW `ExtraIPSchemesOwners` AS
SELECT
	`ExtraIPSchemes`.*
FROM
	`ExtraIPSchemes`;

/* ISPsw values added by lissyara 2011-09-06 in 15:22 MSK */

-- SEPARATOR

DROP VIEW IF EXISTS `ISPswLicensesOwners`;
DROP TABLE IF EXISTS `ISPswLicensesOwners`;
CREATE VIEW `ISPswLicensesOwners` AS select `ISPswLicenses`.*, 100 as `UserID` FROM `ISPswLicenses`;

-- SEPARATOR
DROP VIEW IF EXISTS `ISPswOrdersOwners`;
DROP TABLE IF EXISTS `ISPswOrdersOwners`;
CREATE VIEW `ISPswOrdersOwners` AS select
	`ISPswOrders`.*,
	(SELECT `DaysRemainded` FROM `OrdersOwners` WHERE `ISPswOrders`.`OrderID` = `OrdersOwners`.`ID`) AS `DaysRemainded`,
	`OrdersOwners`.`OrderDate` AS `OrderDate`,
	`OrdersOwners`.`UserID` AS `UserID`,
	`OrdersOwners`.`ContractID` AS `ContractID`,
	`OrdersOwners`.`AdminNotice`
	FROM (`ISPswOrders` LEFT JOIN `OrdersOwners` ON((`ISPswOrders`.`OrderID` = `OrdersOwners`.`ID`)));

-- SEPARATOR
DROP VIEW IF EXISTS `ISPswGroupsOwners`;
DROP TABLE IF EXISTS `ISPswGroupsOwners`;
CREATE
	VIEW `ISPswGroupsOwners` AS
SELECT
	`ISPswGroups`.*,
	100 as `UserID`
FROM
	`ISPswGroups`;

-- SEPARATOR
DROP VIEW IF EXISTS `ISPswBonusesOwners`;
-- SEPARATOR
DROP TABLE IF EXISTS `ISPswBonusesOwners`;
-- SEPARATOR
DROP VIEW IF EXISTS `ISPswPoliticsOwners`;
-- SEPARATOR
DROP TABLE IF EXISTS `ISPswPoliticsOwners`;


-- SEPARATOR
DROP VIEW IF EXISTS `ISPswDomainsPoliticsOwners`;
DROP TABLE IF EXISTS `ISPswDomainsPoliticsOwners`;
CREATE VIEW `ISPswDomainsPoliticsOwners` AS select `ISPswDomainsPolitics`.* from `ISPswDomainsPolitics`;

-- SEPARATOR
DROP VIEW IF EXISTS `ISPswSchemesOwners`;
DROP TABLE IF EXISTS `ISPswSchemesOwners`;
CREATE
	VIEW `ISPswSchemesOwners` AS
SELECT
	`ISPswSchemes`.*
FROM
	`ISPswSchemes`;

/* added by lissyara, 2014-12-24 in 12:52 MSK */
-- SEPARATOR
DROP VIEW IF EXISTS `DNSmanagerOrdersOwners`;
DROP TABLE IF EXISTS `DNSmanagerOrdersOwners`;
CREATE
	VIEW `DNSmanagerOrdersOwners` AS
SELECT
	`DNSmanagerOrders`.*,
	(SELECT `DaysRemainded` FROM `OrdersOwners` WHERE `DNSmanagerOrders`.`OrderID` = `OrdersOwners`.`ID`) AS `DaysRemainded`,
	`OrdersOwners`.`ServerID`,
	`OrdersOwners`.`OrderDate`,
	`OrdersOwners`.`UserID`,
	`OrdersOwners`.`ContractID`,
	`OrdersOwners`.`AdminNotice`
FROM
	`DNSmanagerOrders` LEFT JOIN `OrdersOwners` ON (`DNSmanagerOrders`.`OrderID` = `OrdersOwners`.`ID`);

-- SEPARATOR

DROP VIEW IF EXISTS `DNSmanagerSchemesOwners`;
DROP TABLE IF EXISTS `DNSmanagerSchemesOwners`;
CREATE
	VIEW `DNSmanagerSchemesOwners` AS
SELECT
	`DNSmanagerSchemes`.*
FROM
	`DNSmanagerSchemes`;




