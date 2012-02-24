UPDATE `HostingOrders` SET `DaysRemainded` = (SELECT SUM(`DaysRemainded`) FROM `HostingConsider` WHERE `HostingConsider`.`HostingOrderID` = `HostingOrders`.`ID` AND `HostingConsider`.`DaysRemainded` > 0)