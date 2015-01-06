{assign var=ExpDate value=$VPSOrder.StatusDate + $Config.Tasks.Types.VPSForDelete.DeleteTimeout * 24 * 3600}
Оканчивается срок блокировки Вашего заказа #{$VPSOrder.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) IP адрес: {$VPSOrder.Login|default:'$VPSOrder.Login'}
Дата удаления заказа {$ExpDate|date_format:"%d.%m.%Y"}
