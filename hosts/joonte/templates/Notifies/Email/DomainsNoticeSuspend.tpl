{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа на домен {$Params.Item.DomainName|default:'$Params.Item.DomainName'}.{$Params.Item.Name|default:'$Params.Item.Name'}" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$Params.Item.OrderID|string_format:"%05u"} {$Params.Item.DomainName|default:'$Params.DomainOrder.DomainName'}.{$Params.Item.Name|default:'$Params.DomainOrder.Name'} на регистрацию домена.
Пожалуйста, не забудьте своевременно продлить Ваш заказ, иначе он будет заблокирован и аннулирован, а Ваше доменное имя смогут занять другие люди.
Дата окончания заказа {$Params.Item.ExpirationDate-$smarty.now|date_format:"%d.%m.%Y"}.

{$Params.From.Sign|default:'$Params.From.Sign'}

