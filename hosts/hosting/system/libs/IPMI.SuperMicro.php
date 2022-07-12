<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
#-------------------------------------------------------------------------------

function IPMI_PowerGet($Scheme){
	#-------------------------------------------------------------------------------
	$Out = IPMI_Execute($Scheme,'chassis power status');
	#-------------------------------------------------------------------------------
	if($Out){
		#-------------------------------------------------------------------------------
		foreach($Out as $Line)
			if(Preg_Match('/Power\sis\son/i',$Line))
				return 'on';
		#-------------------------------------------------------------------------------
		return 'off';
		#return (Preg_Match('/Power\sis\son/i',$Out[0]))?'on':'off';
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		return new gException('[IPMI_PowerGet]','Произошла ошибка при получении статуса сервера, возможно IPMI контроллер перезагружается. Подождите две минуты, если проблема повторится - обратитесь в техническую поддержку');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function IPMI_Command($Scheme,$Command){
	#-------------------------------------------------------------------------------
	$Out = IPMI_Execute($Scheme,$Command);
	#-------------------------------------------------------------------------------
	if(!$Out)
		return new gException('[IPMI_Command]','Произошла ошибка при выполнении команды, подождите две минуты, если проблема повторится - обратитесь в техническую поддержку');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# внутренняя функция
function IPMI_Execute($Scheme,$Command){
	#-------------------------------------------------------------------------------
	#ipmitool -c -I lanplus -H bld2-ds01-IPMI.test-hf.su -U ADMIN -P XXXXXX chassis status
	#-------------------------------------------------------------------------------
	// достаём имя сервера
	$ParseUrl = Parse_Url($Scheme['ILOaddr']);
	$Address = $ParseUrl['host'];
	#-------------------------------------------------------------------------------
	$Line = SPrintF('ipmitool -c -I lanplus -H %s -U %s -P %s %s',$Address,$Scheme['ILOuser'],$Scheme['ILOpass'],$Command);
	#-------------------------------------------------------------------------------
	//Debug(SPrintF('[system/libs/IPMI.SuperMicro.php]: выполняем: %s',$Line));
	// выполянем команду
	Exec(SPrintF("%s 2>&1",$Line),$Out,$ReturnValue);
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[IPMI_Execute]: exec return code = %s, Out = %s',$ReturnValue,print_r($Out,true)));
	#-------------------------------------------------------------------------------
	if($ReturnValue != 0)
		return FALSE;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// вертаем выхлоп
	return $Out;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}