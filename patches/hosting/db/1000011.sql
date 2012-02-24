CREATE DEFINER = CURRENT_USER TRIGGER `HostingSchemesOnInsert` BEFORE INSERT ON `HostingSchemes`
  FOR EACH ROW BEGIN
    SET NEW.`CreateDate` = UNIX_TIMESTAMP();
  END;