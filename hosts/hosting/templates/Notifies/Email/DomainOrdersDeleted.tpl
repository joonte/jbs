{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ на домен {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'} удален" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что{$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на регистрацию домена [{$DomainName|default:'$DomainName'}.{$Name|default:'$Name'}] был удален.
Теперь Вы не являетесь владельцем данного доменного имени. Его в любой момент смогут занять другие лица.

{$From.Sign|default:'$From.Sign'}
