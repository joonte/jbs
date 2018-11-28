{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Выделенный сервер подключен к сети" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш выделенный сервер был установлен.
Номер заказа		№{$OrderID|string_format:"%05u"}

{if $DSScheme.IPaddr || $IP != 'noassign'}
{if $IP != 'noassign'}
IP адрес:		{$IP}
{else}
IP адрес:		{$DSScheme.IPaddr}
{/if}
{if $DSScheme.DSuser}
Пользователь:		{$DSScheme.DSuser}
{if $DSScheme.DSpass}
Пароль:			{$DSScheme.DSpass}
{/if}
{/if}
{/if}

{if $DSScheme.ILOaddr}
Интерфейс iLO/IPMI:	{$DSScheme.ILOaddr}
{if $DSScheme.ILOuser}
Пользователь iLO/IPMI:	{$DSScheme.ILOuser}
{if $DSScheme.ILOpass}
Пароль iLO/IPMI:	{$DSScheme.ILOpass}
{/if}
{/if}
{/if}

Дополнительная информация будет предоставлена в центре поддержки биллинговой панели.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

