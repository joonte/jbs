{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ на домен {$Params.Item.DomainName|default:'$Params.Item.DomainName'}.{$Params.Item.Name|default:'$Params.Item.Name'} удален" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что{$Params.Item.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$Params.Item.OrderID|string_format:"%05u"} на регистрацию домена [{$Params.Item.DomainName|default:'$Params.Item.DomainName'}.{$Params.Item.Name|default:'$Params.Item.Name'}] был удален.
Теперь Вы не являетесь владельцем данного доменного имени. Его в любой момент смогут занять другие лица.

{$Params.From.Sign|default:'$Params.From.Sign'}
