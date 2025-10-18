DROP VIEW IF EXISTS `GroupsOwners`;
DROP TABLE IF EXISTS `GroupsOwners`;
CREATE
  VIEW `GroupsOwners` AS
SELECT
  `Groups`.*,
  100 as `UserID`
FROM
  `Groups`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `UsersOwners`;
DROP TABLE IF EXISTS `UsersOwners`;
CREATE
  VIEW `UsersOwners` AS
SELECT
  `Users`.*,
  `Users`.`ID` as `UserID`
FROM
  `Users`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ProfilesOwners`;
DROP TABLE IF EXISTS `ProfilesOwners`;
CREATE
  VIEW `ProfilesOwners` AS
SELECT
  `Profiles`.*
FROM
  `Profiles`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ContractsOwners`;
DROP TABLE IF EXISTS `ContractsOwners`;
CREATE
  VIEW `ContractsOwners` AS
SELECT
  `Contracts`.*
FROM
  `Contracts`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ContractsEnclosuresOwners`;
DROP TABLE IF EXISTS `ContractsEnclosuresOwners`;
CREATE
  VIEW `ContractsEnclosuresOwners` AS
SELECT
  `ContractsEnclosures`.*,
  `Contracts`.`UserID`
FROM
  `ContractsEnclosures`
LEFT JOIN `Contracts`
ON (`ContractsEnclosures`.`ContractID` = `Contracts`.`ID`);
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `InvoicesOwners`;
DROP TABLE IF EXISTS `InvoicesOwners`;
CREATE
  VIEW `InvoicesOwners` AS
SELECT
  `Invoices`.*,
  `Contracts`.`UserID`
FROM
  `Invoices`
LEFT JOIN `Contracts`
ON (`Invoices`.`ContractID` = `Contracts`.`ID`);
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `WorksCompliteOwners`;
DROP TABLE IF EXISTS `WorksCompliteOwners`;
CREATE
  VIEW `WorksCompliteOwners` AS
SELECT
  `WorksComplite`.*,
  `Contracts`.`UserID`
FROM
  `WorksComplite`
LEFT JOIN `Contracts`
ON (`WorksComplite`.`ContractID` = `Contracts`.`ID`);
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `WorksCompliteAgregate`;
DROP TABLE IF EXISTS `WorksCompliteAgregate`;
CREATE
  VIEW `WorksCompliteAgregate` AS
SELECT
  `WorksComplite`.`ID`,
  `WorksComplite`.`ContractID`,
  `WorksComplite`.`Month`,
  `WorksComplite`.`ServiceID`,
  `WorksComplite`.`Comment`,
  `WorksComplite`.`Cost`,
  `WorksComplite`.`Discont`,
  SUM(`WorksComplite`.`Amount`) as `Amount`
FROM
  `WorksComplite`
GROUP BY
  `ContractID`,`Month`,`ServiceID`,`Comment`,`Cost`,`Discont`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `MotionDocumentsOwners`;
DROP TABLE IF EXISTS `MotionDocumentsOwners`;
CREATE
  VIEW `MotionDocumentsOwners` AS
SELECT
  `MotionDocuments`.*,
  `Contracts`.`UserID`
FROM
  `MotionDocuments`
LEFT JOIN `Contracts`
ON (`MotionDocuments`.`ContractID` = `Contracts`.`ID`);
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `PostingsOwners`;
DROP TABLE IF EXISTS `PostingsOwners`;
CREATE
  VIEW `PostingsOwners` AS
SELECT
  `Postings`.*,
  `Contracts`.`UserID`
FROM
  `Postings`
LEFT JOIN `Contracts`
ON (`Postings`.`ContractID` = `Contracts`.`ID`);
#-------------------------------------------------------------------------------
/* дубликат
DROP VIEW IF EXISTS `EdesksOwners`;
DROP TABLE IF EXISTS `EdesksOwners`;
CREATE
  VIEW `EdesksOwners` AS
SELECT
  `Edesks`.*
FROM
  `Edesks`;
*/
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `EdesksMessagesOwners`;
DROP TABLE IF EXISTS `EdesksMessagesOwners`;
CREATE
  VIEW `EdesksMessagesOwners` AS
SELECT
  `EdesksMessages`.*
FROM
  `EdesksMessages`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `TasksOwners`;
DROP TABLE IF EXISTS `TasksOwners`;
CREATE
  VIEW `TasksOwners` AS
SELECT
  `Tasks`.*
FROM
  `Tasks`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `WorksCompliteReports`;
DROP TABLE IF EXISTS `WorksCompliteReports`;
CREATE
  VIEW `WorksCompliteReports` AS
SELECT
  WorksComplite.*,
  SUM(`Amount`) as `WorksCount`,
  `Contracts`.`UserID`
FROM
  `WorksComplite`
LEFT JOIN `Contracts`
ON (`WorksComplite`.`ContractID` = `Contracts`.`ID`)
GROUP BY
  `Month`,`ContractID`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ClausesOwners`;
DROP TABLE IF EXISTS `ClausesOwners`;
CREATE
  VIEW `ClausesOwners` AS
SELECT
  `Clauses`.*,
  `Clauses`.`AuthorID` as `UserID`
FROM
  `Clauses`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ClausesGroupsOwners`;
DROP TABLE IF EXISTS `ClausesGroupsOwners`;
CREATE
  VIEW `ClausesGroupsOwners` AS
SELECT
  `ClausesGroups`.*,
  `ClausesGroups`.`AuthorID` as `UserID`
FROM
  `ClausesGroups`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ClausesFilesOwners`;
DROP TABLE IF EXISTS `ClausesFilesOwners`;
CREATE
  VIEW `ClausesFilesOwners` AS
SELECT
  `ClausesFiles`.*,
  `Clauses`.`AuthorID` as `UserID`
FROM
  `ClausesFiles`
LEFT JOIN `Clauses`
ON (`ClausesFiles`.`ClauseID` = `Clauses`.`ID`);
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `OrdersOwners`;
DROP TABLE IF EXISTS `OrdersOwners`;
CREATE
  VIEW `OrdersOwners` AS
SELECT
  `Orders`.*,`Orders`.`ID` `OrderID`,
  `Contracts`.`UserID`
FROM
  `Orders`
LEFT JOIN `Contracts`
ON (`Orders`.`ContractID` = `Contracts`.`ID`);
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `BasketOwners`;
DROP TABLE IF EXISTS `BasketOwners`;
CREATE
  VIEW `BasketOwners` AS
SELECT
  `Basket`.*,
  `OrdersOwners`.`ServiceID`,
  `OrdersOwners`.`ContractID`,
  `OrdersOwners`.`UserID`
FROM
  `Basket`
LEFT JOIN `OrdersOwners`
ON (`Basket`.`OrderID` = `OrdersOwners`.`ID`);
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ServicesGroupsOwners`;
DROP TABLE IF EXISTS `ServicesGroupsOwners`;
CREATE
  VIEW `ServicesGroupsOwners` AS
SELECT
  `ServicesGroups`.*,
  100 as `UserID`
FROM
  `ServicesGroups`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ServicesOwners`;
DROP TABLE IF EXISTS `ServicesOwners`;
CREATE
  VIEW `ServicesOwners` AS
SELECT
  `Services`.*
FROM
  `Services`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ServicesFieldsOwners`;
DROP TABLE IF EXISTS `ServicesFieldsOwners`;
CREATE
  VIEW `ServicesFieldsOwners` AS
SELECT
  `ServicesFields`.*,
  100 as `UserID`
FROM
  `ServicesFields`;
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `OrdersFieldsOwners`;
DROP TABLE IF EXISTS `OrdersFieldsOwners`;
CREATE
  VIEW `OrdersFieldsOwners` AS
SELECT
  `OrdersFields`.*,
  `OrdersOwners`.`UserID` as `UserID`
FROM
  `OrdersFields`
LEFT JOIN `OrdersOwners`
ON (`OrdersFields`.`OrderID` = `OrdersOwners`.`ID`);
#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `EdesksOwners`;
DROP TABLE IF EXISTS `EdesksOwners`;
CREATE
  VIEW `EdesksOwners` AS
SELECT
  `Edesks`.*,
  `EdesksMessages`.`Content` as `Content`,
  `EdesksMessages`.`ID` AS `MessageID`
FROM
  `Edesks`
LEFT JOIN `EdesksMessages`
ON (`Edesks`.`ID` = `EdesksMessages`.`EdeskID`);


/* реализация JBS-157 / JBS-158 */
#-------------------------------------------------------------------------------

DROP VIEW IF EXISTS `BonusesOwners`;
DROP TABLE IF EXISTS `BonusesOwners`;
CREATE
   VIEW `BonusesOwners` AS
SELECT
  `Bonuses`.*
FROM
  `Bonuses`;

#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `PoliticsOwners`;
DROP TABLE IF EXISTS `PoliticsOwners`;
CREATE
   VIEW `PoliticsOwners` AS
SELECT
   `Politics`.*
FROM
   `Politics`;

#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `SchemesGroupsOwners`;
DROP TABLE IF EXISTS `SchemesGroupsOwners`;
CREATE
   VIEW `SchemesGroupsOwners` AS
SELECT
   `SchemesGroups`.*,
   100 as `UserID`
FROM
   `SchemesGroups`;

#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `SchemesGroupsItemsOwners`;
DROP TABLE IF EXISTS `SchemesGroupsItemsOwners`;
CREATE
   VIEW `SchemesGroupsItemsOwners` AS
SELECT
   `SchemesGroupsItems`.*,
   100 as `UserID`
FROM
   `SchemesGroupsItems`;


#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `PromoCodesOwners`;
DROP TABLE IF EXISTS `PromoCodesOwners`;
CREATE
   VIEW `PromoCodesOwners` AS
SELECT
   `PromoCodes`.*,
   100 as `UserID`
FROM
   `PromoCodes`;

#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `PromoCodesExtinguishedOwners`;
DROP TABLE IF EXISTS `PromoCodesExtinguishedOwners`;
CREATE
   VIEW `PromoCodesExtinguishedOwners` AS
SELECT
   `PromoCodesExtinguished`.*
FROM
   `PromoCodesExtinguished`;

#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `PaymentSystemsCollationOwners`;
DROP TABLE IF EXISTS `PaymentSystemsCollationOwners`;
CREATE
	VIEW `PaymentSystemsCollationOwners` AS
SELECT
	`PaymentSystemsCollation`.*,
	100 as `UserID`
FROM
	`PaymentSystemsCollation`;

#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `ContactsOwners`;
DROP TABLE IF EXISTS `ContactsOwners`;
CREATE
   VIEW `ContactsOwners` AS
SELECT
   `Contacts`.*
FROM
   `Contacts`;

#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `FilesOwners`;
DROP TABLE IF EXISTS `FilesOwners`;
CREATE
   VIEW `FilesOwners` AS
SELECT
   `Files`.*,
   100 as `UserID`
FROM
   `Files`;

#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `OrdersTransferOwners`;
DROP TABLE IF EXISTS `OrdersTransferOwners`;
CREATE
   VIEW `OrdersTransferOwners` AS
SELECT
   `OrdersTransfer`.*
FROM
   `OrdersTransfer`;

#-------------------------------------------------------------------------------
DROP VIEW IF EXISTS `UsersIPsOwners`;
DROP TABLE IF EXISTS `UsersIPsOwners`;
CREATE
   VIEW `UsersIPsOwners` AS
SELECT
   `UsersIPs`.*
FROM
   `UsersIPs`;



