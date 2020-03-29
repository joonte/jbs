{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа на домен {$DomainOrder.DomainName|default:'$DomainOrder.DomainName'}.{$DomainOrder.DomainZone|default:'$DomainOrder.DomainZone'}" scope=global}

Уведомляем Вас о том, что оканчивается срок регистрации доменного имени {$DomainOrder.DomainName|default:'$DomainOrder.DomainName'}.{$DomainOrder.DomainZone|default:'$DomainOrder.DomainZone'}. Номер заказа #{$DomainOrder.OrderID|string_format:"%05u"}.
Пожалуйста, не забудьте своевременно продлить Ваш заказ, иначе он будет заблокирован и аннулирован, а Ваше доменное имя смогут занять другие люди.
Дата окончания заказа:	{$DomainOrder.ExpirationDate|date_format:"%d.%m.%Y"}.
Баланс договора:	{$DomainOrder.Balance|default:'$DomainOrder.Balance'}
Стоимость продления:	{$DomainOrder.Cost|default:'$DomainOrder.Cost'}

---
Обращаем Ваше внимание, что последнее время участились факты фишинговых рассылок с предложением продлить домен, иначе он будет удалён/продан/заблокирован - на что хватает фантазии у создателей рассыки.
Также могут предлагать "регистрацию в поисковых системах", проверку, подтверждение владением и т.п.
В письме содерджится ссылка на оплату, но домен они, в реальности, не продлевают - просто обманывают. Будьте внимательны, проверяйте сайт, на который ведёт ссылка на оплату.

Единственный вариант, когда может быть письмо не от нас - это международные домены, в некоторых зонах требуют подтвердить контактный адрес владельца домена. Бесплатно.

