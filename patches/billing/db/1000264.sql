set @Text = '<NOBODY>
 <H1>СЧЕТ №%Invoice.Number% от %Invoice.CreateDate%</H1>
 <DIV id="Services">[список услуг]</DIV>
 <H2>Платежное поручение</H2>
 <TABLE border="1" cellpadding="5" cellspacing="0">
  <TR bgcolor="#DCDCDC">
   <TD align="center">Назначение</TD>
   <TD align="center">Сумма</TD>
  </TR>
  <TR>
   <TD>За web-услуги по счету №%Invoice.Number%</TD>
   <TD align="right">%Invoice.Foreign% %PaymentSystem.Measure%</TD>
  </TR>
 </TABLE>
</NOBODY>';
-- SEPARATOR
INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','Invoices/PaymentSystems/CO','Шаблон платежной системы 2Checkout',@Text);
