{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}

У вас есть счёт, номер #{$ID|default:'$ID'}, в статусе "Условно оплачен".
Рекомендуем в ближайшее время его оплатить, в противном случае будем вынуждены
приостановить оказание услуг оплаченных данным счётом - заблокировать заказ
хостинга, VPS, или снять домен с делегирования.

Для оплаты счёта, воспользуйтесь этой ссылкой:
{$PaymentLink|default:'$PaymentLink'}

