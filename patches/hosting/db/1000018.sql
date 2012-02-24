CREATE
  VIEW `DomainsOrdersOwners` AS
SELECT
  `DomainsOrders`.*,`OrdersOwners`.`OrderDate`,`OrdersOwners`.`UserID`,`OrdersOwners`.`ContractID`,`OrdersOwners`.`Balance`
FROM
  `DomainsOrders`
LEFT JOIN `OrdersOwners`
ON (`DomainsOrders`.`OrderID` = `OrdersOwners`.`ID`);