<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class WorksCompliteReportMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('WorksCompliteReport', $toUser);

         $this->setParams($params);
     }
 }