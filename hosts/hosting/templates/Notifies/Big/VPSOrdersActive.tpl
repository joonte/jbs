{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Активирован заказ виртуального сервера #{$OrderID|string_format:"%05u"}, логин ({$Login|default:'$Login'})" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!
Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) был активирован.

Данные для доступа к панели управления VPS сервером:
  * Адрес панели управления: {$Server.Params.Url|default:'$Server.Params.Url'}
  * Логин в панель управления: {$Login|default:'$Login'}
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
  * Первичный сервер имён: {$Server.Params.Ns1Name|default:'$Server.Params.Ns1Name'}
  * Вторичный сервер имён: {$Server.Params.Ns2Name|default:'$Server.Params.Ns2Name'}


Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

---
Обращаем ваше внимание, что при использовании виртуализации KVM смена тарифа на больший не приводит к автоматическому изменению размера диска, видимого операционной системой. Для применения настроек, необходимо перезагрузить виртуальный сервер (Ctrl+Alt+Del в сессии VNC) и изменить размер диска средствами операционной системы.


{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

