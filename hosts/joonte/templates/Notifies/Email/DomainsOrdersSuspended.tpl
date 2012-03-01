{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ на домен {$Params.DomainName|default:'$Params.DomainName'}.{$Params.Name|default:'$Params.Name'} заблокирован" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$Params.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$Params.OrderID|string_format:"%05u"} на регистрацию домена {$Params.DomainName|default:'$Params.DomainName'}.{$Params.Name|default:'$Params.Name'} был заблокирован. Теперь Вы не можете использовать данное доменное имя для доступа к Вашему сайту. В течении месяца у Вас еще остается возможность продления имени, по истечении данного срока, домен станет свободным.

{$Params.From.Sign|default:'$Params.From.Sign'}
