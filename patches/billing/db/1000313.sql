UPDATE `DSOrders` SET `StatusID` = 'Active' WHERE `StatusID`='Frozen';
-- SEPARATOR
UPDATE `VPSOrders` SET `StatusID` = 'Active' WHERE `StatusID`='Frozen';
-- SEPARATOR
UPDATE `HostingOrders` SET `StatusID` = 'Active' WHERE `StatusID`='Frozen';

