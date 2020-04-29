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

('ClausesEdit','billing',1,1,4000000,1,1,'no'),
('ClausesEdit','billing',3000000,100,1,1,2,'yes'),

('ClausesRead','billing',1,1,4000000,1,1,'no'),
('ClausesRead','billing',3000000,100,1,1,2,'yes'),

('UsersRead','billing',1,1,4000000,1,1,'yes'),
('UsersRead','billing',3000000,100,1,1,2,'yes'),

('UsersSwitch','billing',1,1,4000000,1,1,'yes'),
('UsersSwitch','billing',3000000,100,1,1,2,'yes'),

('ProfilesRead','billing',1,1,4000000,1,1,'yes'),
('ProfilesRead','billing',3000000,100,1,1,2,'yes'),

('ProfilesEdit','billing',1,1,4000000,1,1,'yes'),
('ProfilesEdit','billing',3000000,100,1,1,2,'yes'),

('ContractsRead','billing',1,1,4000000,1,1,'yes'),
('ContractsRead','billing',3000000,100,1,1,2,'yes'),

('ContractEnclosureRead','billing',1,1,4000000,1,1,'yes'),
('ContractEnclosureRead','billing',3000000,100,1,1,2,'yes'),

('InvoicesRead','billing',1,1,4000000,1,1,'yes'),
('InvoicesRead','billing',3000000,100,1,1,2,'yes'),

('InvoicesEdit','billing',1,1,4000000,1,1,'yes'),
('InvoicesEdit','billing',3000000,100,1,1,2,'yes'),

('WorksCompliteRead','billing',1,1,4000000,1,1,'yes'),
('WorksCompliteRead','billing',3000000,100,1,1,2,'yes'),

('EdeskRead','billing',1,1,1,1,1,'yes'),
('EdeskRead','billing',3000000,100,1,1,2,'yes'),

('EdesksMessagesEdit','billing',1,1,1,1,1,'yes'),
('EdesksMessagesEdit','billing',3000000,100,1,1,2,'yes'),

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

('EdesksMessagesDelete','billing',1,1,4000000,1,1,'no'),
('EdesksMessagesDelete','billing',3000000,100,1,1,2,'yes'),

('TasksDelete','billing',1,1,4000000,1,1,'no'),
('TasksDelete','billing',3000000,100,1,1,2,'yes'),

('ClausesDelete','billing',1,1,4000000,1,1,'yes'),
('ClausesDelete','billing',3000000,100,1,1,2,'yes'),

/* added by lissyara, 2013-05-15 in 13:51 for JBS-671 */
('ClausesGroupsDelete','billing',1,1,4000000,1,1,'yes'),
('ClausesGroupsDelete','billing',3000000,100,1,1,2,'yes'),

('OrdersDelete','billing',1,1,4000000,1,1,'yes'),
('OrdersDelete','billing',3000000,100,1,1,2,'yes'),

('BasketDelete','billing',1,1,4000000,1,1,'yes'),
('BasketDelete','billing',3000000,100,1,1,2,'yes'),

('ServicesGroupsDelete','billing',1,1,4000000,1,1,'no'),
('ServicesGroupsDelete','billing',3000000,100,1,1,2,'yes'),

('ServicesDelete','billing',1,1,4000000,1,1,'no'),
('ServicesDelete','billing',3000000,100,1,1,2,'yes'),

('ServicesEdit','billing',1,1,4000000,1,1,'no'),
('ServicesEdit','billing',3000000,100,1,1,2,'yes'),

('ServicesFieldsDelete','billing',1,1,4000000,1,1,'no'),
('ServicesFieldsDelete','billing',3000000,100,1,1,2,'yes'),

/* added by lissyara, 2012-10-03 in 21:57 for JBS-491 */
('EdeskClose','billing',1,1,1,1,1,'yes'),
('EdeskClose','billing',3000000,100,1,1,2,'yes'),

/* added by lissyara, 2012-11-02 in 15:30 for JBS-157 / JBS-158 */
('SchemesGroupsDelete','billing',1,1,4000000,1,1,'no'),
('SchemesGroupsDelete','billing',3000000,100,1,1,2,'yes'),

('SchemesGroupsItemsDelete','billing',1,1,4000000,1,1,'no'),
('SchemesGroupsItemsDelete','billing',3000000,100,1,1,2,'yes'),

('PoliticsDelete','billing',1,1,4000000,1,1,'no'),
('PoliticsDelete','billing',3000000,100,1,1,2,'yes'),

('BonusesDelete','billing',1,1,4000000,1,1,'no'),
('BonusesDelete','billing',3000000,100,1,1,2,'yes'),

/* added by lissyara, 2012-11-05 in 15:43 MSK, for JBS-15 */
('PromoCodesDelete','billing',1,1,4000000,1,1,'no'),
('PromoCodesDelete','billing',3000000,100,1,1,2,'yes'),

/* added by lissyara, 2012-11-19 in 22:21 MSK, for JBS-15 */
('PromoCodesExtinguishedDelete','billing',1,1,4000000,1,1,'no'),
('PromoCodesExtinguishedDelete','billing',3000000,100,1,1,2,'yes'),

/* added by lissyara, 2012-11-21 in 14:41 MSK, for JBS-565 */
('ContractConsiderEdit','billing',1,1,4000000,1,1,'yes'),
('ContractConsiderEdit','billing',3000000,100,1,1,2,'yes'),

/* added by lissyara, for JBS-600, 2013-02-11 in 15:31 MSK  */
('OrdersTransfer', 'billing', 1, 1, 4000000, 1, 1, 'yes'),
('OrdersTransfer', 'billing', 3000000, 100, 1, 1, 2, 'yes'),

/* added by lissyara, for  JBS-1032, 2015-04-07 in 13:03 MSK  */
('PaymentSystemsCollationDelete','billing',1,1,4000000,1,1,'no'),
('PaymentSystemsCollationDelete','billing',3000000,100,1,1,2,'yes'),

/* added by lissyara, for  JBS-1125, 2019-09-04 in 20:10 MSK */
('ContactRead','billing',1,1,4000000,1,1,'yes'),
('ContactRead','billing',3000000,100,1,1,2,'yes'),
('ContactsDelete','billing',1,1,4000000,1,1,'yes'),
('ContactsDelete','billing',3000000,100,1,1,2,'yes'),

/* added by lissyara, 2020-04-29 in 19:21 MSK  */
('OrdersTransferDelete','billing',1,1,4000000,1,1,'no'),
('OrdersTransferDelete','billing',3000000,100,1,1,2,'yes')

;


UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS=1;
