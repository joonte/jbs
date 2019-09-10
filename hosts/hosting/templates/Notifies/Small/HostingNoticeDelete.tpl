{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=ExpDate value=$StatusDate + $Config.Tasks.Types.HostingForDelete.HostingDeleteTimeout * 24 * 3600}
{$ExpDate|date_format:"%d.%m.%Y"} будет удалён хостинг {$Login|default:'$Login'}, домен {$Domain|default:'$Domain'}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

