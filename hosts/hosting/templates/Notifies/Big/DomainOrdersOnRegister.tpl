{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ домена ({$DomainName|default:'$DomainName'}.{$Name|default:'$Name'}) поступил на регистрацию" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ #{$OrderID|string_format:"%05u"} на регистрацию домена {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'} был отправлен на регистрацию.
{if isset($RegistrationMessage)}

{$RegistrationMessage|default:'$RegistrationMessage'}

{/if}
{if isset($UploadID)}

Для регистрации домена Вам необходимо загрузить документ подтверждающий личность. Для этого пройдите по ссылке:

http://www.reg.ru/user/docs/add?userdoc_secretkey={$UploadID|default:'$UploadID'}

{/if}
Данные в службе WhoIs станут доступны в течение нескольких часов, а полная регистрация Вашего доменного имени будет завершена в течение 24 часов.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

