{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Ваши данные для входа" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

По Вашему запросу, был восстановлен Ваш пароль для входа в биллинговую систему.

Ваши данные для входа в биллинговую систему:
  * Адрес для входа:
      http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Logon
  * Ваш электронный адрес (используется для входа в биллинговую систему):
      {$Params.User.Email|default:'$Params.User.Email'}
  * Ваш новый пароль:
      {$Params.Password|default:'$Password'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$Params.From.Sign|default:'$Params.From.Sign'}
