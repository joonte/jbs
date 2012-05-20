<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class FromTicketsMessages extends Message {
     public function __construct(array $params, $toUser, $fromUser = 100) {
         parent::__construct('FromTicketsMessages', $toUser, $params, $fromUser);
     }

    public function getParams() {
        $Message = Preg_Replace("#\[hidden\](.+)\[/hidden\]#sU", ' ', $this->params('Message'), -1);

        $this->params['Message'] = $Message;

        return $this->params;
    }
 }