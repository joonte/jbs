{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Активирован заказ прокси-сервера #{$OrderID|string_format:"%05u"}, логин ({$Login|default:'$Login'})" scope=global}
Активирован прокси сервер:
Proxy:	{$Host|default:'$Host'}:{$Port|default:'$Port'}
Proto:	{$ProtocolType|default:'$ProtocolType'}
Login:	{$Login|default:'$Login'}
Pass:	{$Password|default:'$Password'}

