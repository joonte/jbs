{*
 *  Joonte Billing System
 *  Copyright © 2013 Alex Keda, for www.host-food.ru
 *}
{assign var=ExpDate value=$DomainOrder.StatusDate + 2678400}
Дата удаления домена {$DomainOrder.DomainName|default:'$DomainOrder.DomainName'}.{$DomainOrder.Name|default:'$DomainOrder.Name'}: {$ExpDate|date_format:"%d.%m.%Y"}

