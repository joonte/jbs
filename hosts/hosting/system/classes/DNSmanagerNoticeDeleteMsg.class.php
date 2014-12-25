<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DNSmanagerNoticeDeleteMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DNSmanagerNoticeDelete', $toUser);

         $this->setParams($params);
     }
 }
