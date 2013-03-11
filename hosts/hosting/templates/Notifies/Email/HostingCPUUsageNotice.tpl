{*
 *  Copyright © 2012 Rootden for Dgrad-host.com
 *  rewritten by Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Превышение использования CPU (процессорного времени)" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что ваш аккаунт {$HostingOrder.Login|default:'$HostingOrder.Login'}, паркованный домен {$HostingOrder.Domain|default:'$HostingOrder.Domain'}, превысил использование процессорного времени, определённое вашим тарифом "{$HostingOrder.Scheme|default:'$HostingOrder.Scheme'}".

Среднесуточное использование за вчерашний день составило: {$HostingOrder.SUsage|default:'$HostingOrder.SUsage'}%, при лимите тарифного плана: {$HostingOrder.QuotaCPU|default:'$HostingOrder.QuotaCPU'}%.

Просим вас решить эту проблему или повысить тариф, иначе, при систематических превышениях, аккаунт может быть заблокирован в автоматическом режиме.

Подробную статистику использования ресурсов, вы можете узнать в панели управления хостингом, раздел "Системные ресурсы":
{$HostingOrder.Url|default:'$HostingOrder.Url'}

За более детальной информацией обращайтесь в систему тикетов, биллинговой системы:
http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Tickets

{$From.Sign|default:'$From.Sign'}
