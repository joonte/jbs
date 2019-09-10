{*
 *  Copyright © 2013 Rootden for Dgrad-host.com
 *  rewritten by Alex Keda, for www.host-food.ru
 *}
У вас есть неоплаченный счёт с номером #{$InvoiceID|default:'$InvoiceID'}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

