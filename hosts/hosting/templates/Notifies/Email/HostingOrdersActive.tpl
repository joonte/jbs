{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ хостинга успешно активирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на хостинг был успешно активирован.

Ваши данные для доступа к аккаунту на сервере:
  * Адрес панели управления:
      {$Server.Url|default:'$Server.Url'}
  * Логин:
      {$Login|default:'$Login'}
  * Пароль:
      {$Password|default:'$Password'}
  * Доменное имя:
      {$Domain|default:'$Domain'}
  * FTP, POP3, SMTP, IMAP:
      {$Server.Address|default:'$Server.Address'}
Сервера имен:
  * Первичный сервер имен:
      {$Server.Ns1Name|default:'$Server.Ns1Name'}
  * Вторичный сервер имен:
      {$Server.Ns2Name|default:'$Server.Ns2Name'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$From.Sign|default:'$From.Sign'}
