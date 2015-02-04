DROP TRIGGER IF EXISTS `HostingSchemesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `HostingSchemesOnInsert` BEFORE INSERT ON `HostingSchemes`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `HostingBonusesOnInsert`;
#-------------------------------------------------------------------------------
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `HostingConsiderOnInsert`;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `HostingConsiderOnInserted`;
/* moved to OrdersConsider */
#-------------------------------------------------------------------------------
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `HostingConsiderOnUpdated`;
#-------------------------------------------------------------------------------
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `HostingConsiderOnDeleted`;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `DomainsSchemesOnInsert`;
DROP TRIGGER IF EXISTS `DomainSchemesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `DomainSchemesOnInsert` BEFORE INSERT ON `DomainSchemes`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `DomainsBonusesOnInsert`;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `DomainsConsiderOnInsert`;
DROP TRIGGER IF EXISTS `DomainConsiderOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `DomainConsiderOnInsert` BEFORE INSERT ON `DomainConsider`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
    IF NEW.`YearsRemainded` = 0
      THEN
        SET NEW.`YearsRemainded` = NEW.`YearsReserved`;
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `HostingOrdersOnDelete`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `HostingOrdersOnDelete` AFTER DELETE ON `HostingOrders`
  FOR EACH ROW BEGIN
    DELETE FROM `Orders` WHERE `ID` = OLD.`OrderID`;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `DomainsOrdersOnDelete`;
DROP TRIGGER IF EXISTS `DomainOrdersOnDelete`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `DomainOrdersOnDelete` AFTER DELETE ON `DomainOrders`
  FOR EACH ROW BEGIN
    DELETE FROM `Orders` WHERE `ID` = OLD.`OrderID`;
  END;
|
DELIMITER ;


/* VPS values added by lissyara 2011-06-22 in 15:49 MSK */


DROP TRIGGER IF EXISTS `VPSSchemesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `VPSSchemesOnInsert` BEFORE INSERT ON `VPSSchemes`
	FOR EACH ROW BEGIN
	IF NEW.`CreateDate` = 0
        THEN
		SET NEW.`CreateDate` = UNIX_TIMESTAMP();
	END IF;
END
|
DELIMITER ;

-- SEPARATOR
DROP TRIGGER IF EXISTS `VPSOrdersOnDelete`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `VPSOrdersOnDelete` AFTER DELETE ON `VPSOrders`
FOR EACH ROW BEGIN
DELETE FROM `Orders` WHERE `ID` = OLD.`OrderID`;
END
|
DELIMITER ;

-- SEPARATOR
DROP TRIGGER IF EXISTS `VPSBonusesOnInsert`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `VPSConsiderOnInsert`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `VPSConsiderOnInserted`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `VPSConsiderOnUpdated`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `VPSConsiderOnDeleted`;

/* DS values added by lissyara 2011-06-29 in 20:32 MSK */

DROP TRIGGER IF EXISTS `DSSchemesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `DSSchemesOnInsert` BEFORE INSERT ON `DSSchemes`
	FOR EACH ROW BEGIN
	IF NEW.`CreateDate` = 0
        THEN
		SET NEW.`CreateDate` = UNIX_TIMESTAMP();
	END IF;
END
|
DELIMITER ;

-- SEPARATOR
DROP TRIGGER IF EXISTS `DSOrdersOnDelete`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `DSOrdersOnDelete` AFTER DELETE ON `DSOrders`
FOR EACH ROW BEGIN
DELETE FROM `Orders` WHERE `ID` = OLD.`OrderID`;
END
|
DELIMITER ;

-- SEPARATOR
DROP TRIGGER IF EXISTS `DSBonusesOnInsert`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `DSConsiderOnInsert`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `DSConsiderOnInserted`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `DSConsiderOnUpdated`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `DSConsiderOnDeleted`;

/* ExtraIP values added by lissyara 2011-08-08 in 15:08 MSK */

DROP TRIGGER IF EXISTS `ExtraIPSchemesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `ExtraIPSchemesOnInsert` BEFORE INSERT ON `ExtraIPSchemes`
	FOR EACH ROW BEGIN
	IF NEW.`CreateDate` = 0
        THEN
		SET NEW.`CreateDate` = UNIX_TIMESTAMP();
	END IF;
END
|
DELIMITER ;

-- SEPARATOR
DROP TRIGGER IF EXISTS `ExtraIPOrdersOnDelete`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `ExtraIPOrdersOnDelete` AFTER DELETE ON `ExtraIPOrders`
FOR EACH ROW BEGIN
DELETE FROM `Orders` WHERE `ID` = OLD.`OrderID`;
END
|
DELIMITER ;

-- SEPARATOR
DROP TRIGGER IF EXISTS `ExtraIPBonusesOnInsert`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `ExtraIPConsiderOnInsert`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `ExtraIPConsiderOnInserted`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `ExtraIPConsiderOnUpdated`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `ExtraIPConsiderOnDeleted`;


/* ISPsw values added by lissyara 2011-09-06 in 15:23 MSK */

-- SEPARATOR
DROP TRIGGER IF EXISTS `ISPswSchemesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `ISPswSchemesOnInsert` BEFORE INSERT ON `ISPswSchemes`
	FOR EACH ROW BEGIN
	IF NEW.`CreateDate` = 0
        THEN
		SET NEW.`CreateDate` = UNIX_TIMESTAMP();
	END IF;
END
|
DELIMITER ;

-- SEPARATOR
DROP TRIGGER IF EXISTS `ISPswOrdersOnDelete`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `ISPswOrdersOnDelete` AFTER DELETE ON `ISPswOrders`
FOR EACH ROW BEGIN
DELETE FROM `Orders` WHERE `ID` = OLD.`OrderID`;
END
|
DELIMITER ;

-- SEPARATOR
DROP TRIGGER IF EXISTS `ISPswBonusesOnInsert`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `ISPswConsiderOnInsert`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `ISPswConsiderOnInserted`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `ISPswConsiderOnUpdated`;
-- SEPARATOR
/* moved to OrdersConsider */
DROP TRIGGER IF EXISTS `ISPswConsiderOnDeleted`;

#-------------------------------------------------------------------------------
# added by lissyara 2011-10-03 in 15:07 MSK, for JBS-148
DROP TRIGGER IF EXISTS `UpdateHostingStatus`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `UpdateHostingStatus` AFTER UPDATE ON `HostingOrders`
  FOR EACH ROW BEGIN
    UPDATE `Orders` SET `StatusID` = NEW.`StatusID`, `StatusDate` = NEW.`StatusDate` WHERE `ID` = NEW.`OrderID`;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `UpdateDomainsStatus`;
DROP TRIGGER IF EXISTS `UpdateDomainStatus`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `UpdateDomainStatus` AFTER UPDATE ON `DomainOrders`
  FOR EACH ROW BEGIN
    UPDATE `Orders` SET `StatusID` = NEW.`StatusID`, `StatusDate` = NEW.`StatusDate`, `ExpirationDate` = NEW.`ExpirationDate`  WHERE `ID` = NEW.`OrderID`;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `UpdateVPSStatus`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `UpdateVPSStatus` AFTER UPDATE ON `VPSOrders`
  FOR EACH ROW BEGIN
    UPDATE `Orders` SET `StatusID` = NEW.`StatusID`, `StatusDate` = NEW.`StatusDate` WHERE `ID` = NEW.`OrderID`;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `UpdateDSStatus`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `UpdateDSStatus` AFTER UPDATE ON `DSOrders`
  FOR EACH ROW BEGIN
    UPDATE `Orders` SET `StatusID` = NEW.`StatusID`, `StatusDate` = NEW.`StatusDate` WHERE `ID` = NEW.`OrderID`;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `UpdateExtraIPStatus`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `UpdateExtraIPStatus` AFTER UPDATE ON `ExtraIPOrders`
  FOR EACH ROW BEGIN
    UPDATE `Orders` SET `StatusID` = NEW.`StatusID`, `StatusDate` = NEW.`StatusDate` WHERE `ID` = NEW.`OrderID`;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `UpdateISPswStatus`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `UpdateISPswStatus` AFTER UPDATE ON `ISPswOrders`
  FOR EACH ROW BEGIN
    UPDATE `Orders` SET `StatusID` = NEW.`StatusID`, `StatusDate` = NEW.`StatusDate` WHERE `ID` = NEW.`OrderID`;
  END;
|
DELIMITER ;

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# реализация JBS-300

DROP TRIGGER IF EXISTS `OrdersConsiderOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `OrdersConsiderOnInsert` BEFORE INSERT ON `OrdersConsider`
  FOR EACH ROW BEGIN
    IF NEW.`CreateDate` = 0
      THEN
        SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    END IF;
    IF NEW.`DaysRemainded` = 0
      THEN
        SET NEW.`DaysRemainded` = NEW.`DaysReserved`;
    END IF;
    IF NEW.`DaysConsidered` = 0
      THEN
        SET NEW.`DaysConsidered` = NEW.`DaysReserved`;
    END IF;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `OrdersConsiderOnInserted`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `OrdersConsiderOnInserted` AFTER INSERT ON `OrdersConsider`
  FOR EACH ROW BEGIN
    UPDATE `Orders` SET `DaysRemainded` = (SELECT SUM(`DaysRemainded`) FROM `OrdersConsider` WHERE `OrdersConsider`.`OrderID` = NEW.`OrderID` AND `OrdersConsider`.`DaysRemainded` > 0) WHERE `ID` = NEW.`OrderID`;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `OrdersConsiderOnUpdated`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `OrdersConsiderOnUpdated` AFTER UPDATE ON `OrdersConsider`
  FOR EACH ROW BEGIN
    UPDATE `Orders` SET `DaysRemainded` = (SELECT SUM(`DaysRemainded`) FROM `OrdersConsider` WHERE `OrdersConsider`.`OrderID` = OLD.`OrderID` AND `OrdersConsider`.`DaysRemainded` > 0) WHERE `ID` = OLD.`OrderID`;
  END;
|
DELIMITER ;
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `OrdersConsiderOnDeleted`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `OrdersConsiderOnDeleted` AFTER DELETE ON `OrdersConsider`
  FOR EACH ROW BEGIN
    UPDATE `Orders` SET `DaysRemainded` = (SELECT SUM(`DaysRemainded`) FROM `OrdersConsider` WHERE `OrdersConsider`.`OrderID` = OLD.`OrderID` AND `OrdersConsider`.`DaysRemainded` > 0) WHERE `ID` = OLD.`OrderID`;
  END;
|
DELIMITER ;


# added by lissyara, 2014-12-24 in 13:01 MSK
#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `DNSmanagerSchemesOnInsert`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `DNSmanagerSchemesOnInsert` BEFORE INSERT ON `DNSmanagerSchemes`
	FOR EACH ROW BEGIN
		IF NEW.`CreateDate` = 0
		THEN
			SET NEW.`CreateDate` = UNIX_TIMESTAMP();
		END IF;
	END;
|
DELIMITER ;

#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `DNSmanagerOrdersOnDelete`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `DNSmanagerOrdersOnDelete` AFTER DELETE ON `DNSmanagerOrders`
	FOR EACH ROW BEGIN
		DELETE FROM `Orders` WHERE `ID` = OLD.`OrderID`;
	END;
|
DELIMITER ;

#-------------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `UpdateDNSmanagerStatus`;
DELIMITER |
CREATE DEFINER = CURRENT_USER TRIGGER `UpdateDNSmanagerStatus` AFTER UPDATE ON `DNSmanagerOrders`
	FOR EACH ROW BEGIN
		UPDATE `Orders` SET `StatusID` = NEW.`StatusID`, `StatusDate` = NEW.`StatusDate` WHERE `ID` = NEW.`OrderID`;
	END;
|
DELIMITER ;






