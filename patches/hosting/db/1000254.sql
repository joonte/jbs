
-- SEPARATOR
/* общая таблица для бонусов. реализация JBS-157 */
DROP TABLE IF EXISTS `Bonuses`;
CREATE TABLE `Bonuses` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) default '0',	-- дата создания бонуса
  `ExpirationDate` int(11) default '0', -- дата окончания возможности заюзать бонус
  `UserID` int(11) NOT NULL,		-- для какого юзера этот бонус
  `ServiceID` int(11) NULL,		-- на какой сервис бонус
  `SchemeID` int(11) NULL,		-- идентификатор тарифа, на который даётся бонус
  `SchemesGroupID` int(11) NULL,   -- группа тарифов на которую даётся бонус
  `DaysReserved` int(11) default '0',	-- на сколько дней дан бонус
  `DaysRemainded` int(11) default '0',	-- сколько дней осталось от бонуса
  `Discont` float(11,2) default '0.00',	-- размер скидки, в долях от единицы
  `Comment` char(255) default '',	-- комментарий к бонусу
  PRIMARY KEY(`ID`),
  /* просто ключ, чтоб не перебирать всю таблицу при поиске */
  KEY `BonusesSchemeID` (`SchemeID`),
  /* внешний ключ на юзера */
  KEY `BonusesUserID` (`UserID`),
  CONSTRAINT `BonusesUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на сервис */
  KEY `BonusesServiceID` (`ServiceID`),
  CONSTRAINT `BonusesServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на группы тарифов */
  KEY `PoliticsSchemesGroupID` (`SchemesGroupID`),
  CONSTRAINT `PoliticsSchemesGroupID` FOREIGN KEY (`SchemesGroupID`) REFERENCES `SchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR

/* общая таблица для политик. реализация JBS-158 */
DROP TABLE IF EXISTS `Politics`;
CREATE TABLE `Politics` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` int(11) default '0',	-- дата создания политики
  `ExpirationDate` int(11) default '0', -- дата окончания действия политики
  `UserID` int(11) NOT NULL,		-- для какого юзера политика
  `GroupID` int(11) NOT NULL,		-- для какой группы юзеров эта политика
  `FromServiceID` int(11) NULL,		-- при заказе какого сервиса работает политика
  `FromSchemeID` int(11) NULL,		-- идентификатор тарифа, по которому срабатывает политика
  `FromSchemesGroupID` int(11) NULL,	-- какую группу услуг оплачивают
  `ToServiceID` int(11) NULL,		-- на какой сервис работает эта политика
  `ToSchemeID` int(11) NULL,		-- идентификатор тарифа, на который будет даваться скидка
  `ToSchemesGroupID` int(11) NULL,	-- на какую группу услуг будет даваться бонус
  `DaysPay` int(11) default '665',	-- какой срок надо оплатить, чтобы сработала политика
  `DaysDiscont` int(11) default '665',  -- на какой срок даётся скидка
  `Discont` float(11,2) default '0.00',	-- размер скидки, в долях от единицы
  `Comment` char(255) default '',       -- комментарий к политике
  PRIMARY KEY(`ID`),
  /* просто ключи для тарифов */
  KEY `PoliticsFromSchemeID` (`FromSchemeID`),
  KEY `ToPoliticsFromSchemeID` (`ToSchemeID`),
  /* внешний ключ на группы юзеров */
  KEY `PoliticsGroupID` (`GroupID`),
  CONSTRAINT `PoliticsGroupID` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на юзеров */
  KEY `PoliticsUserID` (`UserID`),
  CONSTRAINT `PoliticsUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на сервис, при заказе которого работает политика */
  KEY `PoliticsFromServiceID` (`FromServiceID`),
  CONSTRAINT `PoliticsFromServiceID` FOREIGN KEY (`FromServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на группы тарифов */
  KEY `PoliticsFromSchemesGroupID` (`FromSchemesGroupID`),
  CONSTRAINT `PoliticsFromSchemesGroupID` FOREIGN KEY (`FromSchemesGroupID`) REFERENCES `SchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на сервис, на который даётся бонус этой политикой */
  KEY `PoliticsToServiceID` (`ToServiceID`),
  CONSTRAINT `PoliticsToServiceID` FOREIGN KEY (`ToServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на группы тарифов */
  KEY `PoliticsToSchemesGroupID` (`ToSchemesGroupID`),
  CONSTRAINT `PoliticsToSchemesGroupID` FOREIGN KEY (`ToSchemesGroupID`) REFERENCES `SchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR

/* общая таблица для группировки тарифов - группы. реализация JBS-158 */
DROP TABLE IF EXISTS `SchemesGroups`;
CREATE TABLE `SchemesGroups` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` char(255) default '',
   PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR

/* общая таблица для группировки тарифов - элементы групп. реализация JBS-158 */
DROP TABLE IF EXISTS `SchemesGroupsItems`;
CREATE TABLE `SchemesGroupsItems` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SchemesGroupID` int(11) NOT NULL,
  `ServiceID` int(11) NULL,
  `SchemeID` int(11) NULL,
  PRIMARY KEY(`ID`),
  KEY `SchemesGroupsItemsSchemeID` (`SchemeID`),
  /* внешний ключ на группы тарифов */
  KEY `SchemesGroupsItemsSchemesGroupID` (`SchemesGroupID`),
  CONSTRAINT `SchemesGroupsItemsSchemesGroupID` FOREIGN KEY (`SchemesGroupID`) REFERENCES `SchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на сервис */
  KEY `SchemesGroupsItemsServiceID` (`ServiceID`),
  CONSTRAINT `SchemesGroupsItemsServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR

/* ПромоКоды, JBS-15 */
DROP TABLE IF EXISTS `PromoCodes`;
CREATE TABLE `PromoCodes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Code` char(32),				-- промокод
  `CreateDate` int(11) default '0',     	-- дата создания промокода
  `ExpirationDate` int(11) default '0', 	-- дата окончания действия промокода
  `ServiceID` int(11) NULL,			-- на какой сервис
  `SchemeID` int(11) NULL,			-- идентификатор тарифа
  `SchemesGroupID` int(11) NULL,		-- группа тарифов
  `DaysDiscont` int(11) default '665',		-- на какой срок создаётся бонус
  `Discont` float(11,2) default '0.00',		-- размер скидки, в долях от единицы
  `MaxAmount` int(11) default '0',		-- сколько раз можно ввести промокод
  `CurrentAmount` int(11) default '0',		-- сколько раз его уже вводили
  `OwnerID` int(11) NULL,			-- сделать того кто введёт партнёром этого юзера
  `ForceOwner` enum('no','yes') default 'no',	-- делать партнёром принудительно (если уже чей-то партнёр)
  `Comment` char(255) default '',		-- комментарий к промокоду
  PRIMARY KEY (`ID`),
  UNIQUE KEY (`Code`),
  /* внешний ключ на сервис */
  KEY `PromoCodesServiceID` (`ServiceID`),
  CONSTRAINT `PromoCodesServiceID` FOREIGN KEY (`ServiceID`) REFERENCES `Services` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на группы тарифов */
  KEY `PromoCodesSchemesGroupID` (`SchemesGroupID`),
  CONSTRAINT `PromoCodesSchemesGroupID` FOREIGN KEY (`SchemesGroupID`) REFERENCES `SchemesGroups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на таблицу юзеров */
  KEY `PromoCodesOwnerID` (`OwnerID`),
  CONSTRAINT `PromoCodesOwnerID` FOREIGN KEY (`OwnerID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SEPARATOR

/* активированные ПромоКоды */
DROP TABLE IF EXISTS `PromoCodesExtinguished`;
CREATE TABLE `PromoCodesExtinguished` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PromoCodeID` int(11) NOT NULL,		-- идентификатор погашенного промокода
  `UserID` int(11) NOT NULL,			-- какой юзер погасил промокод
  `CreateDate` int(11) default '0',		-- когда промокод был погашен
  /* уникальный ключ */
  PRIMARY KEY (`ID`),
  /* внешний ключ на юзеров */
  KEY `PromoCodesExtinguishedUserID` (`UserID`),
  CONSTRAINT `PromoCodesExtinguishedUserID` FOREIGN KEY (`UserID`) REFERENCES `Users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  /* внешний ключ на ПромоКоды */
  KEY `PromoCodesPromoCodeID` (`PromoCodeID`),
  CONSTRAINT `PromoCodesPromoCodeID` FOREIGN KEY (`PromoCodeID`) REFERENCES `PromoCodes` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


