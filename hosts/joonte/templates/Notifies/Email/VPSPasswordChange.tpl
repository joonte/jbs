{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Пароль для заказа виртуального сервера успешно изменен" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$smarty.now|date_format:"%d.%m.%Y"} пароль на Ваш заказ виртуального выделенного сервера (VPS) №{$Params.Item.OrderID|string_format:"%05u"} был успешно изменен.

Ваши новые данные для доступа к аккаунту на сервере:
  * Адрес панели управления: {$Params.Server.Url|default:'$Params.Server.Url'}
  * Логин в панель управления: {$Params.Login|default:'$Params.Login'}
  * Пароль панели управления: {$Params.Password|default:'$Params.Password'}

 Данные для доступа на сервер по SSH:
  * IP адрес сервера: {$Params.Server.Login|default:'$Params.Server.Login'}
  * Имя пользователя: root
  * Пароль: {$Params.Password|default:'$Params.Password'}
 
При заказе сервера с панелью управления ISPmanager Lite, Вы можете войти в нее используя следующие данные:
  * Адрес панели ISPmanager: https://{$Params.Login|default:'$Params.Login'}/manager/ispmgr
  * Логин в панель ISPmanager: root
  * Пароль панели ISPmanger: {$Params.Password|default:'$Params.Password'}

Сервера имён:
  * Первичный сервер имен: {$Params.Ns1Name|default:'$Params.Server.Ns1Name'}
  * Вторичный сервер имен: {$Params.Ns2Name|default:'$Params.Server.Ns2Name'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$Params.From.Sign|default:'$Params.From.Sign'}
