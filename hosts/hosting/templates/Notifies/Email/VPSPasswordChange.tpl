{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Пароль для заказа виртуального сервера успешно изменен" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$smarty.now|date_format:"%d.%m.%Y"} пароль на Ваш заказ виртуального выделенного сервера (VPS) №{$VPSOrder.OrderID|string_format:"%05u"} был успешно изменен.

Ваши новые данные для доступа к аккаунту на сервере:
  * Адрес панели управления: {$VPSOrder.Server.Url|default:'$VPSOrder.Server.Url'}
  * Логин в панель управления: {$VPSOrder.Login|default:'$VPSOrder.Login'}
  * Пароль панели управления: {$VPSOrder.Password|default:'$VPSOrder.Password'}

 Данные для доступа на сервер по SSH:
  * IP адрес сервера: {$VPSOrder.Server.Login|default:'$VPSOrder.Server.Login'}
  * Имя пользователя: root
  * Пароль: {$VPSOrder.Password|default:'$VPSOrder.Password'}
 
При заказе сервера с панелью управления ISPmanager Lite, Вы можете войти в нее используя следующие данные:
  * Адрес панели ISPmanager: https://{$VPSOrder.Login|default:'$VPSOrder.Login'}/manager/ispmgr
  * Логин в панель ISPmanager: root
  * Пароль панели ISPmanger: {$VPSOrder.Password|default:'$VPSOrder.Password'}

Сервера имён:
  * Первичный сервер имен: {$VPSOrder.Ns1Name|default:'$VPSOrder.Server.Ns1Name'}
  * Вторичный сервер имен: {$VPSOrder.Ns2Name|default:'$VPSOrder.Server.Ns2Name'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$From.Sign|default:'$From.Sign'}
