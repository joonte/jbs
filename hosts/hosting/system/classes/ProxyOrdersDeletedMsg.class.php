<?php
/**
 *
 *  Joonte Billing System
 *
 *  Alex Keda, for www.host-food.ru
 *
 */
 class ProxyOrdersDeletedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('ProxyOrdersDeleted', $toUser);

         $this->setParams($params);
     }
 }
