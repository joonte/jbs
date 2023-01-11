{*
 *  Joonte Billing System
 *  Copyright © 2022 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Обнаружены проблемы в IPMI сервера {$DSServer.Name|default:'$DSServer.Name'}" scope=global}

Уведомляем Вас о том, что в журнале IPMI выделенного сервера {$DSServer.Name|default:'$DSServer.Name'} были обнаружены следующие события:
{$Message|default:'$Message'}


