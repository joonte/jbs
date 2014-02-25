SET FOREIGN_KEY_CHECKS=0;


--
-- Table structure for table `ServersGroups`
--

DROP TABLE IF EXISTS `ServersGroups`;
CREATE TABLE `ServersGroups` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,	-- идентификатор группы
	`Name` char(30) NOT NULL,		-- имя группы
	`ServiceID` int(11) NULL,		-- ссылка на сервис (или NULL, если группа не относится к сервису)
	`FunctionID` char(30) default '',	-- принцип определения того кто IsDefault
	`Comment` char(255) default '',		-- комментарий к группе
	`SortID` int(11) default '10',		-- поле для сортировки
	PRIMARY KEY(`ID`),			-- первичный ключ
	/* внешний ключ на таблицу сервисов */
	KEY `ServersServersGroupID` (`ServersGroupID`),
	CONSTRAINT `ServersServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Servers`
--

DROP TABLE IF EXISTS `Servers`;
CREATE TABLE `Servers` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,		-- идентификатор сервера
	`TemplateID` char(64) default '',		-- шаблон для сервера
	`ServersGroupID` int(11) NULL,			-- группа серверов
	`IsActive` enum('no','yes') default 'yes',	-- активен ли сервер
	`IsDefault` enum('no','yes') default 'no',	-- этот сервер используется "по-умолчанию"
	`Protocol` enum('tcp','ssl') default 'tcp',	-- протокол для связи с сервером
	`Address` char(30) default '',			-- адрес сервера
	`Port` int(5) default '80',			-- порт сервера
	`PrefixAPI` char(127) default '',		-- преффикс используемого API
	`Login` char(60) default '',			-- логин для входа на сервер
	`Password` char(255) default '',		-- пароль для входа на сервер
	`Params` LONGTEXT,				-- набор переменных необходимых для взаимодействия с сервером
	`Monitoring` TEXT,				-- какие сервисы мониторить
	`AdminNotice` TEXT,					-- примечание к серверу
	`SortID` int(11) default '10',			-- поле для сортировки
	PRIMARY KEY(`ID`),
	/* внешний ключ на таблицу групп серверов */
	KEY `ServersServersGroupID` (`ServersGroupID`),
	CONSTRAINT `ServersServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `DomainsOrders`
--

DROP TABLE IF EXISTS `DomainsOrders`;
CREATE TABLE `DomainsOrders` (
  `ID` int(11) NOT NULL auto_increment,
  `OrderID` int(11) NOT NULL,
  `DomainName` char(50) default '',
  `SchemeID` int(11) NOT NULL,
  `ProfileID` int(11) default NULL,
  `PersonID` char(50) default '',
  `DomainID` int(11) default 0,
  `IsPrivateWhoIs` enum('no','yes') default 'no',
  `ExpirationDate` int(11) default '0',
  `Ns1Name` char(50) default '',
  `Ns1IP` char(16) default '',
  `Ns2Name` char(50) default '',
  `Ns2IP` char(16) default '',
  `Ns3Name` char(50) default '',
  `Ns3IP` char(16) default '',
  `Ns4Name` char(50) default '',
  `Ns4IP` char(16) default '',
  `WhoIs` text,
  `AuthInfo` char(64) default NULL,
  `UpdateDate` int(11) default '0',
  `RegUpdateDate` INT(11) default '0',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  PRIMARY KEY  (`ID`),
  KEY `DomainsOrdersOrderID` (`OrderID`),
  CONSTRAINT `DomainsOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainsOrdersSchemeID` (`SchemeID`),
  CONSTRAINT `DomainsOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DomainsSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainsOrdersProfileID` (`ProfileID`),
  CONSTRAINT `DomainsOrdersProfileID` FOREIGN KEY (`ProfileID`) REFERENCES `Profiles` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `DomainsSchemes`
--

DROP TABLE IF EXISTS `DomainsSchemes`;
CREATE TABLE `DomainsSchemes` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `GroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` char(30) default NULL,
  `IsActive` enum('no','yes') default 'no',
  `IsProlong` enum('no','yes') default 'no',
  `CostOrder` decimal(6,2) default '0.00',
  `CostProlong` decimal(6,2) default '0.00',
  `CostTransfer` decimal(6,2) default '0.00',
  `RegistratorID` int(11) NOT NULL,
  `SortID` int(11) default '10',
  `MinOrderYears` int(11) default '1',
  `MaxActionYears` int(11) default '1',
  `MaxOrders` int(6) DEFAULT '0',
  `DaysToProlong` int(11) default '31',
  `DaysBeforeTransfer` INT(3) DEFAULT '60',
  `DaysAfterTransfer` INT(3) DEFAULT '60',
  PRIMARY KEY  (`ID`),
  KEY `DomainsSchemesGroupID` (`GroupID`),
  CONSTRAINT `DomainsSchemesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainsSchemesUserID` (`UserID`),
  CONSTRAINT `DomainsSchemesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainsSchemesRegistratorID` (`RegistratorID`),
  CONSTRAINT `DomainsSchemesRegistratorID` FOREIGN KEY (`RegistratorID`) REFERENCES `Registrators` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `HostingOrders`
--

DROP TABLE IF EXISTS `HostingOrders`;
CREATE TABLE `HostingOrders` (
  `ID` int(11) NOT NULL auto_increment,
  `OrderID` int(11) NOT NULL,
  `SchemeID` int(11) NOT NULL,
  `OldSchemeID` int(11) default NULL,
  `Domain` char(255) default '',
  `Parked` text,
  `ServerID` int(11) NOT NULL,
  `Login` char(20) default '',
  `Password` char(64) default '',
  `ConsiderDay` int(11) default '0',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  PRIMARY KEY  (`ID`),
  KEY `HostingOrdersOrderID` (`OrderID`),
  CONSTRAINT `HostingOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingOrdersSchemeID` (`SchemeID`),
  CONSTRAINT `HostingOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `HostingSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingOrdersServerID` (`ServerID`),
  CONSTRAINT `HostingOrdersServerID` FOREIGN KEY (`ServerID`) REFERENCES `HostingServers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `HostingSchemes`
--

DROP TABLE IF EXISTS `HostingSchemes`;
CREATE TABLE `HostingSchemes` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `GroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` char(30) default '',
  `PackageID` char(30) default '',
  `CostDay` decimal(11,2) default '0.00',
  `CostMonth` decimal(11,2) default '0.00',
  `ServersGroupID` int(11) NOT NULL,
  `HardServerID` int(11) NULL,
  `Comment` char(255) default '',
  `IsReselling` enum('no','yes') default 'no',
  `IsActive` enum('no','yes') default 'yes',
  `IsProlong` enum('no','yes') default 'yes',
  `IsSchemeChangeable` enum('no','yes') default 'yes',
  `IsSchemeChange` enum('no','yes') default 'yes',
  `MinDaysPay` int(6) default '0',			/* минимальное число дней первой оплаты */
  `MinDaysProlong` INT(6) default '0',			/* минимальное число дней продления, для ранее оплаченных заказов */
  `MaxDaysPay` int(6) default '0',			/* максимальное число дней оплаты заказа */
  `MaxOrders` int(6) DEFAULT '0',			/* максимальное число заказов по этому тарифу, на одного пользователя */
  `SortID` int(11) default '10',
--
-- Common quotas
--
  `QuotaDisk` int(11) default '0',
  `QuotaEmail` int(3) default '0',
  `QuotaDomains` int(3) default '0',
  `QuotaFTP` int(3) default '0',
  `QuotaParkDomains` int(3) default '0',
  `QuotaSubDomains` int(3) default '0',
  `QuotaDBs` int(3) default '0',
  `QuotaTraffic` int(6) default '0',
  `QuotaEmailAutoResp` int(3) default '0',
  `QuotaEmailLists` int(3) default '0',
  `QuotaEmailForwards` int(3) default '0',
  `QuotaUsers` int(4) default '0',
  `IsShellAccess` enum('no','yes') default 'no',
  `IsSSLAccess` enum('no','yes') default 'no',
  `IsCGIAccess` enum('no','yes') default 'no',
  `IsDnsControll` enum('no','yes') default 'no',
--
-- Isp Manager quotas
--
  `QuotaWWWDomains` int(3) default '0',
  `QuotaEmailDomains` int(3) default '0',
  `QuotaUsersDBs` int(3) default '0',
  `QuotaCPU` float(7) default '0',
  `MaxExecutionTime` float(7,2) default '0.00',
  `QuotaMEM` float(7,2) default '0.00',
  `QuotaPROC` int(4) default '0',
  `QuotaMPMworkers` INT(2) NOT NULL DEFAULT '1',
  `mysqlquerieslimit` int(12) default '0',
  `mysqlupdateslimit` int(12) default '0',
  `mysqlconnectlimit` int(12) default '0',
  `mysqluserconnectlimit` int(12) default '0',
  `mailrate` int(9) default '100',
  `IsSSIAccess` enum('no','yes') default 'no',
  `IsPHPModAccess` enum('no','yes') default 'no',
  `IsPHPCGIAccess` enum('no','yes') default 'no',
  `IsPHPFastCGIAccess` enum('no','yes') default 'no',
  `IsPHPSafeMode` enum('no','yes') default 'no',
--
-- Cpanel quotas
--
  `QuotaAddonDomains` int(3) default '0',
--
-- Plesk quotas
--
  `QuotaWebUsers` int(3) default '0',
  `QuotaEmailBox` int(6) default '0',
  `QuotaEmailGroups` int(3) default '0',
  `QuotaWebApp` int(3) default '0',
  `IsCreateDomains` enum('no','yes') default 'no',
  `IsManageHosting` enum('no','yes') default 'no',
  `IsManageQuota` enum('no','yes') default 'no',
  `IsManageSubdomains` enum('no','yes') default 'no',
  `IsChangeLimits` enum('no','yes') default 'no',
  `IsManageLog` enum('no','yes') default 'no',
  `IsManageCrontab` enum('no','yes') default 'no',
  `IsManageAnonFtp` enum('no','yes') default 'no',
  `IsManageWebapps` enum('no','yes') default 'no',
  `IsManageMaillists` enum('no','yes') default 'no',
  `IsManageDrWeb` enum('no','yes') default 'no',
  `IsMakeDumps` enum('no','yes') default 'no',
  `IsSiteBuilder` enum('no','yes') default 'no',
  `IsRemoteInterface` enum('no','yes') default 'no',
  `IsManagePerformance` enum('no','yes') default 'no',
  `IsCpAccess` enum('no','yes') default 'no',
  `IsManageDomainAliases` enum('no','yes') default 'no',
  `IsManageIISAppPool` enum('no','yes') default 'no',
  `IsDashBoard` enum('no','yes') default 'no',
  `IsStdGIU` enum('no','yes') default 'no',
  `IsManageDashboard` enum('no','yes') default 'no',
  `IsManageSubFtp` enum('no','yes') default 'no',
  `ISManageSpamFilter` enum('no','yes') default 'no',
  `IsLocalBackups` enum('no','yes') default 'no',
  `IsFtpBackups` enum('no','yes') default 'no',
--
-- DirectAdmin quotas
--
  `IsAnonimousFTP` enum('no','yes') default 'no',
  `IsPHPAccess` enum('no','yes') default 'no',
  `IsSpamAssasing` enum('no','yes') default 'no',
  `IsCatchAll` enum('no','yes') default 'no',
  `IsSystemInfo` enum('no','yes') default 'no',
--
-- additionals fields, for internal use
--
  `field1` VARCHAR(255) NOT NULL,
  `field2` VARCHAR(255) NOT NULL,
  `field3` VARCHAR(255) NOT NULL,

  PRIMARY KEY  (`ID`),
  KEY `HostingSchemesGroupID` (`GroupID`),
  CONSTRAINT `HostingSchemesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingSchemesUserID` (`UserID`),
  CONSTRAINT `HostingSchemesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingSchemesServersGroupID` (`ServersGroupID`),
  CONSTRAINT `HostingSchemesServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `HostingServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingSchemesHardServerID` (`HardServerID`),
  CONSTRAINT `HostingSchemesHardServerID` FOREIGN KEY (`HardServerID`) REFERENCES `HostingServers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `HostingServersGroups`
--

DROP TABLE IF EXISTS `HostingServersGroups`;
CREATE TABLE `HostingServersGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(30) NOT NULL,
  `FunctionID` char(30) default '',
  `Comment` char(255) default '',
  `SortID` int(11) default '10',
  PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `HostingServers`
--

DROP TABLE IF EXISTS `HostingServers`;
CREATE TABLE `HostingServers` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SystemID` char(30) default '',
  `ServersGroupID` int(11) NOT NULL,
  `IsDefault` enum('no','yes') default 'no',
  `IsAutoBalancing` ENUM('yes','no') DEFAULT 'yes',
  `BalancingFactor` DOUBLE NOT NULL DEFAULT '1',
  `NoRestartCreate` ENUM('yes','no') DEFAULT 'no',
  `NoRestartActive` ENUM('yes','no') DEFAULT 'no',
  `NoRestartSuspend` ENUM('yes','no') DEFAULT 'no',
  `NoRestartDelete` ENUM('yes','no') DEFAULT 'no',
  `NoRestartSchemeChange` ENUM('yes','no') DEFAULT 'no',
  `Domain` char(30) default '',
  `Prefix` char(30) default 'h',
  `Address` char(30) default '',
  `Port` int(5) default '80',
  `Protocol` enum('tcp','ssl') default 'tcp',
  `Login` char(60) default '',
  `Password` char(255) default '',
  `IP` char(60) default '127.0.0.1',
  `IPsPool` text,
  `Theme` char(30) default '',
  `Language` char(30) default 'ru',
  `Url` char(60) default '',
  `Ns1Name` char(30) default '',
  `Ns2Name` char(30) default '',
  `Ns3Name` char(30) default '',
  `Ns4Name` char(30) default '',
  `MySQL` char(30) default '',
  `Services` text,
  `TestDate` int(11) default '0',
  `IsOK` enum('no','yes') default 'no',
  `Notice` text,
  PRIMARY KEY(`ID`),
  KEY `HostingServersServersGroupID` (`ServersGroupID`),
  CONSTRAINT `HostingServersServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `HostingServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `Registrators`
--

DROP TABLE IF EXISTS `Registrators`;
CREATE TABLE `Registrators` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(30) default '',
  `TypeID` char(30) default '',
  `Comment` char(255) default '',
  `SortID` int(11) default '10',
  `Address` char(30) default '',
  `Port` int(5) default '80',
  `Protocol` enum('tcp','ssl') default 'tcp',
  `PrefixAPI` char(255) default '',
  `Login` char(60) default '',
  `Password` char(60) default '',
  `Ns1Name` char(50) default '',
  `Ns2Name` char(50) default '',
  `Ns3Name` char(50) default '',
  `Ns4Name` char(50) default '',
  `ParentID` int(20) default '0',
  `PrefixNic` char(20) NOT NULL,
  `PartnerLogin` char(60) NOT NULL,
  `PartnerContract` char(60) NOT NULL,
  `JurName` char(255) NOT NULL,
  `BalanceLowLimit` DOUBLE(7,2) NOT NULL DEFAULT '0',
  PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* join to one Bonuses table */
DROP TABLE IF EXISTS `HostingBonuses`;
/* moved to OrdersConsider */
DROP TABLE IF EXISTS `HostingConsider`;
/* join to one Politics table */
DROP TABLE IF EXISTS `HostingPolitics`;

--
-- Table structure for table `DomainsBonuses`
--

DROP TABLE IF EXISTS `DomainsBonuses`;
CREATE TABLE `DomainsBonuses` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) default '0',
  `UserID` int(11) NOT NULL,
  `SchemeID` int(11) NULL,
  `DomainsSchemesGroupID` int(11) NULL,
  `YearsReserved` int(11) default '0',
  `YearsRemainded` int(11) default '0',
  `OperationID` enum('Order','Prolong') default 'Order',
  `Discont` decimal(11,2) default '0.00',
  `Comment` char(255) default '',
  PRIMARY KEY(`ID`),
  KEY `DomainsBonusesUserID` (`UserID`),
  CONSTRAINT `DomainsBonusesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainsBonusesSchemeID` (`SchemeID`),
  CONSTRAINT `DomainsBonusesSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DomainsSchemes` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  KEY `DomainsBonusesDomainsSchemesGroupID` (`DomainsSchemesGroupID`),
  CONSTRAINT `DomainsBonusesDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `DomainsSchemesGroups`
--

DROP TABLE IF EXISTS `DomainsSchemesGroups`;
CREATE TABLE `DomainsSchemesGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(255) default '',
  PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `DomainsSchemesGroupsItems`
--

DROP TABLE IF EXISTS `DomainsSchemesGroupsItems`;
CREATE TABLE `DomainsSchemesGroupsItems` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DomainsSchemesGroupID` int(11) NOT NULL,
  `SchemeID` int(11) NULL,
  PRIMARY KEY(`ID`),
  KEY `DomainsSchemesGroupsItemsDomainsSchemesGroupID` (`DomainsSchemesGroupID`),
  CONSTRAINT `DomainsSchemesGroupsItemsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainsSchemesGroupsItemsID` (`SchemeID`),
  CONSTRAINT `DomainsSchemesGroupsItemsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DomainsSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `HostingDomainsPolitics`
--

DROP TABLE IF EXISTS `HostingDomainsPolitics`;
CREATE TABLE `HostingDomainsPolitics` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `SchemeID` int(11) NULL,
  `DomainsSchemesGroupID` int(11) NOT NULL,
  `DaysPay` int(11) default '365',
  `Discont` float(11,2) default '0.00',
  PRIMARY KEY(`ID`),
  KEY `HostingDomainsPoliticsGroupID` (`GroupID`),
  CONSTRAINT `HostingDomainsPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingDomainsPoliticsUserID` (`UserID`),
  CONSTRAINT `HostingDomainsPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingDomainsPoliticsSchemeID` (`SchemeID`),
  CONSTRAINT `HostingDomainsPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `HostingSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingDomainsPoliticsDomainsSchemesGroupID` (`DomainsSchemesGroupID`),
  CONSTRAINT `HostingDomainsPoliticsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `DomainsConsider`
--

DROP TABLE IF EXISTS `DomainsConsider`;
CREATE TABLE `DomainsConsider` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) default '0',
  `DomainOrderID` int(11) NOT NULL,
  `YearsReserved` int(11) default '0',
  `YearsRemainded` int(11) default '0',
  `Cost` decimal(11,2) default '0.00',
  `Discont` decimal(11,2) default '0.00',
  PRIMARY KEY(`ID`),
  KEY `DomainsConsiderDomainOrderID` (`DomainOrderID`),
  CONSTRAINT `DomainsConsiderDomainOrderID` FOREIGN KEY (`DomainOrderID`) REFERENCES `DomainsOrders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `ServersUpTime`
--

DROP TABLE IF EXISTS `ServersUpTime`;
CREATE TABLE `ServersUpTime` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ServerID` int(11) default '0',
  `Service` char(20) default 'HTTP',
  `TestDate` int(11) default '0',
  `Day` int(2) default '0',
  `Month` int(2) default '0',
  `Year` int(4) default '0',
  `UpTime` float(11) default '0',
  `Count` int(11) default '1',
  PRIMARY KEY(`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* added by serge 2011-09-13 in 14:35 MSK */
ALTER TABLE `ServersUpTime` ADD INDEX ( `ServerID` );

/* VPS values added by lissyara 2011-06-22 in 15:43 MSK */

CREATE TABLE IF NOT EXISTS `VPSServersGroups` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`Name` char(30) NOT NULL,
	`FunctionID` char(30) DEFAULT '',
	`Comment` char(255) DEFAULT '',
	`SortID` int(11) DEFAULT '10',
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
CREATE TABLE IF NOT EXISTS `VPSSchemes` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) DEFAULT '0',
	`GroupID` int(11) NOT NULL,
	`UserID` int(11) NOT NULL,
	`Name` char(30) DEFAULT '',
	`PackageID` char(30) DEFAULT '',
	`CostDay` decimal(11,2) DEFAULT '0.00',
	`CostMonth` decimal(11,2) DEFAULT '0.00',
	`CostInstall` decimal(11,2) DEFAULT '0.00',
	`ServersGroupID` int(11) NOT NULL,
	`Comment` char(255) NOT NULL,
	`IsReselling` enum('no','yes') DEFAULT 'no',
	`IsActive` enum('no','yes') DEFAULT 'yes',
	`IsProlong` enum('no','yes') DEFAULT 'yes',
	`IsSchemeChangeable` enum('no','yes') DEFAULT 'yes',
	`IsSchemeChange` enum('no','yes') DEFAULT 'yes',
	`MinDaysPay` int(6) DEFAULT '0',
	`MinDaysProlong` int(6) DEFAULT '0',
	`MaxDaysPay` int(6) DEFAULT '0',
	`MaxOrders` int(6) DEFAULT '0',
	`SortID` int(11) DEFAULT '10',
	`vdslimit` int(3) NOT NULL,
	`QuotaUsers` int(4) DEFAULT '0',
	`disklimit` int(11) DEFAULT '0',
	`ncpu` int(2) NOT NULL,
	`cpu` int(7) DEFAULT '0',
	`mem` float(7,2) DEFAULT '0.00',
	`bmem` float(7,2) DEFAULT '0.00',
	`maxswap` float(7,2) NOT NULL,
	`traf` int(6) DEFAULT '0',
	`chrate` int(4) NOT NULL,
	`maxdesc` int(9) NOT NULL,
	`proc` int(4) DEFAULT '0',
	`ipalias` int(4) NOT NULL,
	`disktempl` varchar(128) NOT NULL,
	`extns` VARCHAR(32) NOT NULL,
	`limitpvtdns` INT(6) DEFAULT '0',
	`limitpubdns` INT(6) DEFAULT '0',
	`backup` CHAR(16) NOT NULL,
	PRIMARY KEY (`ID`),
	KEY `VPSSchemesGroupID` (`GroupID`),
	KEY `VPSSchemesUserID` (`UserID`),
	KEY `VPSSchemesServersGroupID` (`ServersGroupID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR


CREATE TABLE IF NOT EXISTS `VPSServers` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`SystemID` char(30) DEFAULT '',
	`ServersGroupID` int(11) NOT NULL,
	`IsDefault` enum('no','yes') DEFAULT 'no',
	`IsAutoBalancing` enum('no','yes') DEFAULT 'yes',
	`BalancingFactor` DOUBLE NOT NULL DEFAULT '1', 
	`Domain` char(30) DEFAULT '',
	`Prefix` char(30) DEFAULT 'h',
	`Address` char(30) DEFAULT '',
	`Port` int(5) DEFAULT '80',
	`Protocol` enum('tcp','ssl') DEFAULT 'tcp',
	`Login` char(60) DEFAULT '',
	`Password` char(255) DEFAULT '',
	`IP` char(60) DEFAULT '127.0.0.1',
	`IPsPool` text,
	`Theme` char(30) DEFAULT '',
	`Language` char(30) DEFAULT 'ru',
	`Url` char(60) DEFAULT '',
	`Ns1Name` char(30) DEFAULT '',
	`Ns2Name` char(30) DEFAULT '',
	`Ns3Name` char(30) DEFAULT '',
	`Ns4Name` char(30) DEFAULT '',
	`Services` text,
	`TestDate` int(11) DEFAULT '0',
	`IsOK` enum('no','yes') DEFAULT 'no',
	`Notice` text,
	PRIMARY KEY (`ID`),
	KEY `VPSServersServersGroupID` (`ServersGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `VPSServers`
	ADD CONSTRAINT `VPSServersServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `VPSServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR


CREATE TABLE IF NOT EXISTS `VPSOrders` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`OrderID` int(11) NOT NULL,
	`SchemeID` int(11) NOT NULL,
	`OldSchemeID` int(11) DEFAULT NULL,
	`Domain` char(255) DEFAULT '',
	`Parked` text,
	`ServerID` int(11) NOT NULL,
	`Login` char(20) DEFAULT '',
	`Password` char(64) DEFAULT '',
	`ConsiderDay` int(11) DEFAULT '0',
	`StatusID` char(30) DEFAULT 'UnSeted',
	`StatusDate` int(11) DEFAULT '0',
	PRIMARY KEY (`ID`),
	KEY `VPSOrdersOrderID` (`OrderID`),
	KEY `VPSOrdersSchemeID` (`SchemeID`),
	KEY `VPSOrdersServerID` (`ServerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `VPSOrders`
ADD CONSTRAINT `VPSOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `VPSOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `VPSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `VPSOrdersServerID` FOREIGN KEY (`ServerID`) REFERENCES `VPSServers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR
/* join to one Politics table */
DROP TABLE IF EXISTS `VPSPolitics`;

-- SEPARATOR
CREATE TABLE IF NOT EXISTS `VPSDomainsPolitics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,
	`GroupID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DomainsSchemesGroupID` int(11) NOT NULL,
	`DaysPay` int(11) DEFAULT '365',
	`Discont` float(11,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `VPSDomainsPoliticsGroupID` (`GroupID`),
	KEY `VPSDomainsPoliticsUserID` (`UserID`),
	KEY `VPSDomainsPoliticsSchemeID` (`SchemeID`),
	KEY `VPSDomainsPoliticsDomainsSchemesGroupID` (`DomainsSchemesGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `VPSDomainsPolitics`
	ADD CONSTRAINT `VPSDomainsPoliticsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `VPSDomainsPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `VPSDomainsPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `VPSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `VPSDomainsPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

/* join to one Bonuses Table */
DROP TABLE IF EXISTS `VPSBonuses`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TABLE IF EXISTS `VPSConsider`;

-- SEPARATOR
/* DS values added by lissyara 2011-06-29 in 20:31 MSK */

CREATE TABLE IF NOT EXISTS `DSServersGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(30) NOT NULL,
  `FunctionID` char(30) DEFAULT '',
  `SystemID` char(127) NOT NULL,
  `Comment` char(255) DEFAULT '',
  `SortID` int(11) DEFAULT '10',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `DSSchemes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) DEFAULT '0',
  `GroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` char(30) DEFAULT '',
  `PackageID` char(30) DEFAULT '',
  `CostDay` decimal(11,2) DEFAULT '0.00',
  `CostMonth` decimal(11,2) DEFAULT '0.00',
  `CostInstall` decimal(11,2) NOT NULL DEFAULT '0.00',
  `ServersGroupID` int(11) NOT NULL,
  `NumServers` int(4) NOT NULL,
  `RemainServers` int(4) NOT NULL,
  `IsCalculateNumServers` enum('no','yes') NOT NULL,
  `IsActive` enum('no','yes') DEFAULT 'yes',
  `IsProlong` enum('no','yes') NOT NULL,
  `MinDaysPay` int(6) DEFAULT '0',
  `MinDaysProlong` int(6) DEFAULT '0',
  `MaxDaysPay` int(6) DEFAULT '0',
  `MaxOrders` int(6) DEFAULT '0',
  `SortID` int(11) DEFAULT '10',
  `cputype` char(16) NOT NULL,
  `cpuarch` char(16) NOT NULL,
  `numcpu` int(2) NOT NULL,
  `numcores` int(3) NOT NULL,
  `cpufreq` int(5) NOT NULL,
  `ram` int(6) NOT NULL,
  `raid` char(128) NOT NULL,
  `disk1` char(128) NOT NULL,
  `disk2` char(128) NOT NULL,
  `disk3` char(128) NOT NULL,
  `disk4` char(128) NOT NULL,
  `chrate` float(9,2) NOT NULL,
  `trafflimit` float(9,2) NOT NULL,
  `traffcorrelation` char(128) NOT NULL,
  `OS` char(128) NOT NULL,
  `UserComment` char(255) NOT NULL,
  `AdminComment` char(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `DSSchemesGroupID` (`GroupID`),
  KEY `DSSchemesUserID` (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- SEPARATOR


CREATE TABLE IF NOT EXISTS `DSServers` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`SystemID` char(30) DEFAULT '',
	`ServersGroupID` int(11) NOT NULL,
	`IsDefault` enum('no','yes') DEFAULT 'no',
	`Domain` char(30) DEFAULT '',
	`Prefix` char(30) DEFAULT 'h',
	`Address` char(30) DEFAULT '',
	`Port` int(5) DEFAULT '80',
	`Protocol` enum('tcp','ssl') DEFAULT 'tcp',
	`Login` char(60) DEFAULT '',
	`Password` char(255) DEFAULT '',
	`IP` char(60) DEFAULT '127.0.0.1',
	`IPsPool` text,
	`Theme` char(30) DEFAULT '',
	`Language` char(30) DEFAULT 'ru',
	`Url` char(60) DEFAULT '',
	`Ns1Name` char(30) DEFAULT '',
	`Ns2Name` char(30) DEFAULT '',
	`Ns3Name` char(30) DEFAULT '',
	`Ns4Name` char(30) DEFAULT '',
	`Services` text,
	`TestDate` int(11) DEFAULT '0',
	`IsOK` enum('no','yes') DEFAULT 'no',
	`Notice` text,
	PRIMARY KEY (`ID`),
	KEY `DSServersServersGroupID` (`ServersGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `DSServers`
	ADD CONSTRAINT `DSServersServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `DSServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR


CREATE TABLE IF NOT EXISTS `DSOrders` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `SchemeID` int(11) NOT NULL,
  `IP` char(32) NOT NULL DEFAULT 'noassign',
  `ExtraIP` text NOT NULL,
  `ConsiderDay` int(11) DEFAULT '0',
  `StatusID` char(30) DEFAULT 'UnSeted',
  `StatusDate` int(11) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `DSOrdersOrderID` (`OrderID`),
  KEY `DSOrdersSchemeID` (`SchemeID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

ALTER TABLE `DSOrders`
  ADD CONSTRAINT `DSOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `DSOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR
/* join to one Politics table */
DROP TABLE IF EXISTS `DSPolitics`;

-- SEPARATOR
CREATE TABLE IF NOT EXISTS `DSDomainsPolitics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,
	`GroupID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DomainsSchemesGroupID` int(11) NOT NULL,
	`DaysPay` int(11) DEFAULT '365',
	`Discont` float(11,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `DSDomainsPoliticsGroupID` (`GroupID`),
	KEY `DSDomainsPoliticsUserID` (`UserID`),
	KEY `DSDomainsPoliticsSchemeID` (`SchemeID`),
	KEY `DSDomainsPoliticsDomainsSchemesGroupID` (`DomainsSchemesGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `DSDomainsPolitics`
	ADD CONSTRAINT `DSDomainsPoliticsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `DSDomainsPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `DSDomainsPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `DSDomainsPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

/* join to one Bonuses table */
DROP TABLE IF EXISTS `DSBonuses`;

-- SEPARATOR
/* moved to OrdersConsider */
DROP TABLE IF EXISTS `DSConsider`;

-- SEPARATOR

/* join to one Bonuses table */
DROP TABLE IF EXISTS `ExtraIPBonuses`;

-- SEPARATOR
/* moved to OrdersConsider */
DROP TABLE IF EXISTS `ExtraIPConsider`;

-- SEPARATOR

--
-- Структура таблицы `ExtraIPDomainsPolitics`
--

CREATE TABLE IF NOT EXISTS `ExtraIPDomainsPolitics` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `SchemeID` int(11) DEFAULT NULL,
  `DomainsSchemesGroupID` int(11) NOT NULL,
  `DaysPay` int(11) DEFAULT '365',
  `Discont` float(11,2) DEFAULT '0.00',
  PRIMARY KEY (`ID`),
  KEY `ExtraIPDomainsPoliticsGroupID` (`GroupID`),
  KEY `ExtraIPDomainsPoliticsUserID` (`UserID`),
  KEY `ExtraIPDomainsPoliticsSchemeID` (`SchemeID`),
  KEY `ExtraIPDomainsPoliticsDomainsSchemesGroupID` (`DomainsSchemesGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

--
-- Структура таблицы `ExtraIPOrders`
--

CREATE TABLE IF NOT EXISTS `ExtraIPOrders` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `SchemeID` int(11) NOT NULL,
  `OldSchemeID` int(11) DEFAULT NULL,
  `OrderType` char(32) NOT NULL,
  `DependOrderID` int(11) NOT NULL,
  `Domain` char(255) DEFAULT '',
  `Parked` text,
  `ServerID` int(11) NOT NULL,
  `Login` char(32) DEFAULT 'noassign',
  `Password` char(64) DEFAULT '',
  `ConsiderDay` int(11) DEFAULT '0',
  `StatusID` char(30) DEFAULT 'UnSeted',
  `StatusDate` int(11) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `ExtraIPOrdersOrderID` (`OrderID`),
  KEY `ExtraIPOrdersSchemeID` (`SchemeID`),
  KEY `ExtraIPOrdersServerID` (`ServerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR
/* join to one Politics table */
DROP TABLE IF EXISTS `ExtraIPPolitics`;

--
-- Структура таблицы `ExtraIPs`
--

CREATE TABLE IF NOT EXISTS `ExtraIPs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SystemID` char(30) DEFAULT '',
  `sGroupID` int(11) NOT NULL,
  `IsDefault` enum('no','yes') DEFAULT 'no',
  `Domain` char(30) DEFAULT '',
  `Prefix` char(30) DEFAULT 'h',
  `Address` char(30) DEFAULT '',
  `Port` int(5) DEFAULT '80',
  `Protocol` enum('tcp','ssl') DEFAULT 'tcp',
  `Login` char(60) DEFAULT '',
  `Password` char(255) DEFAULT '',
  `IP` char(60) DEFAULT '127.0.0.1',
  `IPsPool` text,
  `Theme` char(30) DEFAULT '',
  `Language` char(30) DEFAULT 'ru',
  `Url` char(60) DEFAULT '',
  `Ns1Name` char(30) DEFAULT '',
  `Ns2Name` char(30) DEFAULT '',
  `Ns3Name` char(30) DEFAULT '',
  `Ns4Name` char(30) DEFAULT '',
  `Services` text,
  `TestDate` int(11) DEFAULT '0',
  `IsOK` enum('no','yes') DEFAULT 'no',
  `Notice` text,
  PRIMARY KEY (`ID`),
  KEY `ExtraIPssGroupID` (`sGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- SEPARATOR

--
-- Структура таблицы `ExtraIPSchemes`
--

CREATE TABLE IF NOT EXISTS `ExtraIPSchemes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) DEFAULT '0',
  `GroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` char(30) DEFAULT '',
  `PackageID` char(30) DEFAULT '',
  `CostDay` decimal(11,2) DEFAULT '0.00',
  `CostMonth` decimal(11,2) DEFAULT '0.00',
  `CostInstall` decimal(11,2) NOT NULL,
  `AddressType` CHAR( 8 ) NOT NULL DEFAULT 'IPv4',
  `HostingGroupID` int(11) NOT NULL,
  `VPSGroupID` int(11) NOT NULL,
  `DSGroupID` int(11) NOT NULL,
  `Comment` char(255) NOT NULL,
  `IsAutomatic` enum('no','yes') DEFAULT 'no',
  `IsActive` enum('no','yes') DEFAULT 'yes',
  `IsProlong` enum('no','yes') DEFAULT 'yes',
  `MinDaysPay` int(6) DEFAULT '0',
  `MinDaysProlong` int(6) DEFAULT '0',
  `MaxDaysPay` int(6) DEFAULT '0',
  `MaxOrders` int(6) DEFAULT '0',
  `SortID` int(11) DEFAULT '10',
  PRIMARY KEY (`ID`),
  KEY `ExtraIPSchemesGroupID` (`GroupID`),
  KEY `ExtraIPSchemesUserID` (`UserID`),
  KEY `ExtraIPSchemessGroupID` (`HostingGroupID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- SEPARATOR

--
-- Структура таблицы `ExtraIPsGroups`
--

CREATE TABLE IF NOT EXISTS `ExtraIPsGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(30) NOT NULL,
  `FunctionID` char(30) DEFAULT '',
  `Comment` char(255) DEFAULT '',
  `SortID` int(11) DEFAULT '10',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- SEPARATOR

--
-- Ограничения внешнего ключа таблицы `ExtraIPDomainsPolitics`
--
ALTER TABLE `ExtraIPDomainsPolitics`
  ADD CONSTRAINT `ExtraIPDomainsPoliticsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPDomainsPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPDomainsPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ExtraIPSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPDomainsPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

--
-- Ограничения внешнего ключа таблицы `ExtraIPOrders`
--
ALTER TABLE `ExtraIPOrders`
  ADD CONSTRAINT `ExtraIPOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ExtraIPOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ExtraIPSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

--
-- Ограничения внешнего ключа таблицы `ExtraIPs`
--
ALTER TABLE `ExtraIPs`
  ADD CONSTRAINT `ExtraIPssGroupID` FOREIGN KEY (`sGroupID`) REFERENCES `ExtraIPsGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;



/* ISPsw values added by lissyara 2011-09-06 in 15:24 MSK */

-- SEPARATOR

DROP TABLE IF EXISTS `ISPswLicenses`;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `ISPswLicenses` (
	`ID` int(9) NOT NULL AUTO_INCREMENT,
	`ISPtype` char(12) NOT NULL,
	`IP` char(64) NOT NULL,
	`elid` int(12) NOT NULL,
	`IsInternal` enum('yes','no') NOT NULL DEFAULT 'yes',
	`IsUsed` enum('yes','no') NOT NULL DEFAULT 'yes',
	`ISPname` text,
	`StatusID` char(30) DEFAULT 'UnSeted',
	`CreateDate` int(11) NOT NULL,
	`UpdateDate` int(11) NOT NULL,
	`StatusDate` int(11) NOT NULL,
	`ExpireDate` int(11) NOT NULL,
	`Flag` char(32) NOT NULL,
	PRIMARY KEY (`ID`),
	UNIQUE KEY `elid` (`elid`),
	KEY `IP` (`IP`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- SEPARATOR

CREATE TABLE IF NOT EXISTS `ISPswGroups` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`Name` char(60) DEFAULT '',
	`Comment` text,
	`Address` char(60) DEFAULT '',
	`SortID` text,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `ISPswSchemes` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) DEFAULT '0',
	`GroupID` int(11) NOT NULL,
	`UserID` int(11) NOT NULL,
	`Name` char(30) DEFAULT '',
	`PackageID` char(30) DEFAULT '',
	`CostDay` decimal(11,2) DEFAULT '0.00',
	`CostMonth` decimal(11,2) DEFAULT '0.00',
	`CostInstall` decimal(11,2) DEFAULT '0.00',
	`SoftWareGroup` int(11) NOT NULL,
	`Comment` char(255) NOT NULL,
	`IsActive` enum('no','yes') DEFAULT 'yes',
	`IsProlong` enum('no','yes') DEFAULT 'yes',
	`IsSchemeChangeable` enum('no','yes') DEFAULT 'yes',
	`IsSchemeChange` enum('no','yes') DEFAULT 'yes',
	`IsInternal` ENUM('no','yes') DEFAULT 'no',
	`MinDaysPay` int(6) DEFAULT '0',
	`MinDaysProlong` int(6) DEFAULT '0',
	`MaxDaysPay` int(6) DEFAULT '0',
	`MaxOrders` int(6) DEFAULT '0',
	`ConsiderTypeID` char(30) default 'Daily',
	`SortID` int(11) DEFAULT '10',
	`ISPtype` char(12) DEFAULT '7:7',
	PRIMARY KEY (`ID`),
	KEY `ISPswSchemesGroupID` (`GroupID`),
	KEY `ISPswSchemesUserID` (`UserID`),
	KEY `ISPswSchemesSoftWareGroupID` (`SoftWareGroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

ALTER TABLE `ISPswSchemes`
ADD CONSTRAINT `ISPswSchemesSoftWareGroupID` FOREIGN KEY (`SoftWareGroup`) REFERENCES `ISPswGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `ISPswOrders` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`OrderID` int(11) NOT NULL,
	`SchemeID` int(11) NOT NULL,
	`OldSchemeID` int(11) DEFAULT NULL,
	`IP` text,
	`LicenseID` INT(12) DEFAULT NULL,
	`ConsiderDay` int(11) DEFAULT '0',
	`StatusID` char(30) DEFAULT 'UnSeted',
	`StatusDate` int(11) DEFAULT '0',
	PRIMARY KEY (`ID`),
	KEY `ISPswOrdersOrderID` (`OrderID`),
	KEY `ISPswOrdersSchemeID` (`SchemeID`),
	KEY `ISPswLicenseID` (`LicenseID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `ISPswOrders`
ADD CONSTRAINT `ISPswOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `ISPswOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ISPswSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `ISPswOrdersLicenseID` FOREIGN KEY (`LicenseID`) REFERENCES `ISPswLicenses` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;


-- SEPARATOR
/* join to one Politics table */
DROP TABLE IF EXISTS `ISPswPolitics`;

-- SEPARATOR
CREATE TABLE IF NOT EXISTS `ISPswDomainsPolitics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,
	`GroupID` int(11) NOT NULL,
	`SchemeID` int(11) DEFAULT NULL,
	`DomainsSchemesGroupID` int(11) NOT NULL,
	`DaysPay` int(11) DEFAULT '365',
	`Discont` float(11,2) DEFAULT '0.00',
	PRIMARY KEY (`ID`),
	KEY `ISPswDomainsPoliticsGroupID` (`GroupID`),
	KEY `ISPswDomainsPoliticsUserID` (`UserID`),
	KEY `ISPswDomainsPoliticsSchemeID` (`SchemeID`),
	KEY `ISPswDomainsPoliticsDomainsSchemesGroupID` (`DomainsSchemesGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- SEPARATOR
ALTER TABLE `ISPswDomainsPolitics`
	ADD CONSTRAINT `ISPswDomainsPoliticsDomainsSchemesGroupID` FOREIGN KEY (`DomainsSchemesGroupID`) REFERENCES `DomainsSchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `ISPswDomainsPoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `ISPswDomainsPoliticsSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ISPswSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `ISPswDomainsPoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- SEPARATOR
/* join to one Bonuses table */
DROP TABLE IF EXISTS `ISPswBonuses`;
-- SEPARATOR

/* moved to OrdersConsider */
DROP TABLE IF EXISTS `ISPswConsider`;

-- SEPARATOR
/* общая таблица для учёта. реализация JBS-300 */
DROP TABLE IF EXISTS `OrdersConsider`;
CREATE TABLE `OrdersConsider` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) default '0',
  `OrderID` int(11) NOT NULL,
  `DaysReserved` int(11) default '0',
  `DaysRemainded` int(11) default '0',
  `DaysConsidered` int(11) default '0',
  `Cost` decimal(11,2) default '0.00',
  `Discont` decimal(11,2) default '0.00',
  PRIMARY KEY(`ID`),
  KEY `OrdersConsiderOrderID` (`OrderID`),
  CONSTRAINT `OrdersConsiderOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS=1;

