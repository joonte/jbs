
ALTER TABLE `VPSSchemes` ADD `limitpvtdns` INT(6) NOT NULL AFTER `extns`, ADD `limitpubdns` INT(6) NOT NULL AFTER `limitpvtdns`;

