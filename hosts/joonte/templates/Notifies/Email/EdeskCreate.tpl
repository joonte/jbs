{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас, что на нашем сайте открыто новое обсуждение с темой: {$Theme|default:'$Theme'}.

---
{$Message|default:'$Message'}
---

Для участия в обсуждении нажмите:

http://{$smarty.const.HOST_ID}/EdeskMessages?Email={$User.Email|default:'$User.Email'}&Password={$User.UniqID|default:'$User.UniqID'}&EdeskID={$EdeskID|default:'$EdeskID'}

Если Вы не хотите получать уведомления о новых обсуждениях, созданных на нашем сайте, зайдите в биллинговую систему используя Ваш регистрационный электронный адрес и пароль (Вы так же можете воспользоваться ссылкой, указанной выше), и в верхнем меню вызовите "Мои настройки"->"Уведомления" и отключите данный вид уведомлений.

{$From.Sign|default:'$From.Sign'}
