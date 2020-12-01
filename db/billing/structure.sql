SET FOREIGN_KEY_CHECKS=0;

--
-- Структура таблицы `ClausesGroups`
--
DROP TABLE IF EXISTS `ClausesGroups`;
CREATE TABLE IF NOT EXISTS `ClausesGroups` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`PublicDate` int(11) NOT NULL,
	`ChangedDate` int(11) NOT NULL,
	`AuthorID` int(11) NOT NULL,
	`EditorID` int(11) NOT NULL,
	`Name` varchar(1023),
	`Notice` text,
	`IsProtected` enum('no','yes') DEFAULT 'no',
	`IsPublish` enum('no','yes') DEFAULT 'no',
	PRIMARY KEY (`ID`),
	KEY `ClausesGroupsPublicDate` (`PublicDate`),
	KEY `ClausesGroupsAuthorID` (`AuthorID`),
	KEY `ClausesGroupsEditorID` (`EditorID`),
	CONSTRAINT `ClausesGroupsAuthorID` FOREIGN KEY (`AuthorID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT `ClausesGroupsEditorID` FOREIGN KEY (`EditorID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `ClausesRating`
--

DROP TABLE IF EXISTS `ClausesRating`;
CREATE TABLE `ClausesRating` (
  `ID` int(11) NOT NULL auto_increment,
  `ClauseID` int(11) NOT NULL,
  `IP` char(15) default '127.0.0.1',
  `Rating` int(1) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `ClausesRatingClauseID` (`ClauseID`),
  CONSTRAINT `ClausesRatingClauseID` FOREIGN KEY (`ClauseID`) REFERENCES `Clauses` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `ClausesFiles`
--

DROP TABLE IF EXISTS `ClausesFiles`;
CREATE TABLE `ClausesFiles` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `ClauseID` int(11) NOT NULL,
  `FileName` char(255) default '',
  `FileData` mediumblob,
  `Comment` char(255) default '',
  PRIMARY KEY  (`ID`),
  KEY `ClausesFilesClauseID` (`ClauseID`),
  KEY `ClausesFilesFileName` (`FileName`),
  CONSTRAINT `ClausesFilesClauseID` FOREIGN KEY (`ClauseID`) REFERENCES `Clauses` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Clauses`
--

DROP TABLE IF EXISTS `Clauses`;
CREATE TABLE `Clauses` (
  `ID` int(11) NOT NULL auto_increment,
  `GroupID` INT(11) DEFAULT '1',
  `PublicDate` int(11) default '0',
  `ChangedDate` int(11) default '0',
  `AuthorID` int(11) default '1',
  `EditorID` int(11) default '1',
  `Partition` char(255) default '',
  `Title` char(255) default '',
  `IsProtected` enum('no','yes') default 'no',
  `IsXML` enum('no','yes') default 'no',
  `IsDOM` enum('no','yes') default 'no',
  `IsPublish` enum('no','yes') default 'yes',
  `Text` longtext,
  PRIMARY KEY  (`ID`),
  KEY `ClausesGroupID` (`GroupID`),
  KEY `ClausesPublicDate` (`PublicDate`),
  KEY `ClausesAuthorID` (`AuthorID`),
  KEY `ClausesEditorID` (`EditorID`),
  KEY `ClausesPartition` (`Partition`),
  CONSTRAINT `ClausesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `ClausesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ClausesAuthorID` FOREIGN KEY (`AuthorID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ClausesEditorID` FOREIGN KEY (`EditorID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `Invoices`
--

DROP TABLE IF EXISTS `Invoices`;
CREATE TABLE `Invoices` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `ContractID` int(11) NOT NULL,
  `PaymentSystemID` char(50) default '',
  `Summ` decimal(11,2) default '0.00',
  `IsPosted` enum('no','yes') default 'no',
  `IsCheckSent` enum('no','yes') default 'yes',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  PRIMARY KEY  (`ID`),
  KEY `InvoicesContractID` (`ContractID`),
  CONSTRAINT `InvoicesContractID` FOREIGN KEY (`ContractID`) REFERENCES `Contracts` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `InvoicesStatusID` (`StatusID`),
  KEY `InvoicesStatusDate` (`StatusDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Edesks`
--

DROP TABLE IF EXISTS `Edesks`;
CREATE TABLE `Edesks` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `UserID` int(11) NOT NULL,
  `TargetGroupID` int(11) NOT NULL,
  `TargetUserID` int(11) NOT NULL,
  `PriorityID` char(30) default 'Low',
  `Theme` text CHARACTER SET utf8mb4,
  `UpdateDate` int(11) default '0',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  `SeenByPersonal` int(11) DEFAULT '0',
  `LastSeenBy` int(11) DEFAULT '0',
  `SeenByUser` int(11) DEFAULT '0',
  `Flags` char(32) DEFAULT 'No',
  `NotifyEmail` char(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `EdesksUserID` (`UserID`),
  CONSTRAINT `EdesksUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `EdesksTargetGroupID` (`TargetGroupID`),
  CONSTRAINT `EdesksTargetGroupID` FOREIGN KEY (`TargetGroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `EdesksTargetUserID` (`TargetUserID`),
  CONSTRAINT `EdesksTargetUserID` FOREIGN KEY (`TargetUserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `EdesksUpdateDate` (`UpdateDate`),
  KEY `EdesksStatusID` (`StatusID`),
  KEY `Flags` (`Flags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `EdesksMessages`
--

DROP TABLE IF EXISTS `EdesksMessages`;
CREATE TABLE `EdesksMessages` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `UserID` int(11) NOT NULL,
  `EdeskID` int(11) NOT NULL,
  `Content` text CHARACTER SET utf8mb4,
  `FileName` char(255) default '0',
  `IsNotify` ENUM('no','yes') NOT NULL DEFAULT 'no',
  `IsVisible` ENUM('yes','no') NOT NULL DEFAULT 'yes', 
  `VoteBall` INT(2) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`ID`),
  KEY `EdesksMessagesCreateDate` (`CreateDate`),
  KEY `IsNotify` (`IsNotify`),
  KEY `EdesksMessagesUserID` (`UserID`),
  CONSTRAINT `EdesksMessagesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `EdesksMessagesEdeskID` (`EdeskID`),
  CONSTRAINT `EdesksMessagesEdeskID` FOREIGN KEY (`EdeskID`) REFERENCES `Edesks` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Contracts`
--

DROP TABLE IF EXISTS `Contracts`;
CREATE TABLE `Contracts` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `UserID` int(11) NOT NULL,
  `TypeID` char(50) NOT NULL,
  `ProfileID` int(11) default '0',
  `Customer` char(255) default '',
  `IsUponConsider` enum('no','yes') default 'no',
  `Balance` decimal(11,2) default '0.00',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  PRIMARY KEY  (`ID`),
  KEY `ContractsUserID` (`UserID`),
  KEY `ContractsTypeID` (`TypeID`),
  CONSTRAINT `ContractsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `ContractsEnclosures`
--

DROP TABLE IF EXISTS `ContractsEnclosures`;
CREATE TABLE `ContractsEnclosures` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `ContractID` int(11) NOT NULL,
  `Number` int(11) default '1',
  `TypeID` char(30) default '',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  PRIMARY KEY  (`ID`),
  KEY `ContractsEnclosureContractID` (`ContractID`),
  CONSTRAINT `ContractsEnclosuresContractID` FOREIGN KEY (`ContractID`) REFERENCES `Contracts` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `MotionDocuments`
--

DROP TABLE IF EXISTS `MotionDocuments`;
CREATE TABLE `MotionDocuments` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `TypeID` char(30) default '',
  `ContractID` int(11),
  `AjaxCall` char(255) default '',
  `UniqID` char(255) default '',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  PRIMARY KEY  (`ID`),
  KEY `MotionDocumentsTypeID` (`TypeID`),
  KEY `MotionDocumentsContractID` (`ContractID`),
  CONSTRAINT `MotionDocumentsContractID` FOREIGN KEY (`ContractID`) REFERENCES `Contracts` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `MotionDocumentsUniqID` (`UniqID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Groups`
--

DROP TABLE IF EXISTS `Groups`;
CREATE TABLE `Groups` (
  `ID` int(11) NOT NULL auto_increment,
  `ParentID` int(11) default NULL,
  `Name` char(30) default '',
  `InterfaceID` char(255) default '',
  `IsDefault` enum('no','yes') default 'no',
  `IsDepartment` enum('no','yes') default 'no',
  `Comment` char(255) default '',
  PRIMARY KEY  (`ID`),
  KEY `GroupsParentID` (`ParentID`),
  CONSTRAINT `GroupsParentID` FOREIGN KEY (`ParentID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `GroupsIsDepartment` (`IsDepartment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Permissions`
--

DROP TABLE IF EXISTS `Permissions`;
CREATE TABLE `Permissions` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` char(150) default '',
  `HostID` char(30) default '',
  `UserGroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `OwnerGroupID` int(11) NOT NULL,
  `OwnerID` int(11) NOT NULL,
  `Metric` int(11) default '1',
  `IsAccess` enum('no','yes') default 'yes',
  PRIMARY KEY  (`ID`),
  KEY `PermissionsName` (`Name`),
  KEY (`Name`),
  KEY (`Metric`),
  KEY `PermissionsUserGroupID` (`UserGroupID`),
  CONSTRAINT `PermissionsUserGroupID` FOREIGN KEY (`UserGroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `PermissionsUserID` (`UserID`),
  CONSTRAINT `PermissionsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `PermissionsOwnerGroupID` (`OwnerGroupID`),
  CONSTRAINT `PermissionsOwnerGroupID` FOREIGN KEY (`OwnerGroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `PermissionsOwnerID` (`OwnerID`),
  CONSTRAINT `PermissionsOwnerID` FOREIGN KEY (`OwnerID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `PermissionsMetric` (`Metric`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Postings`
--

DROP TABLE IF EXISTS `Postings`;
CREATE TABLE `Postings` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `ContractID` int(11) NOT NULL,
  `ServiceID` int(11) NOT NULL,
  `Comment` char(255) default '',
  `Before` decimal(11,2) default '0.00',
  `After` decimal(11,2) default '0.00',
  PRIMARY KEY  (`ID`),
  KEY `PostingsContractID` (`ContractID`),
  CONSTRAINT `PostingsContractID` FOREIGN KEY (`ContractID`) REFERENCES `Contracts` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `PostingsServiceID` (`ServiceID`),
  CONSTRAINT `PostingsServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Profiles`
--

DROP TABLE IF EXISTS `Profiles`;
CREATE TABLE `Profiles` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `UserID` int(11) NOT NULL,
  `Name` char(255) default '',
  `TemplateID` char(30) default '',
  `IsDefault` enum('no','yes') default 'no',
  `Attribs` text,
  `Format` char(10) default 'jpg',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  PRIMARY KEY  (`ID`),
  KEY `ProfilesUserID` (`UserID`),
  CONSTRAINT `ProfilesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `ProfilesTemplateID` (`TemplateID`),
  KEY `StatusID` (`StatusID`),
  KEY `StatusDate` (`StatusDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Tasks`
--

DROP TABLE IF EXISTS `Tasks`;
CREATE TABLE `Tasks` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `UserID` int(11) NOT NULL,
  `TypeID` char(30) default '',
  `ExecuteDate` int(11) default '0',
  `Params` LONGTEXT,
  `Errors` int(11) default '0',
  `Result` text CHARACTER SET utf8mb4,
  `IsExecuted` enum('no','yes') default 'no',
  `IsActive` enum('no','yes') default 'yes',
  PRIMARY KEY  (`ID`),
  KEY `TasksCreateDate` (`CreateDate`),
  KEY `TasksUserID` (`UserID`),
  CONSTRAINT `TasksUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `TasksExecuteDate` (`ExecuteDate`),
  KEY `TasksErrors` (`Errors`),
  KEY `TasksIsExecuted` (`IsExecuted`),
  KEY `TasksIsActive` (`IsActive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=100;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
  `ID` int(11) NOT NULL auto_increment,
  `GroupID` int(11) NOT NULL,
  `RegisterDate` int(11) default '0',
  `OwnerID` int(11) NULL,
  `IsManaged` enum('no','yes') default 'no',
  `IsInheritGroup` enum('no','yes') default 'no',
  `Name` char(100) default '',
  `Watchword` char(40) default '',
  `UniqID` char(32) default 'no',
  `Email` char(255) default '',
  `EmailConfirmed` INT(12) default '0',
  `Rating` decimal(7,2) default '0.00',
  `Sign` char(255) default '',
  `EnterIP` char(20) default '-',
  `EnterDate` int(11) default '0',
  `LayPayMaxDays` int(11) default '0',
  `LayPayMaxSumm` decimal(11,2) default '0.00',
  `LayPayThreshold` decimal(11,2) default '0.00',
  `IsActive` enum('no','yes') default 'yes',
  `LockReason` char(255),
  `IsNotifies` enum('no','yes') default 'yes',
  `IsHidden` enum('no','yes') default 'no',
  `IsProtected` enum('no','yes') default 'no',
  `Params` LONGTEXT,
  `AdminNotice` text,
  `IsConfirmed` enum('no','yes') DEFAULT 'yes',
  PRIMARY KEY  (`ID`),
  KEY `UsersGroupID` (`GroupID`),
  CONSTRAINT `UsersGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `UsersOwnerID` (`OwnerID`),
  CONSTRAINT `UsersOwnerID` FOREIGN KEY (`OwnerID`) REFERENCES `Users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  KEY `UsersName` (`Name`),
  KEY `UsersEmail` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `WorksComplite`
--

DROP TABLE IF EXISTS `WorksComplite`;
CREATE TABLE `WorksComplite` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `ContractID` int(11) NOT NULL,
  `Month` int(11) default '1',
  `ServiceID` int(11) NOT NULL,
  `Comment` char(255) default '',
  `Amount` int(11) default '1',
  `Cost` decimal(11,2) default '0.00',
  `Discont` decimal(11,2) default '0.00',
  `UniqID` char(255) default '',
  PRIMARY KEY  (`ID`),
  KEY `WorksCompliteContractID` (`ContractID`),
  CONSTRAINT `WorksCompliteContractID` FOREIGN KEY (`ContractID`) REFERENCES `Contracts` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `WorksCompliteServiceID` (`ServiceID`),
  CONSTRAINT `WorksCompliteServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Orders`
--

DROP TABLE IF EXISTS `Orders`;
CREATE TABLE `Orders` (
  `ID` int(11) NOT NULL auto_increment,
  `OrderDate` int(11) default '0',
  `ContractID` int(11) NOT NULL,
  `ServiceID` int(11) NOT NULL,
  `ServerID` int(11) NULL,
  `IsAutoProlong` ENUM('no','yes') default 'yes', 
  `ExpirationDate` int(11) default '0',
  `Keys` char(255) default '',
  `IsPayed` enum('no','yes') default 'no',
  `DaysRemainded` INT(11) default '0',
  `StatusID` char(30) default 'UnSeted',
  `StatusDate` int(11) default '0',
  `Params` LONGTEXT,
  `UserNotice` TEXT,
  `AdminNotice` TEXT,
  PRIMARY KEY  (`ID`),
  KEY `OrdersContractID` (`ContractID`),
  CONSTRAINT `OrdersContractID` FOREIGN KEY (`ContractID`) REFERENCES `Contracts` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `OrdersServiceID` (`ServiceID`),
  CONSTRAINT `OrdersServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `OrdersServerID` (`ServerID`),
  CONSTRAINT `OrdersServerID` FOREIGN KEY (`ServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `OrdersTransfer`
--

DROP TABLE IF EXISTS `OrdersTransfer`;
CREATE TABLE `OrdersTransfer` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) DEFAULT '0',
  `UserID` int(11) NOT NULL,
  `ServiceID` int(11) NOT NULL,
  `ServiceOrderID` int(11) NOT NULL,
  `ToUserID` int(11) NOT NULL,
  `IsExecuted` enum('yes','no') DEFAULT 'no',
  PRIMARY KEY (`ID`),
  KEY `OrdersTransferCreateDate` (`CreateDate`),
  KEY `OrdersTransferUserID` (`UserID`),
  KEY `OrdersTransferServiceID` (`ServiceID`),
  KEY `OrdersTransferToUserID` (`ToUserID`),
  KEY `OrdersTransferIsExecuted` (`IsExecuted`),
  CONSTRAINT `OrdersTransferToUserID` FOREIGN KEY (`ToUserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `OrdersTransferUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `OrdersTransferServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Basket`
--

DROP TABLE IF EXISTS `Basket`;
CREATE TABLE `Basket` (
  `ID` int(11) NOT NULL auto_increment,
  `OrderID` int(11) NOT NULL,
  `Comment` char(255) default '',
  `Amount` int(11) default '1',
  `Summ` decimal(11,2) default '0.00',
  PRIMARY KEY  (`ID`),
  KEY `BasketOrderID` (`OrderID`),
  CONSTRAINT `BasketOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `InvoicesItems`
--

DROP TABLE IF EXISTS `InvoicesItems`;
CREATE TABLE `InvoicesItems` (
  `ID` int(11) NOT NULL auto_increment,
  `InvoiceID` int(11) NOT NULL,
  `ServiceID` int(11) NOT NULL,
  `Comment` char(255) default '',
  `OrderID` int(11) default NULL,
  `Amount` int(11) default '1',
  `Summ` decimal(11,2) default '0.00',
  PRIMARY KEY  (`ID`),
  KEY `InvoicesItemsInvoiceID` (`InvoiceID`),
  CONSTRAINT `InvoicesItemsInvoiceID` FOREIGN KEY (`InvoiceID`) REFERENCES `Invoices` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `InvoicesItemsServiceID` (`ServiceID`),
  CONSTRAINT `InvoicesItemsServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Services`
--

DROP TABLE IF EXISTS `Services`;
CREATE TABLE `Services` (
  `ID` int(11) NOT NULL auto_increment,
  `GroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ServicesGroupID` int(11) NOT NULL,
  `Name` char(255) default '',
  `NameShort` CHAR(32) default '', 
  `Code` char(255) default 'Default',
  `OperationSign` char(1) NOT NULL DEFAULT '-',
  `PartnersRewardPercent` DOUBLE NOT NULL DEFAULT '-1',
  `Item` char(255) default '',
  `Measure` char(30) default '',
  `ConsiderTypeID` char(30) default 'Upon',
  `CostOn` decimal(11,2) default '0.00',
  `Cost` decimal(11,2) default '0.00',
  `IsHidden` enum('no','yes') default 'no',
  `IsProtected` enum('no','yes') default 'no',
  `IsActive` enum('no','yes') default 'yes',
  `IsProlong` enum('no','yes') default 'yes',
  `IsConditionally` enum('no','yes') default 'no',
  `IsAutoInvoicing` enum('no','yes') default 'yes',
  `Params` LONGTEXT,
  `Priority` INT(3) DEFAULT '100',
  `SortID` int(11) default '10',
  PRIMARY KEY  (`ID`),
  KEY `ServicesGroupID` (`GroupID`),
  CONSTRAINT `ServicesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `ServicesUserID` (`UserID`),
  CONSTRAINT `ServicesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `ServicesServicesGroupID` (`ServicesGroupID`),
  CONSTRAINT `ServicesServicesGroupID` FOREIGN KEY (`ServicesGroupID`) REFERENCES `ServicesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `Notifies`
--

DROP TABLE IF EXISTS `Notifies`;
CREATE TABLE `Notifies` (
  `ID` int(11) NOT NULL auto_increment,
  `ContactID` INT(11) NOT NULL,
  `TypeID` char(255) default '',
  PRIMARY KEY  (`ID`),
  KEY `NotifiesContactID` (`ContactID`),
  CONSTRAINT `NotifiesContactID` FOREIGN KEY (`ContactID`) REFERENCES `Contacts` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `Events`
--

DROP TABLE IF EXISTS `Events`;
CREATE TABLE `Events` (
  `ID` int(11) NOT NULL auto_increment,
  `CreateDate` int(11) default '0',
  `UserID` int(11) NOT NULL,
  `Text` text,
  `PriorityID` char(30) default 'System',
  `IsReaded` enum('no','yes') default 'yes',
  PRIMARY KEY (`ID`),
  KEY (`IsReaded`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* added by serge 2011-09-13 in 14:35 MSK */
ALTER TABLE `Events` ADD INDEX ( `CreateDate` );

--
-- Table structure for table `ServicesGroups`
--

DROP TABLE IF EXISTS `ServicesGroups`;
CREATE TABLE `ServicesGroups` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) default '',
  `IsProtected` enum('no','yes') default 'no',
  `IsActive` enum('no','yes') default 'yes',
  `SortID` int(11) default '1',
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2000;

--
-- Table structure for table `ServicesFields`
--

DROP TABLE IF EXISTS `ServicesFields`;
CREATE TABLE `ServicesFields` (
  `ID` int(11) NOT NULL auto_increment,
  `ServiceID` int(11) NOT NULL,
  `Name` varchar(255) default '',
  `Prompt` TEXT,
  `TypeID` varchar(255) default '',
  `Options` varchar(255) default '',
  `Default` varchar(255) default '',
  `IsDuty` enum('no','yes') default 'no',
  `IsKey` enum('no','yes') default 'no',
  `ValidatorID` varchar(255) default '',
  `SortID` int(11) default '10',
  PRIMARY KEY  (`ID`),
  KEY `ServicesFieldsServiceID` (`ServiceID`),
  CONSTRAINT `ServicesFieldsServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `OrdersFields`
--

DROP TABLE IF EXISTS `OrdersFields`;
CREATE TABLE `OrdersFields` (
  `ID` int(11) NOT NULL auto_increment,
  `OrderID` int(11) NOT NULL,
  `ServiceFieldID` int(11) NOT NULL,
  `Value` text,
  `FileName` varchar(255) default '',
  PRIMARY KEY  (`ID`),
  KEY `OrdersFieldsOrderID` (`OrderID`),
  CONSTRAINT `OrdersFieldsOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `OrdersFieldsServiceFieldID` (`ServiceFieldID`),
  CONSTRAINT `OrdersFieldsServiceFieldID` FOREIGN KEY (`ServiceFieldID`) REFERENCES `ServicesFields` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `StatusesHistory`
--

DROP TABLE IF EXISTS `StatusesHistory`;
CREATE TABLE `StatusesHistory` (
  `ID` int(11) NOT NULL auto_increment,
  `StatusDate` int(11) default '0',
  `ModeID` varchar(255) default '',
  `RowID` int(11) default '1',
  `StatusID` varchar(255) default '',
  `Initiator` varchar(255) default '',
  `Comment` varchar(255) default '',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* added by lissyara 2011-06-22 in 15:25 MSK */
ALTER TABLE `StatusesHistory` ADD INDEX ( `RowID` ) ;

--
-- Table structure for table `Menus`
--

DROP TABLE IF EXISTS `Menus`;
CREATE TABLE `Menus` (
  `ID` int(11) NOT NULL auto_increment,
  `GroupID` int(11) NOT NULL,
  `ParentID` int(11) NULL,
  `Code` varchar(255) default '',
  `Title` varchar(255) default '',
  `Href` varchar(255) default '',
  `IsPick` enum('no','yes') default 'no',
  `IsVisible` enum('no','yes') default 'yes',
  `Comp` varchar(255) default '',
  `SortID` int(11) default '10',
  PRIMARY KEY  (`ID`),
  KEY `MenusGroupID` (`GroupID`),
  CONSTRAINT `MenusGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  KEY `MenusParentID` (`ParentID`),
  CONSTRAINT `MenusParentID` FOREIGN KEY (`ParentID`) REFERENCES `Menus` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* added by lissyara 2011-10-12 in 17:35 MSK, for JBS-173 */

CREATE TABLE IF NOT EXISTS `RequestLog` (
	`ID` int(12) NOT NULL AUTO_INCREMENT,
	`CreateDate` int(11) NOT NULL,
	`UserID` int(11) NOT NULL,
	`REMOTE_ADDR` CHAR(32) NOT NULL,
	`REQUEST_URI` varchar(1024) NOT NULL,
	`HTTP_REFERER` varchar(1024) NOT NULL,
	`HTTP_USER_AGENT` varchar(1024) NOT NULL,
	`WORK_TIME` float NOT NULL,
	`TIME_MYSQL` float NOT NULL,
	`COUNTER_MYSQL` int(4) NOT NULL,
	`COUNTER_COMPS` int(4) NOT NULL,
	PRIMARY KEY (`ID`),
	KEY (`UserID`),
	KEY (`CreateDate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- SEPARATOR
/* общая таблица для бонусов. реализация JBS-157 */
DROP TABLE IF EXISTS `Bonuses`;
CREATE TABLE `Bonuses` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) default '0',	-- дата создания бонуса
  `ExpirationDate` int(11) default '0', -- дата окончания возможности заюзать бонус
  `UserID` int(11) NOT NULL,		-- для какого юзера этот бонус
  `ServiceID` int(11) NULL,		-- на какой сервис бонус
  `SchemeID` int(11) NULL,		-- идентификатор тарифа, на который даётся бонус
  `SchemesGroupID` int(11) NULL,   -- группа тарифов на которую даётся бонус
  `DaysReserved` int(11) default '0',	-- на сколько дней дан бонус
  `DaysRemainded` int(11) default '0',	-- сколько дней осталось от бонуса
  `Discont` decimal(11,2) default '0.00',	-- размер скидки, в долях от единицы
  `Comment` char(255) default '',	-- комментарий к бонусу
  PRIMARY KEY(`ID`),
  /* просто ключ, чтоб не перебирать всю таблицу при поиске */
  KEY `BonusesSchemeID` (`SchemeID`),
  /* внешний ключ на юзера */
  KEY `BonusesUserID` (`UserID`),
  CONSTRAINT `BonusesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на сервис */
  KEY `BonusesServiceID` (`ServiceID`),
  CONSTRAINT `BonusesServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на группы тарифов */
  KEY `PoliticsSchemesGroupID` (`SchemesGroupID`),
  CONSTRAINT `PoliticsSchemesGroupID` FOREIGN KEY (`SchemesGroupID`) REFERENCES `SchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* общая таблица для политик. реализация JBS-158 */
DROP TABLE IF EXISTS `Politics`;
CREATE TABLE `Politics` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) default '0',	-- дата создания политики
  `ExpirationDate` int(11) default '0', -- дата окончания действия политики
  `UserID` int(11) NOT NULL,		-- для какого юзера политика
  `GroupID` int(11) NOT NULL,		-- для какой группы юзеров эта политика
  `FromServiceID` int(11) NULL,		-- при заказе какого сервиса работает политика
  `FromSchemeID` int(11) NULL,		-- идентификатор тарифа, по которому срабатывает политика
  `FromSchemesGroupID` int(11) NULL,	-- какую группу услуг оплачивают
  `ToServiceID` int(11) NULL,		-- на какой сервис работает эта политика
  `ToSchemeID` int(11) NULL,		-- идентификатор тарифа, на который будет даваться скидка
  `ToSchemesGroupID` int(11) NULL,	-- на какую группу услуг будет даваться бонус
  `DaysPay` int(11) default '665',	-- какой срок надо оплатить, чтобы сработала политика
  `DaysDiscont` int(11) default '665',  -- на какой срок даётся скидка
  `Discont` decimal(11,2) default '0.00',	-- размер скидки, в долях от единицы
  `AdminNotice` char(255) default '',	-- комментарий к политике
  PRIMARY KEY(`ID`),
  /* просто ключи для тарифов */
  KEY `PoliticsFromSchemeID` (`FromSchemeID`),
  KEY `ToPoliticsFromSchemeID` (`ToSchemeID`),
  /* внешний ключ на группы юзеров */
  KEY `PoliticsGroupID` (`GroupID`),
  CONSTRAINT `PoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на юзеров */
  KEY `PoliticsUserID` (`UserID`),
  CONSTRAINT `PoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на сервис, при заказе которого работает политика */
  KEY `PoliticsFromServiceID` (`FromServiceID`),
  CONSTRAINT `PoliticsFromServiceID` FOREIGN KEY (`FromServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на группы тарифов */
  KEY `PoliticsFromSchemesGroupID` (`FromSchemesGroupID`),
  CONSTRAINT `PoliticsFromSchemesGroupID` FOREIGN KEY (`FromSchemesGroupID`) REFERENCES `SchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на сервис, на который даётся бонус этой политикой */
  KEY `PoliticsToServiceID` (`ToServiceID`),
  CONSTRAINT `PoliticsToServiceID` FOREIGN KEY (`ToServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на группы тарифов */
  KEY `PoliticsToSchemesGroupID` (`ToSchemesGroupID`),
  CONSTRAINT `PoliticsToSchemesGroupID` FOREIGN KEY (`ToSchemesGroupID`) REFERENCES `SchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* общая таблица для группировки тарифов - группы. реализация JBS-158 */
DROP TABLE IF EXISTS `SchemesGroups`;
CREATE TABLE `SchemesGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(255) default '',
   PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* общая таблица для группировки тарифов - элементы групп. реализация JBS-158 */
DROP TABLE IF EXISTS `SchemesGroupsItems`;
CREATE TABLE `SchemesGroupsItems` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SchemesGroupID` int(11) NOT NULL,
  `ServiceID` int(11) NULL,
  `SchemeID` int(11) NULL,
  PRIMARY KEY(`ID`),
  KEY `SchemesGroupsItemsSchemeID` (`SchemeID`),
  /* внешний ключ на группы тарифов */
  KEY `SchemesGroupsItemsSchemesGroupID` (`SchemesGroupID`),
  CONSTRAINT `SchemesGroupsItemsSchemesGroupID` FOREIGN KEY (`SchemesGroupID`) REFERENCES `SchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на сервис */
  KEY `SchemesGroupsItemsServiceID` (`ServiceID`),
  CONSTRAINT `SchemesGroupsItemsServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* ПромоКоды, JBS-15 */
DROP TABLE IF EXISTS `PromoCodes`;
CREATE TABLE `PromoCodes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Code` char(32),				-- промокод
  `CreateDate` int(11) default '0',     	-- дата создания промокода
  `ExpirationDate` int(11) default '0', 	-- дата окончания действия промокода
  `ServiceID` int(11) NULL,			-- на какой сервис
  `SchemeID` int(11) NULL,			-- идентификатор тарифа
  `SchemesGroupID` int(11) NULL,		-- группа тарифов
  `DaysDiscont` int(11) default '665',		-- на какой срок создаётся бонус
  `Discont` decimal(11,2) default '0.00',		-- размер скидки, в долях от единицы
  `MaxAmount` int(11) default '0',		-- сколько раз можно ввести промокод
  `CurrentAmount` int(11) default '0',		-- сколько раз его уже вводили
  `OwnerID` int(11) NULL,			-- сделать того кто введёт партнёром этого юзера
  `ForceOwner` enum('no','yes') default 'no',	-- делать партнёром принудительно (если уже чей-то партнёр)
  `Comment` char(255) default '',		-- комментарий к промокоду
  PRIMARY KEY (`ID`),
  UNIQUE KEY (`Code`),
  /* внешний ключ на сервис */
  KEY `PromoCodesServiceID` (`ServiceID`),
  CONSTRAINT `PromoCodesServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на группы тарифов */
  KEY `PromoCodesSchemesGroupID` (`SchemesGroupID`),
  CONSTRAINT `PromoCodesSchemesGroupID` FOREIGN KEY (`SchemesGroupID`) REFERENCES `SchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на таблицу юзеров */
  KEY `PromoCodesOwnerID` (`OwnerID`),
  CONSTRAINT `PromoCodesOwnerID` FOREIGN KEY (`OwnerID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* активированные ПромоКоды */
DROP TABLE IF EXISTS `PromoCodesExtinguished`;
CREATE TABLE `PromoCodesExtinguished` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PromoCodeID` int(11) NOT NULL,		-- идентификатор погашенного промокода
  `UserID` int(11) NOT NULL,			-- какой юзер погасил промокод
  `CreateDate` int(11) default '0',		-- когда промокод был погашен
  /* уникальный ключ */
  PRIMARY KEY (`ID`),
  /* внешний ключ на юзеров */
  KEY `PromoCodesExtinguishedUserID` (`UserID`),
  CONSTRAINT `PromoCodesExtinguishedUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на ПромоКоды */
  KEY `PromoCodesPromoCodeID` (`PromoCodeID`),
  CONSTRAINT `PromoCodesPromoCodeID` FOREIGN KEY (`PromoCodeID`) REFERENCES `PromoCodes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* история заказов */
DROP TABLE IF EXISTS `OrdersHistory`;
CREATE TABLE IF NOT EXISTS `OrdersHistory` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`UserID` int(11) NOT NULL,		-- идентификатор юзера... пока не уверен, но пусть будет
	`Email` char(255) NOT NULL,		-- мыло юзера, т.к. юзер может быть удалён
	`ServiceID` int(11) NOT NULL,
	`ServiceName` char(255) NOT NULL,	-- имя сервиса
	`SchemeID` int(11) NOT NULL,
	`SchemeName` char(255) NOT NULL,	-- имя тарифа
	`OrderID` int(11) NOT NULL,
	`Parked` text,				-- прицепленные домены, IP адреса и прочие данные помогающие найти заказ
	`CreateDate` int(11) NOT NULL,		-- дата создания
	`StatusDate` int(11) NOT NULL,		-- дата установки последнего статуса
	PRIMARY KEY (`ID`),
	KEY `OrdersHistoryUserID` (`UserID`),
	KEY `OrdersHistoryEmail` (`Email`),
	KEY `OrdersHistoryServiceID` (`ServiceID`),
	KEY `OrdersHistorySchemeID` (`SchemeID`),
	UNIQUE KEY `OrdersHistoryOrderID` (`OrderID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


/* сопоставления платёжных систем и способов оплаты */
-- SEPARATOR
DROP TABLE IF EXISTS `PaymentSystemsCollation`;
-- SEPARATOR
CREATE TABLE IF NOT EXISTS `PaymentSystemsCollation` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`IsActive` enum('no','yes') DEFAULT 'no',
	`SortID` int(11) default '1000',
	`Source` varchar(1023),
	`Destination` varchar(1023),
	`Image` varchar(1023),
	`Prompt` longtext,
	`Description` text,
	`AdminNotice` text,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/* сбор статистики */
DROP TABLE IF EXISTS `Statistics`;
-- SEPARATOR
CREATE TABLE IF NOT EXISTS `Statistics` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`Stamp` int(11) NOT NULL,
	`Year` int(4) NOT NULL,
	`Month` int(2) NOT NULL,
	`Day` int(2) NOT NULL,
	`TableID` varchar(64),
	`PackageID` varchar(64),
	`Total` int(11) NOT NULL,
	`Active` int(11) NOT NULL,
	`New` int(11) NOT NULL,
	`Waiting` int(11) NOT NULL,
	`Suspended` int(11) NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/* контакты пользователей, в отдельную таблицу - т.к. их может быть много */
DROP TABLE IF EXISTS `Contacts`;
-- SEPARATOR
CREATE TABLE IF NOT EXISTS `Contacts` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,		-- идентфикатор
	`CreateDate` int(11) default '0',		-- дата создания записи
	`UserID` int(11) NOT NULL,			-- идентификатор юзера, владельца контакта
	`MethodID` char(30) default '',			-- метод (тип) оповещения: SMS, Email, Jabber...
	`Address` char(255) default '',			-- собственно контактный адрес
	`ExternalID` char(255) default '',		-- внешний идентификатор, для Telegramm и т.п.
	`Confirmed` INT(12) default '0',		-- подтверждён ил нет адрес, если подвтерждён - тут штамп времени когда это сделано
	`Confirmation` char(32) default '',		-- код подтверждения отосланный юзеру. вопрос о сроках его действия пока оставим открытым...
	`TimeBegin` char(30) default '00',		-- время начала рассылок по этому контакту
	`TimeEnd` char(30) default '00',		-- время конца рассылок по этому контакту
	`IsPrimary` enum('no','yes') DEFAULT 'no',	-- это первичный адрес, используется для входа в биллинг. пока, первичным может быть только почтовый адрес
	`IsActive` enum('no','yes') DEFAULT 'no',	-- можно использовать для оповещений
	`IsSendFiles` enum('no','yes') default 'yes',	-- отсылка вложений и файлов на контакт
	`IsImmediately` enum('no','yes') default 'yes',	-- отсылать немедленно если юзер находится в биллинге
	`IsHidden` enum('no','yes') DEFAULT 'no',	-- адрес скрыт, т.е. не используется у пользователя. сделано вместо удаления
	`UserNotice` text,				-- примечание пользователя о этом контакте
	PRIMARY KEY (`ID`),
	KEY `Confirmation` (`Confirmation`),
	KEY `ContactsUserID` (`UserID`),
	/* внешний ключ на таблицу юзеров */
	CONSTRAINT `ContactsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	/* уникальный ключ по юзеру-методу-адресу, чтобы не дублировались записи у одного пользователя */
	UNIQUE KEY `UserMethodAddress` (`UserID`, `MethodID`, `Address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* описания файлов */
DROP TABLE IF EXISTS `Files`;
-- SEPARATOR
CREATE TABLE IF NOT EXISTS `Files` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,		-- идентфикатор
	`CreateDate` int(11) default '0',		-- дата создания записи
	`TableID` char(30) default '',			-- таблица к которой относятся вложения
	`RowID` int(11) default '0',			-- строка в таблице, к которой относится вложение
	`Name` char(255) default '',			-- имя файла
	`Size` INT(8) default '0',			-- размер файла, в байтах 1.000.000.
	`Mime` char(255) default '',			-- mime тип файла (image/jpeg, text/html, application/pdf)
	PRIMARY KEY (`ID`),				-- первичный ключ
	KEY `TableRowID` (`TableID`,`RowID`)		-- ключ по таблице-строке - собственно по нему и будут искаться по большей части...
	/* ключ и внешний ключ на таблицу юзеров
	KEY `FilesUserID` (`UserID`),
	CONSTRAINT `FilesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE */
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* логгирование всех IP адресов юзера, для запросов из милиции */
DROP TABLE IF EXISTS `UsersIPs`;
-- SEPARATOR
CREATE TABLE IF NOT EXISTS `UsersIPs` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,		-- идентфикатор
	`CreateDate` int(11) default '0',		-- дата создания записи
	`UserID` int(11) NOT NULL,			-- идентификатор юзера
	`EdesksMessageID` int(11) NOT NULL,		-- идентификатор сообщения в системе поддержки (если это IP из сообщения)
	`IP` CHAR(40) NOT NULL DEFAULT '127.0.0.127',	-- IP адрес
	`UA` TEXT NOT NULL,				-- юзер-агент браузера
	PRIMARY KEY (`ID`),				-- первичный ключ
	/* ключ и внешний ключ на таблицу юзеров */
	KEY `UsersIPsUserID` (`UserID`),
	CONSTRAINT `UsersIPsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `UsersIPsCreateDate` (`CreateDate`)		-- ключ на дату создания записи
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- включаем внешние ключи взад
SET FOREIGN_KEY_CHECKS=1;



