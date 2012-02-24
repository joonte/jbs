<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Wsdl = System_Element('config/Wsdl/IShopClientWS.wsdl');

if(Is_Error($Wsdl))
	return ERROR | @Trigger_Error(500);
	
$s = new SoapServer($Wsdl, array('classmap' => array('tns:updateBill' => 'Param', 'tns:updateBillResponse' => 'Response')));
$s->setClass('TestServer');
$s->handle();

class Response {
	public $updateBillResult;
}

class Param {
	public $login;
	public $password;
	public $txn;      
	public $status;
}

class TestServer {
	function updateBill($param) {
		$f = fopen('c:\\phpdump.txt', 'w');
		
		fwrite($f, $param->login);
		fwrite($f, ', ');
		fwrite($f, $param->password);
		fwrite($f, ', ');
		fwrite($f, $param->txn);
		fwrite($f, ', ');
		fwrite($f, $param->status);
		fclose($f);
		
		Debug("TEST QIWI!");
		Debug(print_r($param));

		$temp = new Response();
		$temp->updateBillResult = -1;
		
		return $temp;
	}
}
?>
