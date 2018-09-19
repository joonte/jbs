

INSERT INTO `PaymentSystemsCollation` VALUES ('','yes',10,'PayPal','PayPal','PayPal.png','PayPal является простым и безопасным средством перечисления и приёма денежных ресурсов в сети Интернет. PayPal предоставляет возможность выбора наиболее удобного и оптимального способа оплаты, в том числе используя кредитные карты, банковские счета, PayPal Smart Connect или остаток средств на кошельках, при этом обеспечивая конфиденциальность финансовой информации','PayPal','');


-- SEPARATOR

INSERT INTO `Clauses` (`GroupID`,`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES (6,100,100,'yes','yes','yes','Invoices/PaymentSystems/PayPal','Шаблон платежной системы PayPal','
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
			<TD align="right">%PaymentSystem.Send.business%</TD>
			<TD align="right">%Invoice.Foreign% руб</TD>
		</TR>
	</TABLE>
</NOBODY>'
);



