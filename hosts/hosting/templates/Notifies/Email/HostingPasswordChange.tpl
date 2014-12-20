{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Пароль для заказа хостинга [{$Login|default:'$Login'}] изменен" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$smarty.now|date_format:"%d.%m.%Y"} пароль на Ваш заказ хостинга №{$OrderID|string_format:"%05u"} был изменен.

Ваши новые данные для доступа к аккаунту на сервере:
  * Адрес панели управления:
      {$Server.Params.Url|default:'$Server.Params.Url'}
  * Логин:
      {$Login|default:'$Login'}
  * Пароль:
      {$Password|default:'$Password'}
  * Доменное имя:
      {$Domain|default:'$Domain'}
  * FTP, POP3, SMTP, IMAP:
      {$Server.Address|default:'$Server.Address'}
  * Сервер базы данных MySQL:
      {$Server.Params.MySQL|default:'$Server.Params.MySQL'}
Сервера имен:
  * Первичный сервер имен:
      {$Server.Params.Ns1Name|default:'$Server.Params.Ns1Name'}
  * Вторичный сервер имен:
      {$Server.Params.Ns2Name|default:'$Server.Params.Ns2Name'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$From.Sign|default:'$From.Sign'}

