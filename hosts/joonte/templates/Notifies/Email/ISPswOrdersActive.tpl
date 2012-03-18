{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ программного обеспечения успешно активирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!
Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на ПО ISPsystems был успешно активирован.

IP адрес лицензии: {$IP|default:'$IP'}

Для активации лицензии, используйте слудующую команду:
wget -O /usr/local/ispmgr/etc/ispmgr.lic "http://lic.ispsystem.com/ispmgr.lic?ip={$IP|default:'$IP'}"

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$From.Sign|default:'$From.Sign'}
