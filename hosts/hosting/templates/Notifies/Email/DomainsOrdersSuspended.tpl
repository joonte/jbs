{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ на домен {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'} заблокирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на регистрацию домена {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'} был заблокирован. Теперь Вы не можете использовать данное доменное имя для доступа к Вашему сайту. В течении месяца у Вас еще остается возможность продления имени, по истечении данного срока, домен станет свободным.

{$From.Sign|default:'$From.Sign'}
