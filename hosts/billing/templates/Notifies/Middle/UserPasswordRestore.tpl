{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}

По Вашему запросу, был восстановлен пароль для входа в биллинговую систему.

Логин:  {$User.Email|default:'$User.Email'}
Пароль: {$Password|default:'$Password'}

Адрес для входа:
{$smarty.const.URL_SCHEME|default:'URL_SCHEME'}://{$smarty.const.HOST_ID|default:'HOST_ID'}/Logon

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

