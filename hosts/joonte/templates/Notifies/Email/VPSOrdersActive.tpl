{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ виртуального сервера успешно активирован" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!
Уведомляем Вас о том, что {$Params.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$Params.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) был успешно активирован.

Данные для доступа к панели управления VPS сервером:
  * Адрес панели управления: {$Params.Server.Url|default:'$Params.Server.Url'}
  * Логин в панель управления: {$Params.Login|default:'$Params.Login'}
  * Пароль панели управления: {$Params.Password|default:'$Params.Password'}

Данные для доступа на сервер по SSH:
  * IP адрес сервера: {$Params.Login|default:'$Params.Login'}
  * Имя пользователя: root
  * Пароль: {$Params.Password|default:'$Params.Password'}
 
При заказе сервера с панелью управления ISPmanager Lite, Вы можете войти в нее используя следующие данные:
  * Адрес панели ISPmanager: https://{$Params.Login|default:'$Params.Login'}/manager/ispmgr
  * Логин в панель ISPmanager: root
  * Пароль панели ISPmanger: {$Params.Password|default:'$Params.Password'}

Сервера имён:
  * Первичный сервер имён: {$Params.Server.Ns1Name|default:'$Params.Server.Ns1Name'}
  * Вторичный сервер имён: {$Params.Server.Ns2Name|default:'$Params.Server.Ns2Name'}


Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$Params.From.Sign|default:'$Params.From.Sign'}
