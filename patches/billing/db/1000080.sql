UPDATE `Users` SET `Email` = CONCAT(`Login`,'@',`Login`,'.ru') WHERE `Email` = '';
ALTER TABLE `Users` DROP `Login`;