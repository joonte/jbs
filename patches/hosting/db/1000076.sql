CREATE DEFINER = CURRENT_USER TRIGGER `HostingConsiderOnInsert` BEFORE INSERT ON `HostingConsider`
  FOR EACH ROW BEGIN
    SET NEW.`CreateDate` = UNIX_TIMESTAMP();
    SET NEW.`DaysRemainded` = NEW.`DaysReserved`;
  END;