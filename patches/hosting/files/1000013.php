<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$ConfigPath = SPrintF('%s/hosts/%s/config/Config.xml',SYSTEM_PATH,HOST_ID);
#-------------------------------------------------------------------------------
if(File_Exists($ConfigPath)){
	#-------------------------------------------------------------------------------
	$File = IO_Read($ConfigPath);
	if(Is_Error($File))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$XML = String_XML_Parse($File);
	if(Is_Exception($XML))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Config = $XML->ToArray();
	#-------------------------------------------------------------------------------
	$Config = $Config['XML'];
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Config = Array();
	#-------------------------------------------------------------------------------
}
/*
<IspSoft>
 <Name>IspSystems</Name>
  <Settings>
    <VisibleName>ЗАО "ИСПсистем"</VisibleName>
      <Address>my.ispsystem.com</Address>
        <Port>443</Port>
	  <Protocol>ssl</Protocol>
	    <PrefixAPI>/manager/billmgr</PrefixAPI>
	      <Login />
	        <Password />
		  <BalanceLowLimit />
		   </Settings>
		   </IspSoft>
 <IspSoft>
   <Settings>
      <Login>hf_billing</Login>
         <Password>14hII5xl</Password>
	    <BalanceLowLimit type="double">300</BalanceLowLimit>
	      </Settings>
	       </IspSoft>
*/
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(IsSet($Config['IspSoft'])){
	#-------------------------------------------------------------------------------
	$IspSoft = $Config['IspSoft'];
	Debug(SPrintF('[patches/billing/files/1000062.php]: IspSoft = %s',print_r($IspSoft,true)));
	#-------------------------------------------------------------------------------
	UnSet($Config['IspSoft']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(IsSet($IspSoft['Settings']['Password'])){
		#-------------------------------------------------------------------------------
		$ServersGroups = DB_Select('ServersGroups','ID',Array('Where'=>'`ServiceID` = 51000'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($ServersGroups)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			#-------------------------------------------------------------------------------
			foreach($ServersGroups as $ServersGroup){
				#-------------------------------------------------------------------------------
				$IsDelete = DB_Delete('Servers',Array('Where'=>SPrintF('`ServersGroupID` = %u',$ServersGroup['ID'])));
				if(Is_Error($IsDelete))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$IsDelete = DB_Delete('ServersGroups',Array('ID'=>$ServersGroup['ID']));
				if(Is_Error($IsDelete))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$ServersGroup = Array('Name'=>(IsSet($IspSoft['Settings']['VisibleName'])?$IspSoft['Settings']['VisibleName']:'ЗАО "ИСПсистем"'),'ServiceID'=>51000,'FunctionID'=>'NotDefined','Comment'=>'ПО ИСПсистем','SortID'=>51000);
		#-------------------------------------------------------------------------------
		$ServersGroupID = DB_Insert('ServersGroups',$ServersGroup);
		if(Is_Error($ServersGroupID))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Server = Array('TemplateID'=>'ISPsw','ServersGroupID'=>$ServersGroupID,'IsActive'=>TRUE,'IsDefault'=>TRUE,'Protocol'=>'ssl','Address'=>'my.ispsystem.com','Port'=>443,'PrefixAPI'=>'/manager/billmgr','Login'=>(IsSet($IspSoft['Settings']['Login'])?$IspSoft['Settings']['Login']:'root'),'Password'=>$IspSoft['Settings']['Password'],'Params'=>Array('BalanceLowLimit'=>(IsSet($IspSoft['Settings']['BalanceLowLimit'])?$IspSoft['Settings']['BalanceLowLimit']:'250'),'Monitoring'=>'HTTPS=443'),'Notice'=>'Используется специально созданная учётная запись','SortID'=>51000);
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Insert('Servers',$Server);
		if(Is_Error($ServersGroupID))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$File = IO_Write($ConfigPath,To_XML_String($Config),TRUE);
if(Is_Error($File))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsFlush = CacheManager::flush();
if(!$IsFlush)
	@Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
