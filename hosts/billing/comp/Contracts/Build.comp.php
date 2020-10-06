<?php

#-------------------------------------------------------------------------------
/** @author Alex keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ContractID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DOM.class.php','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','CreateDate','TypeID','ProfileID'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$ProfileID = (integer)$Contract['ProfileID'];
#-------------------------------------------------------------------------------
if(!$ProfileID)
	return TRUE;
#-------------------------------------------------------------------------------
$ContractID = (integer)$Contract['ID'];
#-------------------------------------------------------------------------------
$Replace = Array('MotionDocumentID'=>'NO');
#-------------------------------------------------------------------------------
$UniqID = SPrintF('Contract:%u',$ContractID);
#-------------------------------------------------------------------------------
$MotionDocument = DB_Select('MotionDocuments','ID',Array('UNIQ','Where'=>SPrintF("`ContractID` = %u AND `TypeID` = 'Contract' AND `UniqID` = '%s'",$ContractID,$UniqID)));
#-------------------------------------------------------------------------------
switch(ValueOf($MotionDocument)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	$Config = Config();
	#-------------------------------------------------------------------------------
	$Type = $Config['Contracts']['Types'][$Contract['TypeID']];
	#-------------------------------------------------------------------------------
	if(!$Type['IsUsedMotionDocuments'])
		break;
	#-------------------------------------------------------------------------------
	$MotionDocument = Comp_Load('www/Administrator/API/MotionDocumentEdit',Array('TypeID'=>'Contract','ContractID'=>$ContractID,'AjaxCall'=>Array('Url'=>'/ContractDownload','Args'=>Array('ContractID'=>$ContractID)),'UniqID'=>$UniqID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($MotionDocument)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		# No more...
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/MotionDocument/Number',$MotionDocument['ID']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(400);
	#-------------------------------------------------------------------------------
	$Replace['MotionDocumentID'] = $Comp;
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Customer = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>$ProfileID));
#-------------------------------------------------------------------------------
switch(ValueOf($Customer)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Replace['Customer'] = $Customer['Attribs'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Contract/Number',$ContractID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract['Number'] = $Comp;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Standard',$Contract['CreateDate']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract['CreateDate'] = $Comp;
#-------------------------------------------------------------------------------
$Executor = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>100));
#-------------------------------------------------------------------------------
switch(ValueOf($Executor)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TRUE;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Clauses/Load',SPrintF('Contracts/Types/%s/Template',$Executor['TemplateID']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM = new DOM($Comp['DOM']);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Clauses/Load','Contracts/Content');
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Content',$Comp['DOM']);
#-------------------------------------------------------------------------------
$Replace['Executor'] = $Executor['Attribs'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Clauses/Load',SPrintF('Contracts/Types/%s/Agreement/%s',$Contract['TypeID'],$Executor['TemplateID']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Agreement',$Comp['DOM']);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Clauses/Load',SPrintF('Contracts/Types/%s/Customer',$Contract['TypeID']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Customer',$Comp['DOM']);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Clauses/Load',SPrintF('Contracts/Types/%s/Footer/%s',$Contract['TypeID'],$Executor['TemplateID']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Footer',$Comp['DOM']);
#-------------------------------------------------------------------------------
$Replace['Contract'] = $Contract;
#-------------------------------------------------------------------------------
$Replace['SignDate'] = $Contract['CreateDate'];
#-------------------------------------------------------------------------------
$Hrs = $DOM->GetByTagName('HR');
#-------------------------------------------------------------------------------
for($i=0;$i<Count($Hrs);$i++){
	#-------------------------------------------------------------------------------
	$Hr = &$Hrs[$i];
	#-------------------------------------------------------------------------------
	$Hr->AddAttribs(Array('BREAK'=>'true'));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Document = $DOM->Build();
if(Is_Error($Document))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Replace = Array_ToLine($Replace);
#-------------------------------------------------------------------------------
foreach(Array_Keys($Replace) as $LinkID){
	#-------------------------------------------------------------------------------
	$Text = (string)$Replace[$LinkID];
	#-------------------------------------------------------------------------------
	$Document = Str_Replace(SPrintF('%%%s%%',$LinkID),$Text?$Text:'-',$Document);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#------------------------TRANSACTION--------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('ContractBuld'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('Contracts',Array('Customer'=>$Customer['Name']),Array('ID'=>$ContractID));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём все файлы и удаляем
$Files = GetUploadedFilesInfo('Contracts',$ContractID);
#-------------------------------------------------------------------------------
foreach($Files as $File)
	if(!DeleteUploadedFile($File['ID']))
		return new gException('CANNOT_DELETE_FILE','Не удалось удалить связанный файл');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!SaveUploadedFile(Array(Array('Data'=>$Document,'Name'=>SPrintF('Contract%s.html',$ContractID),'Size'=>Mb_StrLen($Document,'8bit'),'Mime'=>'text/html')),'Contracts',$ContractID))
	return new gException('CANNOT_SAVE_UPLOADED_FILE','Не удалось сохранить загруженный файл');
#-------------------------------------------------------------------------------
$ContractsEnclosures = DB_Select('ContractsEnclosures','ID',Array('Where'=>SPrintF('`ContractID` = %u',$ContractID)));
#-------------------------------------------------------------------------------
switch(ValueOf($ContractsEnclosures)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($ContractsEnclosures as $ContractEnclosure){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Contracts/Enclosures/Build',$ContractEnclosure['ID']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(100);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Invoices = DB_Select('Invoices','ID',Array('Where'=>SPrintF("`ContractID` = %u AND `StatusID` IN ('Waiting','Conditionally')",$ContractID)));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoices)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Invoices as $Invoice){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Invoices/Build',$Invoice['ID']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(100);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-----------------------END TRANSACTION-----------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
