{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Пароль для заказа хостинга успешно изменен" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$smarty.now|date_format:"%d.%m.%Y"} пароль на Ваш заказ хостинга №{$Params.OrderID|string_format:"%05u"} был успешно изменен.

Ваши новые данные для доступа к аккаунту на сервере:
  * Адрес панели управления:
      {$Params.Server.Url|default:'$Params.Server.Url'}
  * Логин:
      {$Params.Login|default:'$Params.Login'}
  * Пароль:
      {$Params.Password|default:'$Params.Password'}
  * Доменное имя:
      {$Params.Domain|default:'$Params.Domain'}
  * FTP, POP3, SMTP, IMAP:
      {$Params.Server.Address|default:'$Params.Server.Address'}
Сервера имен:
  * Первичный сервер имен:
      {$Params.Server.Ns1Name|default:'$Params.Server.Ns1Name'}
  * Вторичный сервер имен:
      {$Params.Server.Ns2Name|default:'$Params.Server.Ns2Name'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$Params.From.Sign|default:'$Params.From.Sign'}

