{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа на домен {$Params.DomainName|default:'$Params.DomainName'}.{$Params.DomainZone|default:'$Params.DomainZone'}" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$Params.ID|string_format:"%05u"} {$Params.DomainName|default:'$Params.DomainName'}.{$Params.DomainZone|default:'$Params.DomainZone'} на регистрацию домена.
Пожалуйста, не забудьте своевременно продлить Ваш заказ, иначе он будет заблокирован и аннулирован, а Ваше доменное имя смогут занять другие люди.
Дата окончания заказа {$Params.ExpirationDate|date_format:"%d.%m.%Y"}.

{$Params.From.Sign|default:'$Params.From.Sign'}

