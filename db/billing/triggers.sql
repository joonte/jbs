DROP TRIGGER IF EXISTS `UsersOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `UsersOnInsert` BEFORE INSERT ON `Users`
  FOR EACH ROW BEGIN
    IF NEW.`RegisterDate` = 0
      THEN
        SET NEW.`RegisterDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `ProfilesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `ProfilesOnInsert` BEFORE INSERT ON `Profiles`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `ContractsOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `ContractsOnInsert` BEFORE INSERT ON `Contracts`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `ContractsEnclosuresOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `ContractsEnclosuresOnInsert` BEFORE INSERT ON `ContractsEnclosures`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
        SET NEW.`Number` = IF(EXISTS(SELECT * FROM `ContractsEnclosures` WHERE `ContractID` = NEW.`ContractID`),(SELECT MAX(`Number`) FROM `ContractsEnclosures` WHERE `ContractID` = NEW.`ContractID`) + 1,1);
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `InvoicesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `InvoicesOnInsert` BEFORE INSERT ON `Invoices`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `InvoicesItemsOnInserted`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `InvoicesItemsOnInserted` AFTER INSERT ON `InvoicesItems`
  FOR EACH ROW BEGIN
    UPDATE `Invoices` SET `Summ` = (SELECT SUM(`Summ`) FROM `InvoicesItems` WHERE `InvoicesItems`.`InvoiceID` = NEW.`InvoiceID`) WHERE `ID` = NEW.`InvoiceID`;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `InvoicesItemsOnUpdated`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `InvoicesItemsOnUpdated` AFTER UPDATE ON `InvoicesItems`
  FOR EACH ROW BEGIN
    UPDATE `Invoices` SET `Summ` = (SELECT SUM(`Summ`) FROM `InvoicesItems` WHERE `InvoicesItems`.`InvoiceID` = OLD.`InvoiceID`) WHERE `ID` = OLD.`InvoiceID`;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `PostingsOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `PostingsOnInsert` BEFORE INSERT ON `Postings`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `MotionDocumentsOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `MotionDocumentsOnInsert` BEFORE INSERT ON `MotionDocuments`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `TasksOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `TasksOnInsert` BEFORE INSERT ON `Tasks`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `EdesksOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `EdesksOnInsert` BEFORE INSERT ON `Edesks`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `EdesksMessagesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `EdesksMessagesOnInsert` BEFORE INSERT ON `EdesksMessages`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `ClausesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `ClausesOnInsert` BEFORE INSERT ON `Clauses`
  FOR EACH ROW BEGIN
    IF NEW.`PublicDate` = 0
      THEN
        SET NEW.`PublicDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `ClausesGroupsOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `ClausesGroupsOnInsert` BEFORE INSERT ON `ClausesGroups`
  FOR EACH ROW BEGIN
    IF NEW.`PublicDate` = 0
      THEN
        SET NEW.`PublicDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `ClausesFilesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `ClausesFilesOnInsert` BEFORE INSERT ON `ClausesFiles`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `WorksCompliteOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `WorksCompliteOnInsert` BEFORE INSERT ON `WorksComplite`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `OrdersOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `OrdersOnInsert` BEFORE INSERT ON `Orders`
  FOR EACH ROW BEGIN
    IF NEW.`OrderDate` = 0
      THEN
        SET NEW.`OrderDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `EventsOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `EventsOnInsert` BEFORE INSERT ON `Events`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;

/* реализация JBS-157 */

DROP TRIGGER IF EXISTS `BonusesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `BonusesOnInsert` BEFORE INSERT ON `Bonuses`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
    IF NEW.`ExpirationDate` = 0
      THEN
        SET NEW.`ExpirationDate` = (UNIX_TIMESTAMP() + NEW.`DaysReserved` * 24 * 3600);
    END IF;
    IF NEW.`DaysRemainded` = 0
      THEN
        SET NEW.`DaysRemainded` = NEW.`DaysReserved`;
    END IF;
  END;
|
DELIMITER ;

#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `UsersIPsOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `UsersIPsOnInsert` BEFORE INSERT ON `UsersIPs`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;



