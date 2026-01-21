
ALTER TABLE `HostingOrders` CHANGE `Password` `Password` char(255) default '';

-- SEPARATOR

ALTER TABLE `DNSmanagerOrders` CHANGE `Password` `Password` char(255) default '';

-- SEPARATOR

ALTER TABLE `VPSOrders` CHANGE `Password` `Password` char(255) default '';

-- SEPARATOR

ALTER TABLE `ExtraIPOrders` CHANGE `Password` `Password` char(255) default '';

-- SEPARATOR

ALTER TABLE `DSSchemes` CHANGE `DSpass` `DSpass` char(255) default '';

-- SEPARATOR

ALTER TABLE `DSSchemes` CHANGE `ILOpass` `ILOpass` char(255) default '';




