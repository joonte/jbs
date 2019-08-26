{*
 *  Copyright © 2013 Rootden for Dgrad-host.com
 *  rewritten by Alex Keda, for www.host-food.ru
 *}
{$smarty.now|date_format:"%d.%m.%Y"} изменён пароль для заказа на хостинг
логин: {$Login|default:'$Login'}
пароль: {$Password|default:'$Password'}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

