SET FOREIGN_KEY_CHECKS=0;

DELETE FROM `Permissions` WHERE `HostID` = 'hosting';
LOCK TABLES `Permissions` WRITE;
INSERT INTO `Permissions`
  (`Name`,`HostID`,`UserGroupID`,`UserID`,`OwnerGroupID`,`OwnerID`,`Metric`,`IsAccess`)
VALUES

('HostingOrdersRead','hosting',1,1,4000000,1,1,'yes'),
('HostingOrdersRead','hosting',3000000,100,1,1,2,'yes'),

('HostingOrdersPay','hosting',1,1,4000000,1,1,'yes'),
('HostingOrdersPay','hosting',3000000,100,1,1,2,'yes'),

('HostingOrdersSchemeChange','hosting',1,1,4000000,1,1,'yes'),
('HostingOrdersSchemeChange','hosting',3000000,100,1,1,2,'yes'),

('HostingManage','hosting',1,1,4000000,1,1,'yes'),
('HostingManage','hosting',3000000,100,1,1,2,'yes'),

('HostingOrdersConsider','hosting',1,1,4000000,1,1,'no'),
('HostingOrdersConsider','hosting',3000000,100,1,1,2,'yes'),

('DomainOrdersRead','hosting',1,1,4000000,1,1,'yes'),
('DomainOrdersRead','hosting',3000000,100,1,1,2,'yes'),

('DomainOrdersPay','hosting',1,1,4000000,1,1,'yes'),
('DomainOrdersPay','hosting',3000000,100,1,1,2,'yes'),

('DomainOrdersNsChange','hosting',1,1,4000000,1,1,'yes'),
('DomainOrdersNsChange','hosting',3000000,100,1,1,2,'yes'),

('DomainOrdersConsider','hosting',1,1,4000000,1,1,'no'),
('DomainOrdersConsider','hosting',3000000,100,1,1,2,'yes'),

/* ----------------------------- */

('HostingOrdersStatusSet','hosting',1,1,4000000,1,1,'no'),
('HostingOrdersStatusSet','hosting',3000000,100,1,1,2,'yes'),

('DomainOrdersStatusSet','hosting',1,1,4000000,1,1,'no'),
('DomainOrdersStatusSet','hosting',3000000,100,1,1,2,'yes'),

/* ----------------------------- */

('HostingOrdersDelete','hosting',1,1,4000000,1,1,'yes'),
('HostingOrdersDelete','hosting',3000000,100,1,1,2,'yes'),

('DomainOrdersDelete','hosting',1,1,4000000,1,1,'yes'),
('DomainOrdersDelete','hosting',3000000,100,1,1,2,'yes'),

('HostingSchemesDelete','hosting',1,1,4000000,1,1,'no'),
('HostingSchemesDelete','hosting',3000000,100,1,1,2,'yes'),

('DomainSchemesDelete','hosting',1,1,4000000,1,1,'no'),
('DomainSchemesDelete','hosting',3000000,100,1,1,2,'yes'),

('DomainBonusesDelete','hosting',1,1,4000000,1,1,'no'),
('DomainBonusesDelete','hosting',3000000,100,1,1,2,'yes'),

('RegistratorsDelete','hosting',1,1,4000000,1,1,'no'),
('RegistratorsDelete','hosting',3000000,100,1,1,2,'yes'),

('DomainSchemesGroupsDelete','hosting',1,1,4000000,1,1,'no'),
('DomainSchemesGroupsDelete','hosting',3000000,100,1,1,2,'yes'),

('DomainSchemesGroupsItemsDelete','hosting',1,1,4000000,1,1,'no'),
('DomainSchemesGroupsItemsDelete','hosting',3000000,100,1,1,2,'yes'),

('HostingDomainPoliticsDelete','hosting',1,1,4000000,1,1,'no'),
('HostingDomainPoliticsDelete','hosting',3000000,100,1,1,2,'yes'),


/* values for VPS added by lissyara 2014-12-24 in 12:58 MSK */
('DNSmanagerOrdersRead','hosting',1,1,4000000,1,1,'yes'),
('DNSmanagerOrdersRead','hosting',3000000,100,1,1,2,'yes'),

('DNSmanagerOrdersPay','hosting',1,1,4000000,1,1,'yes'),
('DNSmanagerOrdersPay','hosting',3000000,100,1,1,2,'yes'),

('DNSmanagerOrdersSchemeChange','hosting',1,1,4000000,1,1,'yes'),
('DNSmanagerOrdersSchemeChange','hosting',3000000,100,1,1,2,'yes'),

('DNSmanagerManage','hosting',1,1,4000000,1,1,'yes'),
('DNSmanagerManage','hosting',3000000,100,1,1,2,'yes'),

('DNSmanagerOrdersConsider','hosting',1,1,4000000,1,1,'no'),
('DNSmanagerOrdersConsider','hosting',3000000,100,1,1,2,'yes'),

('DNSmanagerOrdersStatusSet','hosting',1,1,4000000,1,1,'no'),
('DNSmanagerOrdersStatusSet','hosting',3000000,100,1,1,2,'yes'),

('DomainOrdersStatusSet','hosting',1,1,4000000,1,1,'no'),
('DomainOrdersStatusSet','hosting',3000000,100,1,1,2,'yes'),

('DNSmanagerOrdersDelete','hosting',1,1,4000000,1,1,'yes'),
('DNSmanagerOrdersDelete','hosting',3000000,100,1,1,2,'yes'),

('DNSmanagerSchemesDelete','hosting',1,1,4000000,1,1,'no'),
('DNSmanagerSchemesDelete','hosting',3000000,100,1,1,2,'yes'),

('DNSmanagerDomainPoliticsDelete','hosting',1,1,4000000,1,1,'no'),
('DNSmanagerDomainPoliticsDelete','hosting',3000000,100,1,1,2,'yes'),


/* values for VPS added by lissyara 2011-06-14 in 21:42 MSK */
('VPSOrdersRead', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('VPSOrdersRead', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSOrdersPay', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('VPSOrdersPay', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSOrdersSchemeChange', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('VPSOrdersSchemeChange', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSManage', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('VPSManage', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSOrdersConsider', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('VPSOrdersConsider', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSOrdersStatusSet', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('VPSOrdersStatusSet', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSOrdersDelete', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('VPSOrdersDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSSchemesDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('VPSSchemesDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

/* values for DS added by lissyara 2011-06-14 in 21:42 MSK */
('DSOrdersRead', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('DSOrdersRead', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSOrdersPay', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('DSOrdersPay', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSOrdersSchemeChange', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('DSOrdersSchemeChange', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSManage', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('DSManage', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSOrdersConsider', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSOrdersConsider', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSOrdersStatusSet', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSOrdersStatusSet', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSOrdersDelete', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('DSOrdersDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSSchemesDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSSchemesDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSServersGroupsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSServersGroupsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSServersDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSServersDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSDomainPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSDomainPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

/* show or not show additional fields for server information screen */
('DSAdditionalInfo', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSAdditionalInfo', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

/* values for AdminNote added by lissyara 2011-07-06 in 17:37 MSK */
('AdminNote', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('AdminNote', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

/* values for ExtraIP added by lissyara 2011-08-04 in 15:36 MSK */
('ExtraIPOrdersRead', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ExtraIPOrdersRead', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPOrdersPay', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ExtraIPOrdersPay', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPOrdersSchemeChange', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ExtraIPOrdersSchemeChange', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPManage', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ExtraIPManage', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPOrdersConsider', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPOrdersConsider', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPOrdersStatusSet', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPOrdersStatusSet', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPOrdersDelete', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ExtraIPOrdersDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPSchemesDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPSchemesDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPsGroupsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPsGroupsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPDomainPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPDomainPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

/* values for ISPsw added by lissyara 2011-09-06 in 15:26 MSK */
('ISPswOrdersRead', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ISPswOrdersRead', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswOrdersPay', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ISPswOrdersPay', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswOrdersSchemeChange', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ISPswOrdersSchemeChange', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswManage', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ISPswManage', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswOrdersConsider', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswOrdersConsider', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswOrdersStatusSet', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswOrdersStatusSet', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswOrdersDelete', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ISPswOrdersDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswSchemesDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswSchemesDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswProducerDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswProducerDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswGroupsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswGroupsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswDomainPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswDomainPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswLicensesDelete', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ISPswLicensesDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),


/* values for autoprolongation added by lissyara 2012-01-11 in 10:26 MSK */
('ServiceAutoProlongation', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ServiceAutoProlongation', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

/* added by lissyara, for JBS-353, 2012-03-19 in 16:30 MSK */
('DomainOrdersChangeContactData','hosting',1,1,4000000,1,1,'yes'),
('DomainOrdersChangeContactData','hosting',3000000,100,1,1,2,'yes'),

/* added by lissyara, for JBS-422 */
('ServersGroupsDelete','hosting',1,1,4000000,1,1,'no'),
('ServersGroupsDelete','hosting',3000000,100,1,1,2,'yes'),
('ServersDelete','hosting',1,1,4000000,1,1,'no'),
('ServersDelete','hosting',3000000,100,1,1,2,'yes'),

/* added by lissyara 2015-02-20 in 23:18 MSK, for JBS-953 */
('ServerRead','hosting',1,1,4000000,1,1,'no'),
('ServerRead','hosting',3000000,100,1,1,2,'yes'),
('ServerManage','hosting',1,1,4000000,1,1,'no'),
('ServerManage','hosting',3000000,100,1,1,2,'yes')

;

UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS=1;

