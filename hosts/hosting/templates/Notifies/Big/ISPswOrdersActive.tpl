{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ программного обеспечения активирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!
Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на ПО ISPsystems был активирован.

IP адрес лицензии: {$IP|default:'$IP'}

Для активации лицензии 4-й версии, используйте слудующую команду:
wget -O /usr/local/ispmgr/etc/ispmgr.lic "http://lic.ispsystem.com/ispmgr.lic?ip={$IP|default:'$IP'}"
Для активации лицензии 5-й версии, используйте слудующую команду:
wget -O /usr/local/mgr5/etc/ispmgr.lic "http://lic.ispsystem.com/ispmgr.lic?ip={$IP|default:'$IP'}"


Обращаем ваше внимание, если вы заказали лицензию четвёртой версии, то установку программного обеспечения вам необходимо произвести самостоятельно.

При заказе лицензии для виртуального сервера с шаблоном ISPmanager, вы можете сразу же пользоваться лицензией, пройдя по адресу:
http://{$IP|default:'$IP'}:1500/ispmgr

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

