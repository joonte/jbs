{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Пароль для заказа виртуального сервера успешно изменен" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$smarty.now|date_format:"%d.%m.%Y"} пароль на Ваш заказ виртуального выделенного сервера (ExtraIP) №{$Item.OrderID|string_format:"%05u"} был успешно изменен.

Ваши новые данные для доступа к аккаунту на сервере:
  * Адрес панели управления: {$Item.Server.Url|default:'$Item.Server.Url'}
  * Логин в панель управления: {$Item.Login|default:'$Item.Login'}
  * Пароль панели управления: {$Item.Password|default:'$Item.Password'}

Данные для доступа на сервер по SSH:
  * IP адрес сервера: {$Item.Server.Login|default:'$Item.Login'}
  * Имя пользователя: root
  * Пароль: {$Item.Password|default:'$Item.Password'}
 
При заказе сервера с панелью управления ISPmanager Lite, Вы можете войти в нее используя следующие данные:
  * Адрес панели ISPmanager: https://{$Item.Login|default:'$Item.Login'}/manager/ispmgr
  * Логин в панель ISPmanager: root
  * Пароль панели ISPmanger: {$Item.Password|default:'$Item.Password'}

Сервера имён:
  * Первичный сервер имен: {$Item.Ns1Name|default:'$Item.Server.Ns1Name'}
  * Вторичный сервер имен: {$Item.Ns2Name|default:'$Item.Server.Ns2Name'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$From.Sign|default:'$From.Sign'}
