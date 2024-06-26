{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Выписаны счета на продление оканчивающихся услуг" scope=global}

В связи со скорым окончанием ранее заказанных вами услуг, для вас были выписаны счета на продление:
{$PaymentLinks|default:'$PaymentLinks'}

Будут оплачены следующие услуги:
{$Items|default:'$Items'}

Если вас не устраивают способы оплаты выписанных счетов, то их можно изменить кликнув на иконку "Изменить счёт", в разделе счетов на оплату биллинговой панели:
{$smarty.const.URL_SCHEME|default:'URL_SCHEME'}://{$smarty.const.HOST_ID|default:'HOST_ID'}/v2/Invoices

Отключить автоматическую выписку счетов, или, сменить метод оплаты для автоматически создаваемых счетов вы можете в биллинговой панели, раздел:
"Настройки" -> "Мои настройки"

