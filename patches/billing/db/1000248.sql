DELETE FROM `Clauses` WHERE `Partition` = 'Invoices/PaymentSystems/Juridical/Individual';
-- SEPARATOR
set @Text = '<NOBODY>
 <SPAN id="Logo" />
 <P>
  <SPAN>Распечатайте счет и образец платежного поручения в удобном для Вас формате. Обращаем Ваше внимание на то, что поступившие платежи обрабатываются автоматически. </SPAN>
  <B>Настоятельно рекомендуем Вам в точности повторить назначение платежа, указанное в образце.</B>
 </P>
 <H1>Образец платежного поручения</H1>
 <TABLE border="1" cellpadding="5" cellspacing="0">
  <TR>
   <TD>ИНН %Executor.Inn%</TD>
   <TD>КПП %Executor.Kpp%</TD>
   <TD rowspan="2" valign="bottom">Счет №</TD>
   <TD rowspan="2" valign="bottom">%Executor.BankAccount%</TD>
  </TR>
  <TR>
   <TD colspan="2" valign="bottom">
    <FONT size="1">Получатель</FONT>
    <BR />
    <SPAN>Индивидуальный предприниматель "%Executor.CompanyName%"</SPAN>
   </TD>
  </TR>
  <TR>
   <TD colspan="2" rowspan="2" valign="bottom">
    <FONT size="1">Банк получателя</FONT>
    <BR />
    <SPAN>%Executor.BankName%</SPAN>
   </TD>
   <TD>БИК</TD>
   <TD>%Executor.Bik%</TD>
  </TR>
  <TR>
   <TD>Счет №</TD>
   <TD>%Executor.Kor%</TD>
  </TR>
  <TR>
   <TD colspan="4" bgcolor="#FDF6D3">
    <FONT size="1">Назначение платежа</FONT>
    <BR />
    <SPAN>Предоплата за web-услуги по счету №%Invoice.Number% от %Invoice.CreateDate% согласно договору №%Contract.Number% от %Contract.CreateDate%</SPAN>
   </TD>
  </TR>
 </TABLE>
 <P>Плательщик: %Customer.CompanyForm% "%Customer.CompanyName%" ИНН: %Customer.Inn% КПП: %Customer.Kpp%</P>
 <H1>СЧЕТ №%Invoice.Number% от %Invoice.CreateDate%</H1>
 <DIV id="Services">[список услуг]</DIV>
 <P>
  <SPAN>Всего к оплате: </SPAN>
  <B>%Invoice.Wizard%</B>
 </P>
 <TABLE id="Rubbish" cellpadding="10">
  <TR>
   <TD>
    <SPAN>Руководитель предприятия</SPAN>
    <BR />
    <SPAN>Индивидуальный предприниматель "%Executor.CompanyName%"</SPAN>
    <BR />
    <SPAN>%Executor.dSourname% %Executor.dName% %Executor.dLastname%</SPAN>
   </TD>
   <TD id="Sign">__________________</TD>
   <TD id="Stamp" rowspan="2" />
  </TR>
  <TR>
   <TD>
    <SPAN>Главный бухгалтер</SPAN>
    <BR />
    <SPAN>%Executor.dSourname% %Executor.dName% %Executor.dLastname%</SPAN>
   </TD>
   <TD id="Sign">__________________</TD>
  </TR>
 </TABLE>
</NOBODY>';
-- SEPARATOR
INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','Invoices/PaymentSystems/Juridical/Individual','Шаблон платежной системы юридического лица исполнителя индивидуального предпринимателя',@Text);
-- SEPARATOR
DELETE FROM `Clauses` WHERE `Partition` = 'Invoices/PaymentSystems/Juridical/Juridical';
-- SEPARATOR
set @Text = '<NOBODY>
 <SPAN id="Logo" />
 <P>
  <SPAN>Распечатайте счет и образец платежного поручения в удобном для Вас формате. Обращаем Ваше внимание на то, что поступившие платежи обрабатываются автоматически. </SPAN>
  <B>Настоятельно рекомендуем Вам в точности повторить назначение платежа, указанное в образце.</B>
 </P>
 <H1>Образец платежного поручения</H1>
 <TABLE border="1" cellpadding="5" cellspacing="0">
  <TR>
   <TD>ИНН %Executor.Inn%</TD>
   <TD>КПП %Executor.Kpp%</TD>
   <TD rowspan="2" valign="bottom">Счет №</TD>
   <TD rowspan="2" valign="bottom">%Executor.BankAccount%</TD>
  </TR>
  <TR>
   <TD colspan="2" valign="bottom">
    <FONT size="1">Получатель</FONT>
    <BR />
    <SPAN>%Executor.CompanyForm% "%Executor.CompanyName%"</SPAN>
   </TD>
  </TR>
  <TR>
   <TD colspan="2" rowspan="2" valign="bottom">
    <FONT size="1">Банк получателя</FONT>
    <BR />
    <SPAN>%Executor.BankName%</SPAN>
   </TD>
   <TD>БИК</TD>
   <TD>%Executor.Bik%</TD>
  </TR>
  <TR>
   <TD>Счет №</TD>
   <TD>%Executor.Kor%</TD>
  </TR>
  <TR>
   <TD colspan="4" bgcolor="#FDF6D3">
    <FONT size="1">Назначение платежа</FONT>
    <BR />
    <SPAN>Предоплата за web-услуги по счету №%Invoice.Number% от %Invoice.CreateDate% согласно договору №%Contract.Number% от %Contract.CreateDate%</SPAN>
   </TD>
  </TR>
 </TABLE>
 <P>Плательщик: %Customer.CompanyForm% "%Customer.CompanyName%" ИНН: %Customer.Inn% КПП: %Customer.Kpp%</P>
 <H1>СЧЕТ №%Invoice.Number% от %Invoice.CreateDate%</H1>
 <DIV id="Services">[список услуг]</DIV>
 <P>
  <SPAN>Всего к оплате: </SPAN>
  <B>%Invoice.Wizard%</B>
 </P>
 <TABLE id="Rubbish" cellpadding="10">
  <TR>
   <TD>
    <SPAN>Руководитель предприятия</SPAN>
    <BR />
    <SPAN>%Executor.CompanyForm% "Executor.CompanyName"</SPAN>
    <BR />
    <SPAN>%Executor.dSourname% %Executor.dName% %Executor.dLastname%</SPAN>
   </TD>
   <TD id="Sign">__________________</TD>
   <TD id="Stamp" rowspan="2" />
  </TR>
  <TR>
   <TD>
    <SPAN>Главный бухгалтер</SPAN>
    <BR />
    <SPAN>%Executor.aSourname% %Executor.aName% %Executor.aLastname%</SPAN>
   </TD>
   <TD id="Sign">__________________</TD>
  </TR>
 </TABLE>
</NOBODY>';
-- SEPARATOR
INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','Invoices/PaymentSystems/Juridical/Juridical','Шаблон платежной системы юридического лица исполнителя юридического лица',@Text);