SET FOREIGN_KEY_CHECKS=0;

DELETE FROM `Permissions` WHERE `HostID` = 'billing';
LOCK TABLES `Permissions` WRITE;
INSERT INTO `Permissions` (`Name`,`HostID`,`UserGroupID`,`UserID`,`OwnerGroupID`,`OwnerID`,`Metric`,`IsAccess`) VALUES

/* ----------------------------- */

('/%','billing',1,1,4000000,1,1,'yes'),

/* ----------------------------- */

('%/Administrator/%','billing',1,1,4000000,1,1,'no'),
('%/Administrator/%','billing',3000000,100,4000000,1,2,'yes'),

/* ----------------------------- */

('ClauseEdit','billing',1,1,4000000,1,1,'no'),
('ClauseEdit','billing',3000000,100,1,1,2,'yes'),

('UserRead','billing',1,1,4000000,1,1,'yes'),
('UserRead','billing',3000000,100,1,1,2,'yes'),

('UserSwitch','billing',1,1,4000000,1,1,'yes'),
('UserSwitch','billing',3000000,100,1,1,2,'yes'),

('ProfileRead','billing',1,1,4000000,1,1,'yes'),
('ProfileRead','billing',3000000,100,1,1,2,'yes'),

('ProfileEdit','billing',1,1,4000000,1,1,'yes'),
('ProfileEdit','billing',3000000,100,1,1,2,'yes'),

('ContractRead','billing',1,1,4000000,1,1,'yes'),
('ContractRead','billing',3000000,100,1,1,2,'yes'),

('ContractEnclosureRead','billing',1,1,4000000,1,1,'yes'),
('ContractEnclosureRead','billing',3000000,100,1,1,2,'yes'),

('InvoiceRead','billing',1,1,4000000,1,1,'yes'),
('InvoiceRead','billing',3000000,100,1,1,2,'yes'),

('InvoiceEdit','billing',1,1,4000000,1,1,'yes'),
('InvoiceEdit','billing',3000000,100,1,1,2,'yes'),

('WorksCompliteRead','billing',1,1,4000000,1,1,'yes'),
('WorksCompliteRead','billing',3000000,100,1,1,2,'yes'),

('EdeskRead','billing',1,1,1,1,1,'yes'),
('EdeskRead','billing',3000000,100,1,1,2,'yes'),

('TicketRead','billing',1,1,4000000,1,1,'yes'),
('TicketRead','billing',3000000,100,1,1,2,'yes'),

('TicketEdit','billing',1,1,4000000,1,1,'yes'),
('TicketEdit','billing',3000000,100,1,1,2,'yes'),

('TaskRead','billing',1,1,4000000,1,1,'yes'),
('TaskRead','billing',3000000,100,1,1,2,'yes'),

('MotionDocumentRead','billing',1,1,4000000,1,1,'yes'),
('MotionDocumentRead','billing',3000000,100,1,1,2,'yes'),

('ServiceOrderRead','billing',1,1,4000000,1,1,'yes'),
('ServiceOrderRead','billing',3000000,100,1,1,2,'yes'),

('OrdersFieldsRead','billing',1,1,4000000,1,1,'yes'),
('OrdersFieldsRead','billing',3000000,100,1,1,2,'yes'),

('ServicesOrdersPay','billing',1,1,4000000,1,1,'yes'),
('ServicesOrdersPay','billing',3000000,100,1,1,2,'yes'),

/* ----------------------------- */

('StatusesHistory','billing',1,1,4000000,1,1,'yes'),
('StatusesHistory','billing',3000000,100,1,1,2,'yes'),

/* ----------------------------- */

('ProfilesStatusSet','billing',1,1,4000000,1,1,'no'),
('ProfilesStatusSet','billing',3000000,100,1,1,2,'yes'),

('ContractsStatusSet','billing',1,1,4000000,1,1,'no'),
('ContractsStatusSet','billing',3000000,100,1,1,2,'yes'),

('ContractsEnclosuresStatusSet','billing',1,1,4000000,1,1,'no'),
('ContractsEnclosuresStatusSet','billing',3000000,100,1,1,2,'yes'),

('InvoicesStatusSet','billing',1,1,4000000,1,1,'no'),
('InvoicesStatusSet','billing',3000000,100,1,1,2,'yes'),

('MotionDocumentsStatusSet','billing',1,1,4000000,1,1,'yes'),
('MotionDocumentsStatusSet','billing',3000000,100,1,1,2,'yes'),

('EdesksStatusSet','billing',1,1,4000000,1,1,'yes'),
('EdesksStatusSet','billing',3000000,100,1,1,2,'yes'),

('OrdersStatusSet','billing',1,1,4000000,1,1,'no'),
('OrdersStatusSet','billing',3000000,100,1,1,2,'yes'),

/* ----------------------------- */

('GroupsDelete','billing',1,1,4000000,1,1,'no'),
('GroupsDelete','billing',3000000,100,1,1,2,'yes'),

('UsersDelete','billing',1,1,4000000,1,1,'no'),
('UsersDelete','billing',3000000,100,1,1,2,'yes'),

('ProfilesDelete','billing',1,1,4000000,1,1,'yes'),
('ProfilesDelete','billing',3000000,100,1,1,2,'yes'),

('ContractsDelete','billing',1,1,4000000,1,1,'yes'),
('ContractsDelete','billing',3000000,100,1,1,2,'yes'),

('ContractsEnclosuresDelete','billing',1,1,4000000,1,1,'yes'),
('ContractsEnclosuresDelete','billing',3000000,100,1,1,2,'yes'),

('InvoicesDelete','billing',1,1,4000000,1,1,'no'),
('InvoicesDelete','billing',3000000,100,1,1,2,'yes'),

('PostingsDelete','billing',1,1,4000000,1,1,'no'),
('PostingsDelete','billing',3000000,100,1,1,2,'yes'),

('WorksCompliteDelete','billing',1,1,4000000,1,1,'no'),
('WorksCompliteDelete','billing',3000000,100,1,1,2,'yes'),

('MotionDocumentsDelete','billing',1,1,4000000,1,1,'no'),
('MotionDocumentsDelete','billing',3000000,100,1,1,2,'yes'),

('EdesksDelete','billing',1,1,4000000,1,1,'no'),
('EdesksDelete','billing',3000000,100,1,1,2,'yes'),

('TasksDelete','billing',1,1,4000000,1,1,'no'),
('TasksDelete','billing',3000000,100,1,1,2,'yes'),

('ClausesDelete','billing',1,1,4000000,1,1,'yes'),
('ClausesDelete','billing',3000000,100,1,1,2,'yes'),

('OrdersDelete','billing',1,1,4000000,1,1,'yes'),
('OrdersDelete','billing',3000000,100,1,1,2,'yes'),

('BasketDelete','billing',1,1,4000000,1,1,'yes'),
('BasketDelete','billing',3000000,100,1,1,2,'yes'),

('ServicesGroupsDelete','billing',1,1,4000000,1,1,'no'),
('ServicesGroupsDelete','billing',3000000,100,1,1,2,'yes'),

('ServicesDelete','billing',1,1,4000000,1,1,'no'),
('ServicesDelete','billing',3000000,100,1,1,2,'yes'),

('ServicesFieldsDelete','billing',1,1,4000000,1,1,'no'),
('ServicesFieldsDelete','billing',3000000,100,1,1,2,'yes');

UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS=1;
