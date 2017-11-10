

INSERT INTO `PaymentSystemsCollation` (`IsActive`,`SortID`,`Source`,`Destination`,`Image`,`Prompt`,`Description`)
	VALUES
('yes',16,'SberBank','VISA','Visa.MasterCard.png','Оплата при помощи пластиковой карты МИР, VISA или MasterCard.','МИР/VISA/MasterCard');

-- SEPARATOR
-- added by lissyara 2017-11-10 in 09:11 MSK, for JBS-1230
INSERT INTO `Clauses` (`GroupID`, `AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `Text`, `IsPublish`) VALUES
(6, 100, 100, 'Invoices/PaymentSystems/SberBank', 'Шаблон платежной системы SberBank', 'yes', 'yes', 'yes', '<NOBODY>\r\n <H1>\r\n СЧЕТ №%Invoice.Number% от %Invoice.CreateDate%\r\n</H1>\r\n <DIV id="Services">\r\n [список услуг]\r\n</DIV>\r\n <H2>\r\n Платежное поручение\r\n</H2>\r\n <TABLE border="1" cellpadding="5" cellspacing="0">\r\n  <TBODY>\r\n   <TR bgcolor="#DCDCDC">\r\n    <TD align="center">\r\n    Назначение\r\n   </TD>\r\n    <TD align="center">\r\n    Сумма\r\n   </TD>\r\n   </TR>\r\n   <TR>\r\n    <TD>\r\n    За web-услуги по счету №%Invoice.Number%\r\n   </TD>\r\n    <TD align="right">\r\n    %Invoice.Foreign% %PaymentSystem.Measure%\r\n   </TD>\r\n   </TR>\r\n  </TBODY>\r\n </TABLE>\r\n</NOBODY>\r\n', 'yes');



