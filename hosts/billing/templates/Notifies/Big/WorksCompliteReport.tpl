{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Отчет за услуги" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Разрешите произвести перед Вами отчет за оказанные услуги.

Вся информация по текущим оказанным услугам может быть получена Вами загрузив акт выполненных работ с ипользованием прямой ссылки:

http://{$smarty.const.HOST_ID|default:'HOST_ID'}/WorksCompliteReportDownload?Email={$User.Email|default:'$User.Email'}&Password={$User.UniqID|default:'$User.UniqID'}&ContractID={$ContractID|default:'$ContractID'}&Month={$Month|default:'$Month'}

Оригиналы документов будут высланы Вам по почте или через электронный документооборот в самое ближайшее время.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

