{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заказ на домен {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'} удален" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на регистрацию домена [{$DomainName|default:'$DomainName'}.{$Name|default:'$Name'}] был удален.
Теперь Вы не являетесь владельцем данного доменного имени. Его в любой момент смогут занять другие лица.

Если этот заказ вам всё ещё необходим, рекомендуем немедленно зарегистрировать домен заново - возможно его ещё не заняли.
Для регистрации домена, воспользуйтесь этой ссылкой:
{$smarty.const.URL_SCHEME|default:'URL_SCHEME'}://{$smarty.const.HOST_ID|default:'HOST_ID'}/DomainWhoIs?DomainName={$DomainName|default:'$DomainName'}.{$Name|default:'$Name'}

---
Обращаем Ваше внимание, что последнее время участились факты фишинговых рассылок с предложением продлить домен, иначе он будет удалён/продан/заблокирован - на что хватает фантазии у создателей рассыки.
Также могут предлагать "регистрацию в поисковых системах", проверку, подтверждение владением и т.п.
В письме содерджится ссылка на оплату, но домен они, в реальности, не продлевают - просто обманывают. Будьте внимательны, проверяйте сайт, на который ведёт ссылка на оплату.

Единственный вариант, когда может быть письмо не от нас - это международные домены, в некоторых зонах требуют подтвердить контактный адрес владельца домена. Бесплатно.

