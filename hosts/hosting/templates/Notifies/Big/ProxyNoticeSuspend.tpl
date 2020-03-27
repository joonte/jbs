{*
 *  Joonte Billing System
 *  Copyright © 2020, Alex Keda for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа на прокси-сервер [{$ProxyOrder.Login|default:'$ProxyOrder.Login'}]" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$ProxyOrder.OrderID|string_format:"%05u"} на прокси-сервер.
До окончания заказа:	{$ProxyOrder.DaysRemainded|default:'$ProxyOrder.DaysRemainded'} дн.
Баланс договора:	{$ProxyOrder.Balance|default:'$ProxyOrder.Balance'}
Тарифный план:		"{$ProxyOrder.SchemeName|default:'$ProxyOrder.SchemeName'}"
Стоимость продления:	{$ProxyOrder.Cost|default:'$ProxyOrder.Cost'}

  * Адрес подключения:
      {$Host|default:'$Host'}:{$Port|default:'$Port'}
  * Протокол подключения:
      {$ProtocolType|default:'$ProtocolType'}
  * Логин:
      {$Login|default:'$Login'}

