set @Text = '<P align="justify">Индивидуальный предприниматель "%Executor.CompanyName%", именуемый(ая) в дальнейшем "Исполнитель", действующий(ая) на основании Свидетельства серия %Executor.SvLine% №%Executor.SvNumber% от %Executor.SvDate%, выданного Инспекцией Министерства Российской федерации по налогам и сборам, с одной стороны, и %Customer.Sourname% %Customer.Name% %Customer.Lastname%, именуемый(ая) в дальнейшем "Заказчик", паспорт %Customer.PasportLine% %Customer.PasportNum%, выданный %Customer.PasportWhom%, с другой стороны, именуемые совместно в дальнейшем Стороны, заключили Договор о нижеследующем:</P>';
-- SEPARATOR
INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','yes','Contracts/Types/NaturalPartner/Agreement/Individual','Соглашение исполнителя индивидуального предпринимателя с физическим лицом',@Text);

-- SEPARATOR

set @Text = '<P align="justify">%Executor.dPost% %Executor.dSourname% %Executor.dName% %Executor.dLastname% от имени %Executor.CompanyForm% "%Executor.CompanyName%", именуемое в дальнейшем "Исполнитель", действующий(ая) на основании %Executor.Basis%, с одной стороны, и %Customer.Sourname% %Customer.Name% %Customer.Lastname%, именуемый(ая) в дальнейшем "Заказчик", паспорт %Customer.PasportLine% %Customer.PasportNum%, выданный %Customer.PasportWhom%, с другой стороны, именуемые совместно в дальнейшем Стороны, заключили Договор о нижеследующем:</P>';
-- SEPARATOR
INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','yes','Contracts/Types/NaturalPartner/Agreement/Juridical','Соглашение исполнителя юридического лица с физическим лицом',@Text);

-- SEPARATOR

INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','yes','Contracts/Types/Natural/Agreement/Juridical','Соглашение исполнителя юридического лица с физическим лицом',@Text);
-- SEPARATOR
set @Text = '<FONT size="1">
 <TABLE border="1" width="100%" cellpadding="5">
  <TR>
   <TD>Фамилия Имя Отчество</TD>
   <TD>%Customer.Sourname% %Customer.Name% %Customer.Lastname%</TD>
  </TR>
  <TR>
   <TD>Паспортные данные</TD>
   <TD>%Customer.PasportLine% %Customer.PasportNum% %Customer.PasportWhom%</TD>
  </TR>
  <TR>
   <TD>Почтовый адрес</TD>
   <TD>%Customer.pIndex% %Customer.pCountry% %Customer.pCity% %Customer.pAddress%</TD>
  </TR>
  <TR>
   <TD>Телефон</TD>
   <TD>%Customer.Phone%</TD>
  </TR>
  <TR>
   <TD>Электронный адрес</TD>
   <TD>%Customer.Email%</TD>
  </TR>
 </TABLE>
</FONT>';
-- SEPARATOR
INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','yes','Contracts/Types/NaturalPartner/Customer','Реквизиты клиента физического лица',@Text);

-- SEPARATOR

set @Text = '<TABLE width="100%">
 <TR>
  <TD width="50%" valign="top">
   <TABLE cellpadding="5">
    <TR>
     <TD>От имени Исполнителя:</TD>
    </TR>
    <TR>
     <TD>%Executor.dPost% %Executor.CompanyForm% "%Executor.CompanyName%"</TD>
    </TR>
    <TR>
     <TD>%Executor.dSourname% %Executor.dName% %Executor.dLastname%</TD>
    </TR>
    <TR>
     <TD id="Sign">
      <BR />
      <DIV>__________________</DIV>
      <SUP>[Подпись]</SUP>
     </TD>
    </TR>
    <TR>
     <TD id="Stamp">
      <DIV>%SignDate%</DIV>
      <SUP>[место печати]</SUP>
     </TD>
    </TR>
   </TABLE>
  </TD>
  <TD width="50%" valign="top">
   <TABLE cellpadding="5">
    <TR>
     <TD>От имени Заказчика:</TD>
    </TR>
    <TR>
     <TD>-</TD>
    </TR>
    <TR>
     <TD>%Customer.Sourname% %Customer.Name% %Customer.Lastname%</TD>
    </TR>
    <TR>
     <TD>
      <BR />
      <DIV>__________________</DIV>
      <SUP>[Подпись]</SUP>
     </TD>
    </TR>
    <TR>
     <TD>
      <DIV>"__"_________201__г.</DIV>
     </TD>
    </TR>
   </TABLE>
  </TD>
 </TR>
</TABLE>';
-- SEPARATOR
INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','yes','Contracts/Types/NaturalPartner/Footer/Juridical','Подпись исполнителя юридического лица с физическим лицом',@Text);

-- SEPARATOR

set @Text = '<TABLE width="100%">
 <TR>
  <TD width="50%" valign="top">
   <TABLE cellpadding="5">
    <TR>
     <TD>От имени Исполнителя:</TD>
    </TR>
    <TR>
     <TD>ИП %Executor.CompanyName%</TD>
    </TR>
    <TR>
     <TD>%Executor.dSourname% %Executor.dName% %Executor.dLastname%</TD>
    </TR>
    <TR>
     <TD id="Sign">
      <BR />
      <DIV>__________________</DIV>
      <SUP>[Подпись]</SUP>
     </TD>
    </TR>
    <TR>
     <TD id="Stamp">
      <DIV>%SignDate%</DIV>
      <SUP>[место печати]</SUP>
     </TD>
    </TR>
   </TABLE>
  </TD>
  <TD width="50%" valign="top">
   <TABLE cellpadding="5">
    <TR>
     <TD>От имени Заказчика:</TD>
    </TR>
    <TR>
     <TD>-</TD>
    </TR>
    <TR>
     <TD>%Customer.Sourname% %Customer.Name% %Customer.Lastname%</TD>
    </TR>
    <TR>
     <TD>
      <BR />
      <DIV>__________________</DIV>
      <SUP>[Подпись]</SUP>
     </TD>
    </TR>
    <TR>
     <TD>
      <DIV>"__"_________201__г.</DIV>
     </TD>
    </TR>
   </TABLE>
  </TD>
 </TR>
</TABLE>';
-- SEPARATOR
INSERT INTO `Clauses`
  (`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES
(100,100,'yes','yes','yes','Contracts/Types/NaturalPartner/Footer/Individual','Подпись исполнителя индивидуального предпринимателя с физическим лицом',@Text);
