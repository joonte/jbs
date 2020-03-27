{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заказ на прокси-сервер [{$Host|default:'$Host'}:{$Port|default:'$Port'}] активирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на прокси-сервер был активирован.

  * Адрес подключения:
      {$Host|default:'$Host'}:{$Port|default:'$Port'}
  * Протокол подключения:
      {$ProtocolType|default:'$ProtocolType'}
  * Логин:
      {$Login|default:'$Login'}
  * Пароль:
      {$Password|default:'$Password'}
  *IP адрес с которого будет работать прокси (именно он будет отображаться в логах сайтов на которые вы заходите):
      {$IP|default:'$IP'}

Сохраните эти данные в надежном месте, они потребуются для дальнейшей работы.

