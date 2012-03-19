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

('DomainsOrdersRead','hosting',1,1,4000000,1,1,'yes'),
('DomainsOrdersRead','hosting',3000000,100,1,1,2,'yes'),

('DomainsOrdersPay','hosting',1,1,4000000,1,1,'yes'),
('DomainsOrdersPay','hosting',3000000,100,1,1,2,'yes'),

('DomainsOrdersNsChange','hosting',1,1,4000000,1,1,'yes'),
('DomainsOrdersNsChange','hosting',3000000,100,1,1,2,'yes'),

('DomainsOrdersConsider','hosting',1,1,4000000,1,1,'no'),
('DomainsOrdersConsider','hosting',3000000,100,1,1,2,'yes'),

/* ----------------------------- */

('HostingOrdersStatusSet','hosting',1,1,4000000,1,1,'no'),
('HostingOrdersStatusSet','hosting',3000000,100,1,1,2,'yes'),

('DomainsOrdersStatusSet','hosting',1,1,4000000,1,1,'no'),
('DomainsOrdersStatusSet','hosting',3000000,100,1,1,2,'yes'),

/* ----------------------------- */

('HostingOrdersDelete','hosting',1,1,4000000,1,1,'yes'),
('HostingOrdersDelete','hosting',3000000,100,1,1,2,'yes'),

('DomainsOrdersDelete','hosting',1,1,4000000,1,1,'yes'),
('DomainsOrdersDelete','hosting',3000000,100,1,1,2,'yes'),

('HostingSchemesDelete','hosting',1,1,4000000,1,1,'no'),
('HostingSchemesDelete','hosting',3000000,100,1,1,2,'yes'),

('DomainsSchemesDelete','hosting',1,1,4000000,1,1,'no'),
('DomainsSchemesDelete','hosting',3000000,100,1,1,2,'yes'),

('HostingBonusesDelete','hosting',1,1,4000000,1,1,'no'),
('HostingBonusesDelete','hosting',3000000,100,1,1,2,'yes'),

('DomainsBonusesDelete','hosting',1,1,4000000,1,1,'no'),
('DomainsBonusesDelete','hosting',3000000,100,1,1,2,'yes'),

('HostingPoliticsDelete','hosting',1,1,4000000,1,1,'no'),
('HostingPoliticsDelete','hosting',3000000,100,1,1,2,'yes'),

('HostingServersGroupsDelete','hosting',1,1,4000000,1,1,'no'),
('HostingServersGroupsDelete','hosting',3000000,100,1,1,2,'yes'),

('HostingServersDelete','hosting',1,1,4000000,1,1,'no'),
('HostingServersDelete','hosting',3000000,100,1,1,2,'yes'),

('RegistratorsDelete','hosting',1,1,4000000,1,1,'no'),
('RegistratorsDelete','hosting',3000000,100,1,1,2,'yes'),

('DomainsSchemesGroupsDelete','hosting',1,1,4000000,1,1,'no'),
('DomainsSchemesGroupsDelete','hosting',3000000,100,1,1,2,'yes'),

('DomainsSchemesGroupsItemsDelete','hosting',1,1,4000000,1,1,'no'),
('DomainsSchemesGroupsItemsDelete','hosting',3000000,100,1,1,2,'yes'),

('HostingDomainsPoliticsDelete','hosting',1,1,4000000,1,1,'no'),
('HostingDomainsPoliticsDelete','hosting',3000000,100,1,1,2,'yes'),

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

('VPSBonusesDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('VPSBonusesDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('VPSPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSServersGroupsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('VPSServersGroupsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSServersDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('VPSServersDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('VPSDomainsPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('VPSDomainsPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

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

('DSBonusesDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSBonusesDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSServersGroupsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSServersGroupsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSServersDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSServersDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('DSDomainsPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('DSDomainsPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

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

('ExtraIPBonusesDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPBonusesDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPsGroupsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPsGroupsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ExtraIPDomainsPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ExtraIPDomainsPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

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

('ISPswBonusesDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswBonusesDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswProducerDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswProducerDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswGroupsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswGroupsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswDomainsPoliticsDelete', 'hosting', 1, 1, 4000000, 1, 1, 'no'),
('ISPswDomainsPoliticsDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

('ISPswLicensesDelete', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ISPswLicensesDelete', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),


/* values for autoprolongation added by lissyara 2012-01-11 in 10:26 MSK */
('ServiceAutoProlongation', 'hosting', 1, 1, 4000000, 1, 1, 'yes'),
('ServiceAutoProlongation', 'hosting', 3000000, 100, 1, 1, 2, 'yes'),

/* added by lissyara, for JBS-353, 2012-03-19 in 16:30 MSK */
('DomainsOrdersChangeContactData','hosting',1,1,4000000,1,1,'yes'),
('DomainsOrdersChangeContactData','hosting',3000000,100,1,1,2,'yes')

;
UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS=1;
