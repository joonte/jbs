{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заказ программного обеспечения активирован" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на ПО ISPsystems был активирован.

IP адрес лицензии: {$IP|default:'$IP'}

Для активации лицензии 5-й версии, используйте следующую команду:
wget -O /usr/local/mgr5/etc/ispmgr.lic "http://lic.ispsystem.com/ispmgr.lic?ip={$IP|default:'$IP'}"
Для активации лицензии 4-й версии, используйте следующую команду:
wget -O /usr/local/ispmgr/etc/ispmgr.lic "http://lic.ispsystem.com/ispmgr.lic?ip={$IP|default:'$IP'}"
(обращаем ваше внимнаие, что 4 версия уже не поддерживается и уязвимости в ней не исправляются)

Для установки панели 5 версии выполните следующие команды:
wget http://download.ispmanager.com/install.5.sh
sh install.5.sh
И следуйте инструкциям установщика

Панель 6-й версии работает с лицензией 5-й версии до версии 6.96.1, c ограничением в 10 доменов
Для её установки, она (6-5.391) должна быть прописана в файлах /etc/yum.repos.d/ispsystem.repo и /etc/yum.repos.d/exosoft.repo (для RedHat-образных)

Учтите, если вы заказали лицензию четвёртой версии, то установку программного обеспечения вам необходимо произвести самостоятельно.

При заказе лицензии для виртуального сервера с шаблоном ISPmanager, вы можете сразу же пользоваться лицензией, пройдя по адресу:
{$smarty.const.URL_SCHEME|default:'URL_SCHEME'}://{$IP|default:'$IP'}:1500/ispmgr

