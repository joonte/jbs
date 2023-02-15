SET FOREIGN_KEY_CHECKS=0;


--
-- Table structure for table `ServersGroups`
--

DROP TABLE IF EXISTS `ServersGroups`;
CREATE TABLE `ServersGroups` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,		-- идентификатор группы
	`Name` char(30) NOT NULL,			-- имя группы
	`ServiceID` int(11) NULL,			-- ссылка на сервис (или NULL, если группа не относится к сервису)
	`FunctionID` char(30) default '',		-- принцип определения того кто IsDefault
	`IsCheckUsers` enum('no','yes') default 'yes',	-- JBS-910: проверка соответствия юзеров на серверах группы и в биллинге
	`Params` LONGTEXT NOT NULL,			-- дополнительные параметры группы серверов (зависимые услуги)
	`Comment` char(255) default '',			-- комментарий к группе
	`SortID` int(11) default '10',			-- поле для сортировки
	PRIMARY KEY(`ID`),				-- первичный ключ
	/* внешний ключ на таблицу сервисов */
	KEY `ServersGroupsServiceID` (`ServiceID`),
	CONSTRAINT `ServersGroupsServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
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
	`Login` char(60) default '',			-- логин для входа на сервер
	`Password` char(255) default '',		-- пароль для входа на сервер
	`Params` LONGTEXT,				-- набор переменных необходимых для взаимодействия с сервером
	`Monitoring` TEXT,				-- какие сервисы мониторить
	`TestDate` int(11) default '0',			-- дата последнего тестирования мониторингом
	`IsOK` int(3) NULL DEFAULT NULL,		-- с каким итогом был последний мониторинг
	`AdminNotice` TEXT,				-- примечание к серверу
	`SortID` int(11) default '10',			-- поле для сортировки
	PRIMARY KEY(`ID`),
	/* внешний ключ на таблицу групп серверов */
	KEY `ServersServersGroupID` (`ServersGroupID`),
	CONSTRAINT `ServersServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `DomainOrders`
--

DROP TABLE IF EXISTS `DomainOrders`;
CREATE TABLE `DomainOrders` (
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
  `Ns1IP` char(64) default '',
  `Ns2Name` char(64) default '',
  `Ns2IP` char(64) default '',
  `Ns3Name` char(64) default '',
  `Ns3IP` char(64) default '',
  `Ns4Name` char(64) default '',
  `Ns4IP` char(64) default '',
  `WhoIs` text,
  `AuthInfo` char(64) default NULL,
  `UpdateDate` int(11) default '0',
  `RegUpdateDate` INT(11) default '0',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  PRIMARY KEY  (`ID`),
  KEY `DomainOrdersOrderID` (`OrderID`),
  CONSTRAINT `DomainOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainOrdersSchemeID` (`SchemeID`),
  CONSTRAINT `DomainOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DomainSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainOrdersProfileID` (`ProfileID`),
  CONSTRAINT `DomainOrdersProfileID` FOREIGN KEY (`ProfileID`) REFERENCES `Profiles` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `DomainSchemes`
--

DROP TABLE IF EXISTS `DomainSchemes`;
CREATE TABLE `DomainSchemes` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `GroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` char(30) default NULL,
  `IsActive` enum('no','yes') default 'no',
  `IsProlong` enum('no','yes') default 'no',
  `IsTransfer` enum('no','yes') default 'no',
  `IsAutoBalanced` ENUM('yes','no') NOT NULL DEFAULT 'no',
  `CostOrder` decimal(11,2) default '0.00',
  `CostProlong` decimal(11,2) default '0.00',
  `CostTransfer` decimal(11,2) default '0.00',
  `ServerID` int(11) NOT NULL,
  `SortID` int(11) default '10',
  `MinOrderYears` int(11) default '1',
  `MaxActionYears` int(11) default '1',
  `MaxOrders` int(6) DEFAULT '0',
  `MinOrdersPeriod` INT(6) DEFAULT '0',
  `DaysToProlong` int(11) default '31',
  `DaysBeforeTransfer` INT(3) DEFAULT '60',
  `DaysAfterTransfer` INT(3) DEFAULT '60',
  `Params` VARCHAR(1024) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `DomainSchemesGroupID` (`GroupID`),
  CONSTRAINT `DomainSchemesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainSchemesUserID` (`UserID`),
  CONSTRAINT `DomainSchemesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `DomainSchemesServerID` (`ServerID`),
  CONSTRAINT `DomainSchemesServerID` FOREIGN KEY (`ServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
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
  `Login` char(20) default '',
  `Password` char(64) default '',
  `ConsiderDay` int(11) default '0',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  PRIMARY KEY  (`ID`),
  KEY `HostingOrdersOrderID` (`OrderID`),
  CONSTRAINT `HostingOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingOrdersSchemeID` (`SchemeID`),
  CONSTRAINT `HostingOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `HostingSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
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
  `Discount` DOUBLE NOT NULL DEFAULT '-1',
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
  `MinOrdersPeriod` INT(6) DEFAULT '0',
  `SortID` int(11) default '10',
  `SchemeParams` VARCHAR(16384) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `HostingSchemesGroupID` (`GroupID`),
  CONSTRAINT `HostingSchemesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingSchemesUserID` (`UserID`),
  CONSTRAINT `HostingSchemesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingSchemesServersGroupID` (`ServersGroupID`),
  CONSTRAINT `HostingSchemesServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `HostingSchemesHardServerID` (`HardServerID`),
  CONSTRAINT `HostingSchemesHardServerID` FOREIGN KEY (`HardServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `DNSmanagerOrders`
--

DROP TABLE IF EXISTS `DNSmanagerOrders`;
CREATE TABLE `DNSmanagerOrders` (
	`ID` int(11) NOT NULL auto_increment,
	`OrderID` int(11) NOT NULL,
	`SchemeID` int(11) NOT NULL,
	`OldSchemeID` int(11) default NULL,
	`Domain` char(255) default '',
	`Parked` text,
	`Login` char(20) default '',
	`Password` char(64) default '',
	`ConsiderDay` int(11) default '0',
	`StatusID` char(30) default 'UnSeted',
	`StatusDate` int(11) default '0',

	PRIMARY KEY  (`ID`),
	KEY `DNSmanagerOrdersOrderID` (`OrderID`),
	CONSTRAINT `DNSmanagerOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `DNSmanagerOrdersSchemeID` (`SchemeID`),
	CONSTRAINT `DNSmanagerOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `DNSmanagerSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `DNSmanagerSchemes`
--

DROP TABLE IF EXISTS `DNSmanagerSchemes`;
CREATE TABLE `DNSmanagerSchemes` (
	`ID` int(11) NOT NULL auto_increment,
	`CreateDate` int(11) default '0',
	`GroupID` int(11) NOT NULL,
	`UserID` int(11) NOT NULL,
	`Name` char(30) default '',
	`PackageID` char(30) default '',
	`CostDay` decimal(11,2) default '0.00',
	`CostMonth` decimal(11,2) default '0.00',
	`Discount` DOUBLE NOT NULL DEFAULT '-1',
	`ServersGroupID` int(11) NOT NULL,
	`HardServerID` int(11) NULL,
	`Comment` char(255) default '',
	`IsActive` enum('no','yes') default 'yes',
	`IsProlong` enum('no','yes') default 'yes',
	`IsSchemeChangeable` enum('no','yes') default 'yes',
	`IsSchemeChange` enum('no','yes') default 'yes',
	`MinDaysPay` int(6) default '0',			/* минимальное число дней первой оплаты */
	`MinDaysProlong` INT(6) default '0',			/* минимальное число дней продления, для ранее оплаченных заказов */
	`MaxDaysPay` int(6) default '0',			/* максимальное число дней оплаты заказа */
	`MaxOrders` int(6) DEFAULT '0',				/* максимальное число заказов по этому тарифу, на одного пользователя */
	`MinOrdersPeriod` INT(6) DEFAULT '0',
	`SortID` int(11) default '10',
	`SchemeParams` VARCHAR(16384) NOT NULL,
	`IsReselling` enum('no','yes') default 'no',		/* тариф предполагает создание реселлера */
	`Reseller` char(255) default '',			/* реселлер от которого будут создаваться юзеры */
	PRIMARY KEY  (`ID`),
	KEY `DNSmanagerSchemesGroupID` (`GroupID`),
	CONSTRAINT `DNSmanagerSchemesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `DNSmanagerSchemesUserID` (`UserID`),
	CONSTRAINT `DNSmanagerSchemesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `DNSmanagerSchemesServersGroupID` (`ServersGroupID`),
	CONSTRAINT `DNSmanagerSchemesServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `DNSmanagerSchemesHardServerID` (`HardServerID`),
	CONSTRAINT `DNSmanagerSchemesHardServerID` FOREIGN KEY (`HardServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;





/* moved into Servers table*/
DROP TABLE IF EXISTS `Registrators`;
/* join to one Bonuses table */
DROP TABLE IF EXISTS `HostingBonuses`;
/* moved to OrdersConsider */
DROP TABLE IF EXISTS `HostingConsider`;
/* join to one Politics table */
DROP TABLE IF EXISTS `HostingPolitics`;
/* join to one Bonuses table */
DROP TABLE IF EXISTS `DomainsBonuses`;
/* join to one table */
DROP TABLE IF EXISTS `DomainsSchemesGroups`;
/* join to one table */
DROP TABLE IF EXISTS `DomainsSchemesGroupsItems`;
/* join to one Politics table */
DROP TABLE IF EXISTS `HostingDomainsPolitics`;
--
-- Table structure for table `DomainsConsider`
--

DROP TABLE IF EXISTS `DomainConsider`;
CREATE TABLE `DomainConsider` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) default '0',
  `DomainOrderID` int(11) NOT NULL,
  `YearsReserved` int(11) default '0',
  `YearsRemainded` int(11) default '0',
  `Cost` decimal(11,2) default '0.00',
  `Discont` decimal(11,2) default '0.00',
  PRIMARY KEY(`ID`),
  KEY `DomainConsiderDomainOrderID` (`DomainOrderID`),
  CONSTRAINT `DomainConsiderDomainOrderID` FOREIGN KEY (`DomainOrderID`) REFERENCES `DomainOrders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
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
	`Discount` DOUBLE NOT NULL DEFAULT '-1',
	`ServersGroupID` int(11) NOT NULL,
	`Node` char(255) default '',				-- нода кластера
	`Comment` char(255) NOT NULL,
	`IsActive` enum('no','yes') DEFAULT 'yes',
	`IsProlong` enum('no','yes') DEFAULT 'yes',
	`IsSchemeChangeable` enum('no','yes') DEFAULT 'yes',
	`IsSchemeChange` enum('no','yes') DEFAULT 'yes',
	`MinDaysPay` int(6) DEFAULT '0',
	`MinDaysProlong` int(6) DEFAULT '0',
	`MaxDaysPay` int(6) DEFAULT '0',
	`MaxOrders` int(6) DEFAULT '0',
	`MinOrdersPeriod` INT(6) DEFAULT '0',
	`SortID` int(11) DEFAULT '10',
	`SchemeParams` VARCHAR(16384) NOT NULL,
	PRIMARY KEY (`ID`),
	KEY `VPSSchemesGroupID` (`GroupID`),
	KEY `VPSSchemesUserID` (`UserID`),
	KEY `VPSSchemesServersGroupID` (`ServersGroupID`),
	CONSTRAINT `VPSSchemesServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- SEPARATOR

CREATE TABLE IF NOT EXISTS `VPSOrders` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`OrderID` int(11) NOT NULL,
	`SchemeID` int(11) NOT NULL,
	`OldSchemeID` int(11) DEFAULT NULL,
	`Domain` char(255) DEFAULT '',
	`Parked` text,
	`Login` char(20) DEFAULT '',
	`IP` char(60) default '',
	`Password` char(64) DEFAULT '',
	`ConsiderDay` int(11) DEFAULT '0',
	`StatusID` char(30) DEFAULT 'UnSeted',
	`StatusDate` int(11) DEFAULT '0',
	PRIMARY KEY (`ID`),
	KEY `VPSOrdersOrderID` (`OrderID`),
	CONSTRAINT `VPSOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `VPSOrdersSchemeID` (`SchemeID`),
	CONSTRAINT `VPSOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `VPSSchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- SEPARATOR
/* join to one Politics table */
DROP TABLE IF EXISTS `VPSPolitics`;

-- SEPARATOR

/* join to one Bonuses Table */
DROP TABLE IF EXISTS `VPSBonuses`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TABLE IF EXISTS `VPSConsider`;

-- SEPARATOR
/* DS values added by lissyara 2011-06-29 in 20:31 MSK */

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
  `Discount` DOUBLE NOT NULL DEFAULT '-1',
  `ServerID` int(11) NULL,
  `IsActive` enum('no','yes') DEFAULT 'yes',
  `IsBroken` enum('no','yes') DEFAULT 'no',
  `IsProlong` enum('no','yes') NOT NULL,
  `MinDaysPay` int(6) DEFAULT '0',
  `MinDaysProlong` int(6) DEFAULT '0',
  `MaxDaysPay` int(6) DEFAULT '0',
  `MaxOrders` int(6) DEFAULT '0',
  `MinOrdersPeriod` INT(6) DEFAULT '0',
  `SortID` int(11) DEFAULT '10',
  `CPU` VARCHAR(128) NOT NULL,
  `ram` int(6) NOT NULL,
  `raid` char(128) NOT NULL,
  `disks` VARCHAR(128) NOT NULL,
  `chrate` int(5) DEFAULT '0',
  `trafflimit` int(11) DEFAULT '0',
  `traffcorrelation` char(128) NOT NULL,
  `OS` char(128) NOT NULL,
  `Switch` char(255),
  `IPaddr` varchar(32) NOT NULL DEFAULT '',
  `DSuser` varchar(32) NOT NULL DEFAULT '',
  `DSpass` char(64) NOT NULL DEFAULT '',
  `ILOaddr` varchar(128) NOT NULL DEFAULT '',
  `ILOuser` varchar(32) NOT NULL DEFAULT '',
  `ILOpass` char(64) NOT NULL DEFAULT '',
  `UserNotice` VARCHAR(1024) NOT NULL,
  `AdminNotice` VARCHAR(1024) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `DSSchemesGroupID` (`GroupID`),
  KEY `DSSchemesUserID` (`UserID`),
  KEY `DSSchemesServerID` (`ServerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
-- SEPARATOR
ALTER TABLE `DSSchemes` ADD CONSTRAINT `DSSchemesServerID` FOREIGN KEY (`ServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
/* join to one Politics table */
DROP TABLE IF EXISTS `DSDomainsPolitics`;
/* join to one Bonuses table */
DROP TABLE IF EXISTS `DSBonuses`;
/* moved to OrdersConsider */
DROP TABLE IF EXISTS `DSConsider`;
/* join to one Bonuses table */
DROP TABLE IF EXISTS `ExtraIPBonuses`;
/* moved to OrdersConsider */
DROP TABLE IF EXISTS `ExtraIPConsider`;
/* join to one Politics table */
DROP TABLE IF EXISTS `ExtraIPDomainsPolitics`;

--
-- Структура таблицы `ExtraIPOrders`
--

CREATE TABLE IF NOT EXISTS `ExtraIPOrders` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` int(11) NOT NULL,
  `SchemeID` int(11) NOT NULL,
  `OldSchemeID` int(11) DEFAULT NULL,
  `DependOrderID` int(11) NOT NULL,
  `Domain` char(255) DEFAULT '',
  `Parked` text,
  `Login` char(32) DEFAULT 'noassign',
  `Password` char(64) DEFAULT '',
  `ConsiderDay` int(11) DEFAULT '0',
  `StatusID` char(30) DEFAULT 'UnSeted',
  `StatusDate` int(11) DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `ExtraIPOrdersOrderID` (`OrderID`),
  KEY `ExtraIPOrdersSchemeID` (`SchemeID`)
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
	`Discount` DOUBLE NOT NULL DEFAULT '-1',
	`AddressType` CHAR( 8 ) NOT NULL DEFAULT 'IPv4',
	`Comment` char(255) NOT NULL,
	`IsActive` enum('no','yes') DEFAULT 'yes',
	`IsProlong` enum('no','yes') DEFAULT 'yes',
	`MinDaysPay` int(6) DEFAULT '0',
	`MinDaysProlong` int(6) DEFAULT '0',
	`MaxDaysPay` int(6) DEFAULT '0',
	`MaxOrders` int(6) DEFAULT '0',
	`MinOrdersPeriod` INT(6) DEFAULT '0',
	`Params` varchar(1024),					-- набор переменных необходимых для работы
	`SortID` int(11) DEFAULT '10',
	PRIMARY KEY (`ID`),
	KEY `ExtraIPSchemesGroupID` (`GroupID`),
	KEY `ExtraIPSchemesUserID` (`UserID`)
) ENGINE InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


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
	`pricelist_id` int(12) NOT NULL,
	`period` VARCHAR(32) NOT NULL,
	`addon` INT(12) NOT NULL DEFAULT '1',
	`IP` char(64) NOT NULL,					-- IP-адрес
	`remoteip` char(64) NOT NULL,				-- IP-адрес сервера
	`elid` int(12) NOT NULL,
	`LicKey` VARCHAR(128) NOT NULL,
	`IsInternal` enum('yes','no') NOT NULL DEFAULT 'yes',
	`IsUsed` enum('yes','no') NOT NULL DEFAULT 'yes',
	`ISPname` text,
	`StatusID` char(30) DEFAULT 'UnSeted',
	`CreateDate` int(11) NOT NULL,
	`ip_change_date` int(11) NOT NULL,
	`lickey_change_date` int(11) NOT NULL,
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
	`Discount` DOUBLE NOT NULL DEFAULT '-1',
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
	`MinOrdersPeriod` INT(6) DEFAULT '0',
	`ConsiderTypeID` char(30) default 'Daily',
	`SortID` int(11) DEFAULT '10',
	`pricelist_id` int(12) NOT NULL,
	`period` VARCHAR(32) NOT NULL,
	`addon` INT(12) NOT NULL DEFAULT '1',
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
DROP TABLE IF EXISTS `ISPswDomainsPolitics`;

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

-- SEPARATOR

/* прокси */
DROP TABLE IF EXISTS `ProxySchemes`;
-- SEPARATOR
CREATE TABLE `ProxySchemes` (
	`ID` int(11) NOT NULL auto_increment,
	`CreateDate` int(11) default '0',
	`GroupID` int(11) NOT NULL,
	`UserID` int(11) NOT NULL,
	`Name` char(30) default '',
	`PackageID` char(30) default '',
	`CostDay` decimal(11,2) default '0.00',
	`CostMonth` decimal(11,2) default '0.00',
	`Discount` DOUBLE NOT NULL DEFAULT '-1',
	`ServersGroupID` int(11) NOT NULL,
	`HardServerID` int(11) NULL,
	`Comment` char(255) default '',
	`IsActive` enum('no','yes') default 'yes',
	`IsProlong` enum('no','yes') default 'yes',
	`IsSchemeChangeable` enum('no','yes') default 'yes',
	`IsSchemeChange` enum('no','yes') default 'yes',
	`MinDaysPay` int(6) default '0',			/* минимальное число дней первой оплаты */
	`MinDaysProlong` INT(6) default '0',			/* минимальное число дней продления, для ранее оплаченных заказов */
	`MaxDaysPay` int(6) default '0',			/* максимальное число дней оплаты заказа */
	`MaxOrders` int(6) DEFAULT '0',				/* максимальное число заказов по этому тарифу, на одного пользователя */
	`MinOrdersPeriod` int(6) DEFAULT '0',			/* минимальный период между закзаами */
	--
	-- Common
	--
	`IPtype` char(12) default 'IPv4',			/* тип заказываемого прокси сервера IPv6,IPv4,IPv4shared */
	`Country` char(3) default 'ru',				-- страна
	`SortID` int(11) default '10',

	PRIMARY KEY  (`ID`),
	KEY `ProxySchemesGroupID` (`GroupID`),
	CONSTRAINT `ProxySchemesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `ProxySchemesUserID` (`UserID`),
	CONSTRAINT `ProxySchemesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `ProxySchemesServersGroupID` (`ServersGroupID`),
	CONSTRAINT `ProxySchemesServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `ProxySchemesHardServerID` (`HardServerID`),
	CONSTRAINT `ProxySchemesHardServerID` FOREIGN KEY (`HardServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- SEPARATOR
--
-- Table structure for table `ProxyOrders`
--

DROP TABLE IF EXISTS `ProxyOrders`;
-- SEPARATOR
CREATE TABLE `ProxyOrders` (
	`ID` int(11) NOT NULL auto_increment,
	`OrderID` int(11) NOT NULL,
	`SchemeID` int(11) NOT NULL,
	`OldSchemeID` int(11) default NULL,
	`Login` char(20) default '',		-- логин
	`Password` char(64) default '',		-- пароль
	`IP` char(64) default '0.0.0.0',	-- IP адрес прокси (с которого выходит в инет)
	`Host` char(64) default '0.0.0.0',	-- IP адрес с которому надлежит коннектится клиенту
	`Port` int(5) default '0',		-- порт прокси
	`ProtocolType` char(9) default 'https',	-- тип протокола прокси: SOCK5, HTTPS
	`ConsiderDay` int(11) default '0',
	`StatusID` char(30) default 'UnSeted',
	`StatusDate` int(11) default '0',
	PRIMARY KEY  (`ID`),
	KEY `ProxyOrdersOrderID` (`OrderID`),
	CONSTRAINT `ProxyOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `ProxyOrdersSchemeID` (`SchemeID`),
	CONSTRAINT `ProxyOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ProxySchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR

SET FOREIGN_KEY_CHECKS=1;

