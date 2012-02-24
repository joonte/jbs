UPDATE `Clauses` SET `Text` = REPLACE(`Text`,'/CO','/2Checkout');
-- SEPARATOR
UPDATE `Invoices` SET `PaymentSystemID` = '2Checkout' WHERE `PaymentSystemID` = 'CO';