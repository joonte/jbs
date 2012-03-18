{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Ваши данные для входа" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

По Вашему запросу, был восстановлен Ваш пароль для входа в биллинговую систему.

Ваши данные для входа в биллинговую систему:
  * Адрес для входа:
      http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Logon
  * Ваш электронный адрес (используется для входа в биллинговую систему):
      {$User.Email|default:'$User.Email'}
  * Ваш новый пароль:
      {$Password|default:'$Password'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{$From.Sign|default:'$From.Sign'}
