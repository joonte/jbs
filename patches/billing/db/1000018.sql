CREATE
  VIEW `ContractsEnclosuresOwners` AS
SELECT
  `ContractsEnclosures`.*,`Contracts`.`UserID`,`Contracts`.`CustomerName`
FROM
  `ContractsEnclosures`
LEFT JOIN `Contracts`
ON (`ContractsEnclosures`.`ContractID` = `Contracts`.`ID`);