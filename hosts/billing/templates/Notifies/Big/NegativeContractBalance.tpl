{*
 *  Joonte Billing System
 *  Copyright © 2012 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Отрицательный баланс договора" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Один из ваших договоров имеет отрицательный балланс ({$Balance|default:'$Balance'}). Обычно, такое 
происходит в результате отмены условно оплаченных счетов. Точную причину, вы
можете посмотреть в истории операций по вашим счетам:
http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Postings

Рекомендуем пополнить счёт на недостающую сумму, в противном случае, мы будем
вынуждены приостановить оказание вам услуг - заблокировать заказ хостинга, VPS,
или снять домен с делегирования.

Ваши счета на оплату: http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Invoices

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

