{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Пароль для заказа хостинга успешно изменен" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$smarty.now|date_format:"%d.%m.%Y"} пароль на Ваш заказ хостинга №{$HostingOrder.OrderID|string_format:"%05u"} был успешно изменен.

Ваши новые данные для доступа к аккаунту на сервере:
  * Адрес панели управления:
      {$Server.Url|default:'$Server.Url'}
  * Логин:
      {$HostingOrder.Login|default:'$HostingOrder.Login'}
  * Пароль:
      {$HostingOrder.Password|default:'$HostingOrder.Password'}
  * Доменное имя:
      {$HostingOrder.Domain|default:'$HostingOrder.Domain'}
  * FTP, POP3, SMTP, IMAP:
      {$Server.Address|default:'$Server.Address'}
Сервера имен:
  * Первичный сервер имен:
      {$Server.Ns1Name|default:'$Server.Ns1Name'}
  * Вторичный сервер имен:
      {$Server.Ns2Name|default:'$Server.Ns2Name'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$From.Sign|default:'$From.Sign'}

