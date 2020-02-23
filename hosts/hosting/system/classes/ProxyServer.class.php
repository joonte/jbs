<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
#-------------------------------------------------------------------------------
class ProxyServer{
	#-------------------------------------------------------------------------------
	# Тип системы сервера
	public $SystemID = 'Default';
	#-------------------------------------------------------------------------------
	# Параметры связи с сервером
	public $Settings = Array();
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function Select($ServerID){
		/******************************************************************************/
		$__args_types = Array('integer');
		#-------------------------------------------------------------------------------
		$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
		/******************************************************************************/
			$Settings = DB_Select('Servers','*',Array('UNIQ','ID'=>$ServerID));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Settings)){
		case 'error':
			return ERROR | @Trigger_Error('[ProxyServer->Select]: не удалось выбрать сервер');
		case 'exception':
			return new gException('SERVER_NOT_FOUND','Указаный сервер не найден');
		case 'array':
			#-------------------------------------------------------------------------------
			$this->SystemID = $Settings['Params']['SystemID'];
			#-------------------------------------------------------------------------------
			$this->Settings = $Settings;
			#-------------------------------------------------------------------------------
			if(Is_Error(System_Load(SPrintF('libs/%s.php',$this->SystemID))))
				@Trigger_Error('[ProxyServer->Select]: не удалось загрузить целевую библиотеку');
				#-------------------------------------------------------------------------------
			return TRUE;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function Logon(){
		/******************************************************************************/
		$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
		/******************************************************************************/
		Array_UnShift($__args__,$this->Settings);
		#-------------------------------------------------------------------------------
		$Function = SPrintF('%s_Logon',$this->SystemID);
		#-------------------------------------------------------------------------------
		if(!Function_Exists($Function))
			return new gException('FUNCTION_NOT_SUPPORTED',SPrintF('Функция (%s) не поддерживается API модулем',$Function));
		#-------------------------------------------------------------------------------
		$Result = Call_User_Func_Array($Function,$__args__);
		if(Is_Error($Result))
			return ERROR | @Trigger_Error('[ProxyServer->UserLogin]: не удалось вызвать целевую функцию');
		#-------------------------------------------------------------------------------
		return $Result;
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function GetUsers(){
		/******************************************************************************/
		$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
		/******************************************************************************/
		Array_UnShift($__args__,$this->Settings);
		#-------------------------------------------------------------------------------
		$Function = SPrintF('%s_Get_Users',$this->SystemID);
		#-------------------------------------------------------------------------------
		if(!Function_Exists($Function))
			return new gException('FUNCTION_NOT_SUPPORTED',SPrintF('Функция (%s) не поддерживается API модулем',$Function));
		#-------------------------------------------------------------------------------
		$Result = Call_User_Func_Array($Function,$__args__);
		if(Is_Error($Result))
			return ERROR | @Trigger_Error('[ProxyServer->GetUsers]: не удалось вызвать целевую функцию');
		#-------------------------------------------------------------------------------
		return $Result;
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function Create(){
		/******************************************************************************/
		$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
		/******************************************************************************/
		Array_UnShift($__args__,$this->Settings);
		#-------------------------------------------------------------------------------
		$Function = SPrintF('%s_Create',$this->SystemID);
		#-------------------------------------------------------------------------------
		if(!Function_Exists($Function))
			return new gException('FUNCTION_NOT_SUPPORTED',SPrintF('Функция (%s) не поддерживается API модулем',$Function));
		#-------------------------------------------------------------------------------
		$Result = Call_User_Func_Array($Function,$__args__);
		if(Is_Error($Result))
			return ERROR | @Trigger_Error('[ProxyServer->Create]: не удалось вызвать целевую функцию');
		#-------------------------------------------------------------------------------
		return $Result;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function Delete(){
		/******************************************************************************/
		$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
		/******************************************************************************/
		Array_UnShift($__args__,$this->Settings);
		#-------------------------------------------------------------------------------
		$Function = SPrintF('%s_Delete',$this->SystemID);
		#-------------------------------------------------------------------------------
		if(!Function_Exists($Function))
			return new gException('FUNCTION_NOT_SUPPORTED',SPrintF('Функция (%s) не поддерживается API модулем',$Function));
		#-------------------------------------------------------------------------------
		$Result = Call_User_Func_Array($Function,$__args__);
		if(Is_Error($Result))
			return ERROR | @Trigger_Error('[ProxyServer->Delete]: не удалось вызвать целевую функцию');
		#-------------------------------------------------------------------------------
		return $Result;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function SettingsChange(){
		/******************************************************************************/
		$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
		/******************************************************************************/
		Array_UnShift($__args__,$this->Settings);
		#-------------------------------------------------------------------------------
		$Function = SPrintF('%s_Settings_Change',$this->SystemID);
		#-------------------------------------------------------------------------------
		if(!Function_Exists($Function))
			return new gException('FUNCTION_NOT_SUPPORTED',SPrintF('Функция (%s) не поддерживается API модулем',$Function));
		#-------------------------------------------------------------------------------
		$Result = Call_User_Func_Array($Function,$__args__);
		if(Is_Error($Result))
			return ERROR | @Trigger_Error('[ProxyServer->SettingsChange]: не удалось вызвать целевую функцию');
		#-------------------------------------------------------------------------------
		return $Result;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	public function GetBalance(){
		/******************************************************************************/
		$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
		/******************************************************************************/
		Array_UnShift($__args__,$this->Settings);
		#-------------------------------------------------------------------------------
		Debug('[system/classes/ProxyServer.class.php]: function GetBalance');
		#-------------------------------------------------------------------------------
		$Function = SPrintF('%s_Get_Balance',$this->SystemID);
		#-------------------------------------------------------------------------------
		if(!Function_Exists($Function))
			return new gException('FUNCTION_NOT_SUPPORTED',SPrintF('Функция (%s) не поддерживается API модулем',$Function));
		#-------------------------------------------------------------------------------
		$Result = Call_User_Func_Array($Function,$__args__);
		if(Is_Error($Result))
			return ERROR | @Trigger_Error('[DomainServer->GetBalance]: не удалось вызвать целевую функцию');
		#-------------------------------------------------------------------------------
		return $Result;
		#-------------------------------------------------------------------------------
	}

}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
