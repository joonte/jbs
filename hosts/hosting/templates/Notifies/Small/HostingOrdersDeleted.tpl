{*
 *  Copyright © 2013 Rootden for Dgrad-host.com
 *  rewritten by Alex Keda, for www.host-food.ru
 *}
Ваш заказ #{$OrderID|string_format:"%05u"} на хостинг, логин {$Login|default:'$Login'} был удален {$StatusDate|date_format:"%d.%m.%Y"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

