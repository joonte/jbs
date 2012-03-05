{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ поступил на регистрацию" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$Params.StatusDate|default:'$Params.StatusDate'} Ваш заказ №{$Params.OrderID|string_format:"%05u"} на регистрацию домена {$Params.DomainName|default:'$Params.DomainName'}.{$Params.Name|default:'$Params.Name'} был отправлен на регистрацию.
{if isset($Params.UploadID)}

Для регистрации домена Вам необходимо загрузить документ подтверждающий личность. Для этого пройдите по ссылке:

http://www.reg.ru/user/docs/add?userdoc_secretkey={$Params.UploadID|default:'$Params.UploadID'}

{/if}
Данные в службе WhoIs станут доступны в течение нескольких часов, а полная регистрация Вашего доменного имени будет завершена в течение 24 часов.

{$Params.From.Sign|default:'$Params.From.Sign'}

