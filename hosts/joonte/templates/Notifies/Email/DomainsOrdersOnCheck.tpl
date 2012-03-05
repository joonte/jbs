{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ поступил на проверку" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$Params.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$Params.OrderID|string_format:"%05u"} на регистрацию домена поступил на обработку нашим операторам.
После проверки, Ваш заказ будет немедленно исполнен.

{$Params.From.Sign|default:'$Params.From.Sign'}

