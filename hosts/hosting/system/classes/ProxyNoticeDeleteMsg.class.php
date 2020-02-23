<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2020, Alex Keda for www.host-food.ru
 *
 */
 class ProxyNoticeDeleteMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('ProxyNoticeDelete', $toUser);

         $this->setParams($params);
     }
 }
