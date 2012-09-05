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
DROP VIEW IF EXISTS `EdesksOwners`;
DROP TABLE IF EXISTS `EdesksOwners`;
CREATE
  VIEW `EdesksOwners` AS
SELECT
  `Edesks`.*
FROM
  `Edesks`;
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
  `Orders`.*,
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
