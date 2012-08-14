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
$Result = Array('Title'=>'Использование ресурсов серверов виртуального хостинга');
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Сервер'),new Tag('TD',Array('class'=>'Head'),'Диск, Gb'),new Tag('TD',Array('class'=>'Head'),'Память, Mb')));
#-------------------------------------------------------------------------------
# выбираем все сервера, пеербираем их
$HostingServers = DB_Select('HostingServers',Array('ID','Address'),Array('SortOn'=>'Address'));
switch(ValueOf($HostingServers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Result;
case 'array':
	$NoBody->AddChild(new Tag('P','Данный вид статистики дает детальную информацию о используемых ресурсах серверов виртуального хостинга'));
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach ($HostingServers as &$HostingServer){
	# считаем использование ресурсов - раму, проц
	$Where = Array();
	$Where[] = "`HostingSchemes`.`ID`=`HostingOrders`.`SchemeID`";
	$Where[] = "`StatusID` = 'Active'";
	$Where[] = SPrintF('`ServerID` = %u',$HostingServer['ID']);
	$HostingResources = DB_Select(Array('HostingOrders','HostingSchemes'),Array('CEIL(SUM(QuotaMEM)) AS tmem','CEIL(SUM(QuotaDisk)/1024) AS tdisk'),Array('Where'=>$Where,'UNIQ'));
	switch(ValueOf($HostingResources)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#Debug(print_r($HostingResources,true));
		$Table[] = Array($HostingServer['Address'],$HostingResources['tdisk'],$HostingResources['tmem']);
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
