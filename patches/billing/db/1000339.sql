

INSERT INTO `Clauses` (`GroupID`,`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES (6,100,100,'yes','yes','yes','Invoices/PaymentSystems/Yandex.p2p','Шаблон платежной системы Yandex.p2p','
<NOBODY>
	<H1>СЧЕТ №%Invoice.Number% от %Invoice.CreateDate%</H1>
	<DIV id="Services">[список услуг]</DIV>
	<H2>Платежное поручение</H2>
	<TABLE border="1" cellpadding="5" cellspacing="0">
		<TR bgcolor="#DCDCDC">
			<TD align="center">Назначение</TD>
			<TD align="center">Кошелек №</TD>
			<TD align="center">Сумма</TD>
		</TR>
		<TR>
			<TD>За web-услуги по счету №%Invoice.Number%</TD>
			<TD align="right">%PaymentSystem.Send.receiver%</TD>
			<TD align="right">%Invoice.Foreign% руб</TD>
		</TR>
	</TABLE>
</NOBODY>'
);

