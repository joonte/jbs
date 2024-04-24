{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Восстановление ваших данных для входа" scope=global}

По Вашему запросу, был восстановлен Ваш пароль для входа в биллинговую систему.

Ваши данные для входа в биллинговую систему:
  * Адрес для входа:
      {$smarty.const.URL_SCHEME|default:'URL_SCHEME'}://{$smarty.const.HOST_ID|default:'HOST_ID'}/v2/Logon
  * Ваш электронный адрес (используется для входа в биллинговую систему):
      {$User.Email|default:'$User.Email'}
  * Ваш новый пароль:
      {$Password|default:'$Password'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

