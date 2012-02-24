INSERT INTO `Clauses` (`ID`, `PublicDate`, `ChangedDate`, `AuthorID`, `EditorID`, `Partition`, `Title`, `IsProtected`, `IsXML`, `IsDOM`, `Text`, `IsPublish`) VALUES (NULL, UNIX_TIMESTAMP(), '0', '100', '100', 'Invoices/PaymentSystems/InterKassa', 'Шаблон платежной системы ИнтерКасса', 'no', 'yes', 'yes', '<NOBODY>
 <H1>
 СЧЕТ №%Invoice.Number% от %Invoice.CreateDate%
</H1>
 <DIV id="Services">
 [список услуг]
</DIV>
 <H2>
 Платежное поручение
</H2>
 <TABLE border="1" cellpadding="5" cellspacing="0">
  <TBODY>
   <TR bgcolor="#DCDCDC">
    <TD align="center">
    Назначение
   </TD>
    <TD align="center">
    Идентификатор магазина
   </TD>
    <TD align="center">
    Сумма
   </TD>
   </TR>
   <TR>
    <TD>
    За web-услуги по счету №%Invoice.Number%
   </TD>
    <TD align="right">
    %PaymentSystem.Send.ik_shop_id%
   </TD>
    <TD align="right">
    %Invoice.Foreign% %PaymentSystem.Measure%
   </TD>
   </TR>
  </TBODY>
 </TABLE>
</NOBODY>
', 'yes');
