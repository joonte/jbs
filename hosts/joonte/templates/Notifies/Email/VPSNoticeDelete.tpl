{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на виртуальный сервер" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$Params.Item.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS).
До удаления заказа {$Params.Item.StatusDate + $Config.Tasks.Types.VPSForDelete.DeleteTimeout * 24 * 3600 - $smarty.now|date_format:"%d.%m.%Y"}.

{$Params.From.Sign|default:'$Params.From.Sign'}

