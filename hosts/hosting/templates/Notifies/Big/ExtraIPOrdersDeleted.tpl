{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ выделенного IP адреса удален #{$ExtraIPOrder.OrderID|string_format:"%05u"}/[{$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}]" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ на выделенный IP [{$Login|default:'$Login'}], под номером {$Item.OrderID|string_format:"%05u"}, был удален.

Для нового заказа, воспользуйтесь этой ссылкой:
http://{$smarty.const.HOST_ID|default:'HOST_ID'}/ExtraIPSchemes

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

