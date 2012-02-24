CREATE DEFINER = CURRENT_USER TRIGGER `DomainsConsiderOnInsert` BEFORE INSERT ON `DomainsConsider`
  FOR EACH ROW BEGIN
    SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    SET NEW.`YearsRemainded` = NEW.`YearsReserved`;
  END;