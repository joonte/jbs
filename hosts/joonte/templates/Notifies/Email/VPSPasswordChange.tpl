{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Пароль для заказа виртуального сервера успешно изменен" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$smarty.now|date_format:"%d.%m.%Y"} пароль на Ваш заказ виртуального выделенного сервера (VPS) №{$Params.Item.OrderID|string_format:"%05u"} был успешно изменен.

Ваши новые данные для доступа к аккаунту на сервере:
  * Адрес панели управления: {$Params.Item.Server.Url|default:'$Params.Item.Server.Url'}
  * Логин в панель управления: {$Params.Item.Login|default:'$Params.Item.Login'}
  * Пароль панели управления: {$Params.Item.Password|default:'$Params.Item.Password'}

 Данные для доступа на сервер по SSH:
  * IP адрес сервера: {$Params.Item.Server.Login|default:'$Params.Item.Login'}
  * Имя пользователя: root
  * Пароль: {$Params.Item.Password|default:'$Params.Item.Password'}
 
При заказе сервера с панелью управления ISPmanager Lite, Вы можете войти в нее используя следующие данные:
  * Адрес панели ISPmanager: https://{$Params.Item.Login|default:'$Params.Item.Login'}/manager/ispmgr
  * Логин в панель ISPmanager: root
  * Пароль панели ISPmanger: {$Params.Item.Password|default:'$Params.Item.Password'}

Сервера имён:
  * Первичный сервер имен: {$Params.Item.Ns1Name|default:'$Params.Item.Server.Ns1Name'}
  * Вторичный сервер имен: {$Params.Item.Ns2Name|default:'$Params.Item.Server.Ns2Name'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$Params.From.Sign|default:'$Params.From.Sign'}
