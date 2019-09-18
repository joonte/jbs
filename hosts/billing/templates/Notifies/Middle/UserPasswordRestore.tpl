{*
 *  Joonte Billing System
 *  Copyright © 2019 Alex Keda, for www.host-food.ru
 *}

По Вашему запросу, был восстановлен пароль для входа в биллинговую систему.

Логин:  {$User.Email|default:'$User.Email'}
Пароль: {$Password|default:'$Password'}

Адрес для входа:
http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Logon

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

