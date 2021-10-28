<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *
 */

class VPSOrdersSuspendedMsg extends Message {
	#-------------------------------------------------------------------------------
	public function __construct(array $params,$toUser) {
		#-------------------------------------------------------------------------------
		parent::__construct('VPSOrdersSuspended',$toUser,$params);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function getParams() {
		#-------------------------------------------------------------------------------
		$VPSScheme = DB_Select('VPSSchemes', Array('*'), Array('UNIQ', 'Where' => SPrintF('`ID` = %u',$this->params['SchemeID'])));
		#-------------------------------------------------------------------------------
		if(!Is_Array($VPSScheme))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$this->params['VPSScheme'] = $VPSScheme;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// ссылка на продление заказа
		$Ajax = SPrintF("ShowWindow('/VPSOrderPay',{VPSOrderID:'%s'});",$this->params['ID']);
		#-------------------------------------------------------------------------------
		$ProlongLink = Comp_Load('Formats/System/EvalLink',$Ajax);
		if(Is_Error($ProlongLink))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$this->params['ProlongLink'] = $ProlongLink;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// ссылка на смену тарифа
		$Ajax = SPrintF("ShowWindow('/VPSOrderSchemeChange',{VPSOrderID:'%s'});",$this->params['ID']);
		#-------------------------------------------------------------------------------
		$SchemeChangeLink = Comp_Load('Formats/System/EvalLink',$Ajax);
		if(Is_Error($SchemeChangeLink))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$this->params['SchemeChangeLink'] = $SchemeChangeLink;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return $this->params;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}

