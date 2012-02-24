UPDATE `Clauses` SET `Text` = REPLACE(`Text`,'/2Checkout','/Checkout');
-- SEPARATOR
UPDATE `Invoices` SET `PaymentSystemID` = 'Checkout' WHERE `PaymentSystemID` = '2Checkout';