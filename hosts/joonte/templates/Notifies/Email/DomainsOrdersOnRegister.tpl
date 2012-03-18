{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ поступил на регистрацию" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|default:'$StatusDate'} Ваш заказ №{$OrderID|string_format:"%05u"} на регистрацию домена {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'} был отправлен на регистрацию.
{if isset($UploadID)}

Для регистрации домена Вам необходимо загрузить документ подтверждающий личность. Для этого пройдите по ссылке:

http://www.reg.ru/user/docs/add?userdoc_secretkey={$UploadID|default:'$UploadID'}

{/if}
Данные в службе WhoIs станут доступны в течение нескольких часов, а полная регистрация Вашего доменного имени будет завершена в течение 24 часов.

{$From.Sign|default:'$From.Sign'}

