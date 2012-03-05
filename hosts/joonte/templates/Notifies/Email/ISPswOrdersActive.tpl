{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ программного обеспечения успешно активирован" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!
Уведомляем Вас о том, что {$Params.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$Params.OrderID|string_format:"%05u"} на ПО ISPsystems был успешно активирован.

IP адрес лицензии: {$Params.IP|default:'$Params.IP'}

Для активации лицензии, используйте слудующую команду:
wget -O /usr/local/ispmgr/etc/ispmgr.lic "http://lic.ispsystem.com/ispmgr.lic?ip={$Params.IP|default:'$Params.IP'}"

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$Params.From.Sign|default:'$Params.From.Sign'}
