{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Пароль для заказа виртуального сервера #{$OrderID|string_format:"%05u"}/[{$IP|default:'$IP'}] изменен" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$smarty.now|date_format:"%d.%m.%Y"} пароль на Ваш заказ виртуального выделенного сервера (VPS) №{$OrderID|string_format:"%05u"} был изменен.

Ваши новые данные для доступа к аккаунту на сервере:
  * Адрес панели управления: {$Server.Params.Url|default:'$Server.Params.Url'}
  * Логин в панель управления: {$Login|default:'$Login'}
  * Пароль панели управления: {$Password|default:'$Password'}

 Данные для доступа на сервер по SSH:
  * IP адрес сервера: {$IP|default:'$IP'}
  * Имя пользователя: root
  * Пароль: {$Password|default:'$Password'}
 
При заказе сервера с панелью управления ISPmanager, Вы можете войти в неё используя следующие данные:
  * Адрес панели ISPmanager: https://{$IP|default:'$IP'}:1500/ispmgr
  * Логин в панель ISPmanager: root
  * Пароль панели ISPmanger: {$Password|default:'$Password'}

Сервера имён:
  * Первичный сервер имен: {$Server.Params.Ns1Name|default:'$Server.Params.Ns1Name'}
  * Вторичный сервер имен: {$Server.Params.Ns2Name|default:'$Server.Params.Ns2Name'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$From.Sign|default:'$From.Sign'}
