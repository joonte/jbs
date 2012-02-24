CREATE
  VIEW `HostingOrdersOwners` AS
SELECT
  `HostingOrders`.*,`OrdersOwners`.`OrderDate`,`OrdersOwners`.`UserID`,`OrdersOwners`.`ContractID`,`OrdersOwners`.`Balance`
FROM
  `HostingOrders`
LEFT JOIN `OrdersOwners`
ON (`HostingOrders`.`OrderID` = `OrdersOwners`.`ID`);