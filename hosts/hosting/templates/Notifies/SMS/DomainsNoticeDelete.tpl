{*
 *  Joonte Billing System
 *  Copyright © 2013 Alex Keda, for www.host-food.ru
 *}
{assign var=ExpDate value=$StatusDate + 2678400}
Оканчивается срок блокировки заказа на домен {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'}, удаление заказа {$ExpDate|date_format:"%d.%m.%Y"}

