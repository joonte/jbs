
DELETE FROM `Users` WHERE `ID` IN (300);

-- SEPARATOR

INSERT INTO `Users`
  (`ID`,`GroupID`,`Name`,`Watchword`,`Email`,`Sign`,`IsActive`,`IsProtected`,`AdminNotice`)
  VALUES
  (300,3000000,'Сайт компании','e02f7b992578cd299e3e3edaed120689','site@example.com','С уважением, сайт ООО \"Компания\".','no','yes','Пользователь с доступом только к API, используется для работы сайта');

