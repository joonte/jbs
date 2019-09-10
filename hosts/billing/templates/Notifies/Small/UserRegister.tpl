{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

Поздравляем Вас, вы зарегистрировались на ресурсе {$smarty.const.HOST_ID|default:'$HOST_ID'}!

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

