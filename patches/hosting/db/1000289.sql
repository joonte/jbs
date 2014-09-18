DELETE FROM `Clauses` WHERE `Partition` = 'Registrators/REGRU/internal';
-- SEPARATOR
DELETE FROM `Clauses` WHERE `Partition` = 'Registrators/REGTIME/Internal';
-- SEPARATOR

set @Text = '<P>Шаблоны писем на смену обслуживающего партнера (данные нашего партнерского аккаунта указаны выше):<BR />
для юридических лиц:            http://www.reg.ru/docs/letters/dom_transfer_acc_org_ru.rtf<BR />
для физических лиц и ИП:        http://www.reg.ru/docs/letters/dom_transfer_acc_pers_ru.rtf</P>';
-- SEPARATOR
INSERT INTO `Clauses` (`GroupID`,`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES (7,100,100,'no','no','no','Registrators/REGRU/internal','Трансфер внутренний. REGRU.',@Text);

-- SEPARATOR
set @Text = '<P>Администратору домена следует прислать копию паспорта (если администратор — физическое лицо) или заявление на официальном бланке организации с печатью и подписью руководителя (для юридических лиц) на e-mail support@webnames.ru (в отсканированном виде) или по факсу +7 846 9799038, и сообщить о своем желании передать свой домен под сопровождение нового партнера указав наш логин и номер договора (данные были указаны выше).<BR />
<BR />Перенос домена на наш аккаунт может занять у регистратора до 5 рабочих дней. В переносе может быть отказано на основании причин, указанных в правилах регистрации доменов.</P>';
-- SEPARATOR
INSERT INTO `Clauses` (`GroupID`,`AuthorID`,`EditorID`,`IsProtected`,`IsXML`,`IsDOM`,`Partition`,`Title`,`Text`)
VALUES (7,100,100,'no','no','no','Registrators/REGTIME/Internal','Трансфер внутренний. REGTIME.',@Text);

