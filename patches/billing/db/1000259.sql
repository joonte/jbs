ALTER TABLE `Profiles` ADD `Document` mediumblob AFTER `Attribs`;
-- SEPARATOR
ALTER TABLE `Profiles` ADD `Format` char(10) default 'jpg' AFTER `Document`;