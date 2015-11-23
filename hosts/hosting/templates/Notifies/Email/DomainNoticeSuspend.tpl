{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа на домен {$DomainName|default:'$DomainName'}.{$DomainZone|default:'$DomainZone'}" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок регистрации доменного имени {$DomainName|default:'$DomainName'}.{$DomainZone|default:'$DomainZone'}. Номер заказа #{$ID|string_format:"%05u"}.
Пожалуйста, не забудьте своевременно продлить Ваш заказ, иначе он будет заблокирован и аннулирован, а Ваше доменное имя смогут занять другие люди.
Дата окончания заказа {$ExpirationDate|date_format:"%d.%m.%Y"}.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

