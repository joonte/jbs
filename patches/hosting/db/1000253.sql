
DELETE FROM `Clauses` WHERE `Partition` = 'CreateTicket/ERROR_DOMAIN_REGISTER';

-- SEPARATOR

INSERT INTO `Clauses` (`ID`, `PublicDate`, `ChangedDate`, `AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `IsPublish`, `Text`) VALUES
(217, 1352145600, 1352184464, 2248, 2248, 'CreateTicket/ERROR_DOMAIN_REGISTER', 'Ошибка регистрации домена', 'no', 'yes', 'no', 'yes', 'Здравствуйте. При регистрации заказанного вами домена, произошла ошибка, из-за того, что Вы ввели неверные паспортные данные в профиль.<br />\nДля успешной регистрации доменного имени необходимо ввести корректные паспортные данные.<br />\nОбратите внимание, что паспортные данные проверяет робот вышестоящего регистратора, и ошибочные он не пропустит.<br />\nИсправить данные можно тут: [color:green]Мой офис -> Профили[/color]');



