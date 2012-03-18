{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
Здравствуйте, %User.Name%!

Уведомляем Вас, что на нашем сайте добавлено новое сообщение для обсуждения с темой: %Theme%.

---
%Message%
---

Для продолжения участия в обсуждении нажмите:

http://%HostID%/EdeskMessages?Email=%User.Email%&Password=%User.UniqID%&EdeskID=%EdeskID%

{$From.Sign|default:'$From.Sign'}
