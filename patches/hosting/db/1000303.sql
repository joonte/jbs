
ALTER TABLE `DomainSchemes` CHANGE `CostOrder` `CostOrder` DECIMAL(11,2) NULL DEFAULT '0.00';
-- SEPARATOR
ALTER TABLE `DomainSchemes` CHANGE `CostProlong` `CostProlong` DECIMAL(11,2) NULL DEFAULT '0.00';
-- SEPARATOR
ALTER TABLE `DomainSchemes` CHANGE `CostTransfer` `CostTransfer` DECIMAL(11,2) NULL DEFAULT '0.00';

