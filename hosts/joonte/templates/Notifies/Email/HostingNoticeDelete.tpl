{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на хостинг" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!
#-------------------------------------------------------------------------------
Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$Params.Item.OrderID|string_format:"%05u"} на хостинг, логин {$Params.Login|default:'$Params.Login'}, домен {$Params.Domain|default:'$Params.Domain'}.
До удаления заказа {$Params.StatusDate + $Config.Tasks.Types.HostingForDelete.DeleteTimeout * 24 * 3600 - $smarty.now|date_format:"%d.%m.%Y"}

{$Params.From.Sign|default:'$Params.From.Sign'}


