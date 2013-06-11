{assign var=ExpDate value=$StatusDate + $Config.Tasks.Types.HostingForDelete.DeleteTimeout * 24 * 3600}
{$ExpDate|date_format:"%d.%m.%Y"} будет удалён хостинг {$Login|default:'$Login'}, домен {$Domain|default:'$Domain'}
