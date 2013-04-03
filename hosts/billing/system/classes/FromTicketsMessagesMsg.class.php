<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class FromTicketsMessagesMsg extends Message {
     public function __construct(array $params, $toUser, $fromUser = 100) {
         parent::__construct('FromTicketsMessages', $toUser, $params, $fromUser);
     }

    public function getParams() {
        #$Message = Preg_Replace("#\[hidden\](.+)\[/hidden\]#sU", ' ', $this->params['Message'], -1);

	$Message = Comp_Load('Edesks/Text',Array('String'=>$this->params['Message'],'IsEmail'=>TRUE));
	if(Is_Error($Message))
		return ERROR | @Trigger_Error(500);

	$this->params['Message'] = $Message;

        return $this->params;
    }
 }
