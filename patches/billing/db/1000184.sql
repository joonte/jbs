INSERT INTO `Users`
  (`ID`,`GroupID`,`OwnerID`,`Name`,`Watchword`,`Email`,`Sign`,`IsActive`,`IsProtected`)
VALUES
(200,3000000,1,'Клиент',MD5('nopassword'),'client@company.com','С уважением, Клиент.','no','yes');