{*
 *  Joonte Billing System
 *  Copyright © 2020, Alex Keda for www.host-food.ru
 *}
{assign var=ExpDate value=$StatusDate + $Config.Tasks.Types.ProxyForDelete.ProxyDeleteTimeout * 24 * 3600}
{$ExpDate|date_format:"%d.%m.%Y"} будет удалён прокси-сервер {$Host|default:'$Host'}:{$Port|default:'$Port'}

