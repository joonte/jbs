{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Изменён пароль для заказа виртуального сервера #{$OrderID|string_format:"%05u"}/{$IP|default:'$IP'}" scope=global}

Уведомляем Вас о том, что {$smarty.now|date_format:"%d.%m.%Y"} пароль на Ваш заказ виртуального выделенного сервера (VPS) №{$OrderID|string_format:"%05u"} был изменен.

Ваши новые данные для доступа к аккаунту на сервере:
  * Адрес панели управления: {$Server.Params.Url|default:'$Server.Params.Url'}
{if {$Server.Params.SystemID} == "VmManager6_Hosting"}
  * Логин в панель управления: {$Login|default:'$Login'}@{$Server.Params.Domain|default:'$Server.Params.Domain'}
{else}
  * Логин в панель управления: {$Login|default:'$Login'} ({$Login|default:'$Login'}@{$Server.Params.Domain|default:'$Server.Params.Domain'})
{/if}
  * Пароль панели управления: {$Password|default:'$Password'}

Если вы заказывали сервер с UNIX-like операционной системой (FreeBSD/Linux), то вы можете войти на него по SSH:
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

