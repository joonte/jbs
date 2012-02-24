
ALTER TABLE `HostingOrders` CHANGE `Password` `Password` CHAR( 64 ) NOT NULL ;
-- SEPARATOR
ALTER TABLE `VPSOrders` CHANGE `Password` `Password` CHAR( 64 ) NOT NULL ;

