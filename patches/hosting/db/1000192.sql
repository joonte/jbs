DROP VIEW IF EXISTS `DomainsOrdersOwners`;
-- SEPARATOR
DROP TABLE IF EXISTS `DomainsOrdersOwners`;
-- SEPARATOR
CREATE
  VIEW `DomainsOrdersOwners` AS
SELECT
  `DomainsOrders`.*,
  `DomainsSchemes`.`Name`,
  `DomainsSchemes`.`RegistratorID`,
  `OrdersOwners`.`OrderDate`,
  `OrdersOwners`.`UserID`,
  `OrdersOwners`.`ContractID`
FROM
  `DomainsOrders`
LEFT JOIN `DomainsSchemes`
ON (`DomainsOrders`.`SchemeID` = `DomainsSchemes`.`ID`)
LEFT JOIN `OrdersOwners`
ON (`DomainsOrders`.`OrderID` = `OrdersOwners`.`ID`);