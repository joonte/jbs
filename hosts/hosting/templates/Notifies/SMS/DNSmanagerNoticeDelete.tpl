{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=ExpDate value=$StatusDate + $Config.Tasks.Types.DNSmanagerForDelete.DNSmanagerDeleteTimeout * 24 * 3600}
{$ExpDate|date_format:"%d.%m.%Y"} будет удалён вторичный DNS {$Login|default:'$Login'}

