UPDATE `DomainsBonuses` SET `UserID` = (SELECT `UserID` FROM `Contracts` WHERE `DomainsBonuses`.`ContractID` = `Contracts`.`ID`);