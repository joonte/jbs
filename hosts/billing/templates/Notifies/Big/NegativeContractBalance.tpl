{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Отрицательный баланс договора" scope=global}

{if isset($PreTrial)}
Ваш договор №{$ContractID|default:'$ContractID'} имеет задолженность в размере ({$Balance|default:'$Balance'}). В порядке
досудебного урегулирования, просим пополнить его на сумму ({$Balance|default:'$Balance'})
В противном случае, оставляем за собой право обратится в судебные органы для взыскания задолженности.
{else}
Ваш договор №{$ContractID|default:'$ContractID'} имеет отрицательный балланс (-{$Balance|default:'$Balance'}). Обычно, такое
происходит в результате отмены условно оплаченных счетов. Точную причину, вы
можете посмотреть в истории операций по договору.

Рекомендуем пополнить счёт на недостающую сумму, в противном случае, мы будем
вынуждены приостановить оказание вам услуг - заблокировать заказ хостинга, VPS,
или снять домен с делегирования.
{/if}

История операций по договору: https://{$smarty.const.HOST_ID|default:'HOST_ID'}/Postings?ContractID={$ContractID|default:'$ContractID'}
Ваши счета на оплату: https://{$smarty.const.HOST_ID|default:'HOST_ID'}/Invoices

