{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Выделенный сервер подключен к сети" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш выделенный сервер был установлен.
Номер заказа		№{$OrderID|string_format:"%05u"}

{if $DSScheme.IPaddr || $IP != 'noassign'}
{if $IP != 'noassign'}
IP адрес сервера:		{$IP}
{else}
IP адрес сервера:		{$DSScheme.IPaddr}
{/if}
{if $DSScheme.OS}
Предустановленная система:	{$DSScheme.OS}
{/if}
{if $DSScheme.DSuser}
Пользователь:			{$DSScheme.DSuser}
{if $DSScheme.DSpass}
Пароль:				{$DSScheme.DSpass}
{/if}
{/if}
{/if}

{if $DSScheme.ILOaddr}
Интерфейс iLO/IPMI:		{$DSScheme.ILOaddr}
{if $DSScheme.ILOuser}
Пользователь iLO/IPMI:		{$DSScheme.ILOuser}
{if $DSScheme.ILOpass}
Пароль iLO/IPMI:		{$DSScheme.ILOpass}
{/if}
{/if}
{/if}

Дополнительная информация будет предоставлена в центре поддержки биллинговой панели.

