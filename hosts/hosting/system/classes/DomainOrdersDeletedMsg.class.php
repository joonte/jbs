<?php
/**
 *
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 *  Official site: www.joonte.com
 *
 */
 class DomainOrdersDeletedMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainOrdersDeleted', $toUser);

         $this->setParams($params);
     }
 }
