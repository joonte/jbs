{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="{$Theme|default:'$Theme'}" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

---
{$Message|default:'$Message'}
---
Если вы не хотите получать рассылки от администрации, вы можете их отключить в биллинговой системе:
Мои настройки / Уведомления / Сообщения от администрации
---
{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

