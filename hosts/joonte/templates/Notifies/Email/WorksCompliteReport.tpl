{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Отчет за услуги" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Разрешите произвести перед Вами отчет за оказанные услуги.

Вся информация по текущим оказанным услугам может быть получена Вами загрузив акт выполненных работ с ипользованием прямой ссылки:

http://{$smarty.const.HOST_ID|default:'HOST_ID'}/WorksCompliteReportDownload?Email={$Params.User.Email|default:'$Params.User.Email'}&Password={$Params.User.UniqID|default:'$Params.User.UniqID'}&ContractID={$Params.ContractID|default:'$Params.ContractID'}&Month={$Params.Month|default:'$Params.Month'}

Оригиналы документов будут высланы Вам по почте в самое ближайшее время.

{$Params.From.Sign|default:'$Params.From.Sign'}
