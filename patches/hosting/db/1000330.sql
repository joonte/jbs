
DROP VIEW IF EXISTS `ProxyOrdersOwners`;

-- SEPARATOR

DELETE FROM `Services` WHERE `ID` = 53000;

-- SEPARATOR

DELETE FROM `Tasks` WHERE `ID` IN (95);

-- SEPARATOR

DELETE FROM `Clauses` WHERE `Partition` = 'Contracts/Enclosures/Types/ProxyRules/Content';

-- SEPARATOR

DELETE FROM `Clauses` WHERE `Partition` = 'Header:/ProxyOrders';

-- SEPARATOR

DROP TRIGGER IF EXISTS `UpdateProxyStatus`;

-- SEPARATOR

DROP TRIGGER IF EXISTS `ProxyOrdersOnDelete`;

-- SEPARATOR

DROP TRIGGER IF EXISTS `ProxySchemesOnInsert`;

-- SEPARATOR

DROP TABLE IF EXISTS `ProxyOrders`;

-- SEPARATOR

DROP TABLE IF EXISTS `ProxySchemes`;


