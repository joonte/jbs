<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class HostingNoticeDeleteMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('HostingNoticeDelete', $toUser);

         $this->setParams($params);
     }
 }