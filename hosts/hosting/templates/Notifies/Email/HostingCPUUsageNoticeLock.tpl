{*
 *  Copyright © 2012 Rootden for Dgrad-host.com
 *  rewritten by Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Аккаунт заблокирован за превышение использования CPU (процессорного времени)" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что ваш аккаунт {$HostingOrder.Login|default:'$HostingOrder.Login'}, паркованный домен {$HostingOrder.Domain|default:'$HostingOrder.Domain'}, превысил использование процессорного времени, определённое вашим тарифом "{$HostingOrder.Scheme|default:'$HostingOrder.Scheme'}". Превышения были систематические, на предыдущие уведомления по данному поводу вы не реагировали, поэтому аккаунт заблокирован.

Среднее использование за {$HostingOrder.PeriodToLock|default:'$HostingOrder.PeriodToLock'} дней составило: {$HostingOrder.BUsage|default:'$HostingOrder.BUsage'}%, при лимите тарифного плана: {$HostingOrder.QuotaCPU|default:'$HostingOrder.QuotaCPU'}%.

{if $HostingOrder.UnLockOverlimits}
Если вы никак не отреагируете на данное событие, то ваш аккаунт будет автоматически разблокирован через {$HostingOrder.UnLockOverlimitsPeriod|default:'$HostingOrder.UnLockOverlimitsPeriod'} часов.

{/if}
Подробную статистику использования ресурсов, вы можете узнать в панели управления хостингом:
{$HostingOrder.Url|default:'$HostingOrder.Url'}

За более детальной информацией обращайтесь в систему тикетов, биллинговой системы:
http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Tickets

{$From.Sign|default:'$From.Sign'}
