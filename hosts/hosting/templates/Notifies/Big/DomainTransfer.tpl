{*
 *  Joonte Billing System
 *  Copyright © 2022 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Подана заявка на перенос домена {$DomainName|default:'$DomainName'}.{$DomainZone|default:'$DomainZone'}" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} была подана заявка на перенос заказа на домен №{$OrderID|string_format:"%05u"} [{$DomainName|default:'$DomainName'}.{$DomainZone|default:'$DomainZone'}].

От регистратора домена вам придёт письмо о подтверждении переноса, необходимо подтвердить это.

Фактический перенос произойдёт в срок от 3 до 7 дней, зависит от доменной зоны. Обычно, регистратор "отпускает" домен в последний момент.

