

DELETE FROM `Tasks` WHERE `ID` IN (95);
-- SEPARATOR
INSERT INTO `Tasks` (`ID`,`UserID`,`TypeID`,`Params`,`IsActive`) VALUES
(95,1,'ProxyForDelete','[]','yes');

-- SEPARATOR
/* added by lissyara, 2014-12-25 in 21:37 MSK */
INSERT INTO `Clauses` (`GroupID`,`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES (4,100,100,'yes','yes','yes','Contracts/Enclosures/Types/ProxyRules/Content','Регламент предоставления услуги Прокси-сервер','<NOBODY>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">Услуга "Прокси-сервер" предосталвляется по принципу "как есть" и Исполнитель не несет какой-либо ответственности за причинение или возможность причинения вреда Заказчику, его информации или бизнесу вследствие использования или невозможности использования услуги.</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">На наших серверах запрещены:</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">•</td>
<td align="justify" width="98%">Мошеничество, взломы, оскорбления, угрозы и клевета;</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">•</td>
<td align="justify" width="98%">Подбор паролей (брутфорс), сканирование и уязвимость портов;</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">•</td>
<td align="justify" width="98%">Создание фишинговых сайтов;</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">•</td>
<td align="justify" width="98%">Спам (включая в себя спам на форумах, сайтах, блогах), любая активность, которая может привести к попаданию IP адреса сервера в блек листы (BlockList.de, SpamHaus, StopForumSpam, SpamCop и др.);</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">•</td>
<td align="justify" width="98%">E-mail рассылка;</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">•</td>
<td align="justify" width="98%">Распространение вредоносных программ (вирусов, троянов и все что может влиять на работу ПО);</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">•</td>
<td align="justify" width="98%">Взлом сайтов и поиск их уязвимостей (включая sql-инъекции);</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">•</td>
<td align="justify" width="98%">Распространение материалов без ведома правообладателя (Видео, музыка, софт и др.);</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">•</td>
<td align="justify" width="98%">Нарушение законов страны в которой расположен сервер к которому вы подключаетесь;</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">•</td>
<td align="justify" width="98%">Массовая регистрация аккаунтов на различных сервисах, форумах и в социальных сетях;</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">Совершая противоправные действия, вы несете полную личную, административную и уголовную ответственность за все действия и их последствия.</td>
</tr>
</tbody>
</table>
<table style="width: 100%;">
<tbody>
<tr>
<td valign="top">При нарушении правил пользования, сервис оставляет за собой право в одностороннем порядке прекратить обслуживание клиента без возможности восстановления.</td>
</tr>
</tbody>
</table>
</NOBODY>');

-- SEPARATOR

INSERT INTO `Clauses` (`GroupID`, `AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `IsPublish`, `Text`)
VALUES (9, 100, 100, 'Header:/ProxyOrders', 'Дополнительная услуга - Прокси-сервер', 'no', 'yes', 'yes', 'yes', '<NOBODY>
<p>Услуга "Прокси-сервер" необходима:</p>
<ul class="Standard" type="square">
<li>Тем, кто хочет скрыть свой реальный IP адрес и DNS </li>
<li>Обеспечить анонимное и безопасное использование интернета </li>
<li>Скрыть интернет активность от своего провайдера интернет </li>
<li>Снять ограничения сервисов по IP, GEO данным, порту и протоколу </li>
</ul>
<hr size="1" />
<p>Обращаем Ваше внимание, что:</p>
<ul class="Standard" type="square">
<li><b>Отсутствует техническая возможность возврата средств и отказа от услуги - по любой причине (не та версия протокола, не могу использовать, не знаю, не умею, не подходит, и т.п.)</b></li>
<li>25 порт закрыт. Email рассылка запрещена</li>
<li>Не работает Steam, Qiwi, Paypal, WebMoney и Яндекс.Деньги, так как участились случаи мошенничества</li>
<li>Спам в социальные сети не проходит</li>
<li>Скорость прокси: IPv4 - до 10 Мбит/с, IPv6 - до 30 Мбит/с</li>
<li>Прокси в формате <strong>HTTPs</strong> и <strong>SOCKS5</strong></li>
<li>Авторизация прокси происходит по логину и паролю </li>
<li>Проверить прокси можно тут: <a href="https://www.yandex.ru/internet/">https://www.yandex.ru/internet/</a></li>
</ul>
</NOBODY>\n');



-- SEPARATOR

DELETE FROM `Services` WHERE `ID` = 53000;
-- SEPARATOR

INSERT INTO `Services` (`ID`, `GroupID`, `UserID`, `ServicesGroupID`, `Name`, `NameShort`, `Code`, `Item`, `Measure`, `ConsiderTypeID`, `CostOn`, `Cost`, `IsHidden`, `IsProtected`, `IsActive`, `IsProlong`, `SortID`,`Params`) VALUES
(53000, 2000000, 1, 1100, 'Прокси-сервер', 'Прокси', 'Proxy', 'Прокси-сервер', 'дн.', 'Daily', 0.00, 0.00, 'no', 'yes', 'yes', 'yes', 53000,'');




DROP TABLE IF EXISTS `ProxySchemes`;
-- SEPARATOR
CREATE TABLE `ProxySchemes` (
	`ID` int(11) NOT NULL auto_increment,
	`CreateDate` int(11) default '0',
	`GroupID` int(11) NOT NULL,
	`UserID` int(11) NOT NULL,
	`Name` char(30) default '',
	`PackageID` char(30) default '',
	`CostDay` decimal(11,2) default '0.00',
	`CostMonth` decimal(11,2) default '0.00',
	`Discount` DOUBLE NOT NULL DEFAULT '-1',
	`ServersGroupID` int(11) NOT NULL,
	`HardServerID` int(11) NULL,
	`Comment` char(255) default '',
	`IsActive` enum('no','yes') default 'yes',
	`IsProlong` enum('no','yes') default 'yes',
	`IsSchemeChangeable` enum('no','yes') default 'yes',
	`IsSchemeChange` enum('no','yes') default 'yes',
	`MinDaysPay` int(6) default '0',			/* минимальное число дней первой оплаты */
	`MinDaysProlong` INT(6) default '0',			/* минимальное число дней продления, для ранее оплаченных заказов */
	`MaxDaysPay` int(6) default '0',			/* максимальное число дней оплаты заказа */
	`MaxOrders` int(6) DEFAULT '0',				/* максимальное число заказов по этому тарифу, на одного пользователя */
	`MinOrdersPeriod` int(6) DEFAULT '0',			/* минимальный период между закзаами */
	--
	-- Common
	--
	`IPtype` char(12) default 'IPv4',			/* тип заказываемого прокси сервера IPv6,IPv4,IPv4shared */
	`Country` char(3) default 'ru',				-- страна
	`SortID` int(11) default '10',

	PRIMARY KEY  (`ID`),
	KEY `ProxySchemesGroupID` (`GroupID`),
	CONSTRAINT `ProxySchemesGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `ProxySchemesUserID` (`UserID`),
	CONSTRAINT `ProxySchemesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `ProxySchemesServersGroupID` (`ServersGroupID`),
	CONSTRAINT `ProxySchemesServersGroupID` FOREIGN KEY (`ServersGroupID`) REFERENCES `ServersGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `ProxySchemesHardServerID` (`HardServerID`),
	CONSTRAINT `ProxySchemesHardServerID` FOREIGN KEY (`HardServerID`) REFERENCES `Servers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;





-- SEPARATOR
--
-- Table structure for table `ProxyOrders`
--

DROP TABLE IF EXISTS `ProxyOrders`;
-- SEPARATOR
CREATE TABLE `ProxyOrders` (
	`ID` int(11) NOT NULL auto_increment,
	`OrderID` int(11) NOT NULL,
	`SchemeID` int(11) NOT NULL,
	`OldSchemeID` int(11) default NULL,
	`Login` char(20) default '',		-- логин
	`Password` char(64) default '',		-- пароль
	`IP` char(64) default '0.0.0.0',	-- IP адрес прокси (с которого выходит в инет)
	`Host` char(64) default '0.0.0.0',	-- IP адрес с которому надлежит коннектится клиенту
	`Port` int(5) default '0',		-- порт прокси
	`ProtocolType` char(9) default 'Https',	-- тип протокола прокси: SOCK5, HTTPS
	`ConsiderDay` int(11) default '0',
	`StatusID` char(30) default 'UnSeted',
	`StatusDate` int(11) default '0',
	PRIMARY KEY  (`ID`),
	KEY `ProxyOrdersOrderID` (`OrderID`),
	CONSTRAINT `ProxyOrdersOrderID` FOREIGN KEY (`OrderID`) REFERENCES `Orders` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
	KEY `ProxyOrdersSchemeID` (`SchemeID`),
	CONSTRAINT `ProxyOrdersSchemeID` FOREIGN KEY (`SchemeID`) REFERENCES `ProxySchemes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


