<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('IsCreate','Folder','StartDate','FinishDate','Details');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Result = Array('Title'=>'Использование ресурсов серверов VPS');
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Сервер'),new Tag('TD',Array('class'=>'Head'),'Процессор, MHz'),new Tag('TD',Array('class'=>'Head'),'Память, Mb')));
#-------------------------------------------------------------------------------
# выбираем все сервера, пеербираем их
$VPSServers = DB_Select('VPSServers',Array('ID','Address'));
switch(ValueOf($VPSServers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Result;
case 'array':
	$NoBody->AddChild(new Tag('P','Данный вид статистики дает детальную информацию о используемых ресурсах серверов VPS'));
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach ($VPSServers as &$VPSServer){
	# считаем использование ресурсов - раму, проц
	$Where = Array();
	$Where[] = "`VPSSchemes`.`ID`=`VPSOrders`.`SchemeID`";
	$Where[] = "`StatusID`='Active'";
	$Where[] = SPrintF('`ServerID`=%u',$VPSServer['ID']);
	$VPSResources = DB_Select(Array('VPSOrders','VPSSchemes'),Array('SUM(mem) AS tmem','SUM(ncpu * cpu) AS tcpu'),Array('Where'=>$Where,'UNIQ'));
	switch(ValueOf($VPSResources)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#Debug(print_r($VPSResources,true));
		$Table[] = Array($VPSServer['Address'],$VPSResources['tcpu'],$VPSResources['tmem']);
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
	#---------------------------------------------------------------------------
	#---------------------------------------------------------------------------
	$Comp = Comp_Load('Tables/Extended',$Table);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#---------------------------------------------------------------------------
	$NoBody->AddChild($Comp);

#---------------------------------------------------------------------------
#---------------------------------------------------------------------------
$Result['DOM'] = $NoBody;
#---------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------

?>
