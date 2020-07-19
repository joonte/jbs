{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на домен {$DomainOrder.DomainName|default:'$DomainOrder.DomainName'}.{$DomainOrder.Name|default:'$DomainOrder.Name'}" scope=global}
{assign var=ExpDate value=$DomainOrder.StatusDate + 2678400}

Уведомляем Вас о том, оканчивается срок блокировки Вашего заказа №{$DomainOrder.OrderID|string_format:"%05u"}, на регистрацию домена [{$DomainOrder.DomainName|default:'$DomainOrder.DomainName'}.{$DomainOrder.Name|default:'$DomainOrder.Name'}].
Дата удаления заказа:   {$ExpDate|date_format:"%d.%m.%Y"}
Баланс договора:        {$DomainOrder.Balance|default:'$DomainOrder.Balance'}
Стоимость продления:    {$DomainOrder.Cost|default:'$DomainOrder.Cost'}*

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

---
Обращаем Ваше внимание, что последнее время участились факты фишинговых рассылок с предложением продлить домен, иначе он будет удалён/продан/заблокирован - на что хватает фантазии у создателей рассыки.
Также могут предлагать "регистрацию в поисковых системах", проверку, подтверждение владением и т.п.
В письме содерджится ссылка на оплату, но домен они, в реальности, не продлевают - просто обманывают. Будьте внимательны, проверяйте сайт, на который ведёт ссылка на оплату.

Единственный вариант, когда может быть письмо не от нас - это международные домены, в некоторых зонах требуют подтвердить контактный адрес владельца домена. Бесплатно.

