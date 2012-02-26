{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас, что на нашем сайте открыто новое обсуждение с темой: {$Params.Theme|default:'$Params.Theme'}.

---
{$Params.Message|default:'$Params.Message'}
---

Для участия в обсуждении нажмите:

http://{$smarty.const.HOST_ID}/EdeskMessages?Email={$Params.User.Email|default:'$Params.User.Email'}&Password={$Params.User.UniqID|default:'$Params.User.UniqID'}&EdeskID={$Params.EdeskID|default:'$Params.EdeskID'}

Если Вы не хотите получать уведомления о новых обсуждениях, созданных на нашем сайте, зайдите в биллинговую систему используя Ваш регистрационный электронный адрес и пароль (Вы так же можете воспользоваться ссылкой, указанной выше), и в верхнем меню вызовите "Мои настройки"->"Уведомления" и отключите данный вид уведомлений.

{$Params.From.Sign|default:'$Params.From.Sign'}
