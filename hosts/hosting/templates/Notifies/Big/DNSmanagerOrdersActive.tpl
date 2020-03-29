{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заказ вторичного DNS [{$Login|default:'$Login'}] активирован" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на вторичный DNS был активирован.

Ваши данные для доступа к аккаунту на сервере:
  * Адрес панели управления:
      {$Server.Params.Url|default:'$Server.Params.Url'}
  * Логин:
      {$Login|default:'$Login'}
  * Пароль:
      {$Password|default:'$Password'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

