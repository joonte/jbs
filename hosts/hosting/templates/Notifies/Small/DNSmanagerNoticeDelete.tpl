{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=ExpDate value=$DNSmanagerOrder.StatusDate + $Config.Tasks.Types.DNSmanagerForDelete.DNSmanagerDeleteTimeout * 24 * 3600}
{$ExpDate|date_format:"%d.%m.%Y"} будет удалён вторичный DNS {$DNSmanagerOrder.Login|default:'$DNSmanagerOrder.Login'}

