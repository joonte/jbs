{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ виртуального сервера #{$OrderID|string_format:"%05u"}/[{$Login|default:'$Login'}] успешно активирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!
Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) был успешно активирован.

Данные для доступа к панели управления VPS сервером:
  * Адрес панели управления: {$Server.Params.Url|default:'$Server.Params.Url'}
  * Логин в панель управления: {$Login|default:'$Login'}
  * Пароль панели управления: {$Password|default:'$Password'}

Данные для доступа на сервер по SSH:
  * IP адрес сервера: {$Login|default:'$Login'}
  * Имя пользователя: root
  * Пароль: {$Password|default:'$Password'}
 
При заказе сервера с панелью управления ISPmanager Lite, Вы можете войти в нее используя следующие данные:
  * Адрес панели ISPmanager: https://{$Login|default:'$Login'}/manager/ispmgr
  * Логин в панель ISPmanager: root
  * Пароль панели ISPmanger: {$Password|default:'$Password'}

Сервера имён:
  * Первичный сервер имён: {$Server.Params.Ns1Name|default:'$Server.Params.Ns1Name'}
  * Вторичный сервер имён: {$Server.Params.Ns2Name|default:'$Server.Params.Ns2Name'}


Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$From.Sign|default:'$From.Sign'}
