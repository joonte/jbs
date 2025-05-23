<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Contracts = DB_Select('Contracts','ID');
#-------------------------------------------------------------------------------
switch(ValueOf($Contracts)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	$CurrentMonth = (Date('Y') - 1970)*12 + (integer)Date('n');
	#-------------------------------------------------------------------------------
	$Count = 0;	// счётчик для sleep()
	#-------------------------------------------------------------------------------
	foreach($Contracts as $Contract){
		#-------------------------------------------------------------------------------
		$Columns = Array('CreateDate','ContractID','Month','ServiceID','Comment','SUM(`Amount`) as `Amount`','Cost','Discont');
		#-------------------------------------------------------------------------------
		$WorksComplite = DB_Select('WorksComplite',$Columns,Array('GroupBy'=>Array('ServiceID','Month','Comment','Cost','Discont'),'Where'=>SPrintF('`ContractID` = %u',$Contract['ID'],$CurrentMonth)));
		#-------------------------------------------------------------------------------
		switch(ValueOf($WorksComplite)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# No more...
			break;
		case 'array':
			#-------------------------------------------------------------------------------
			#-----------------------------TRANSACTION---------------------------------------
			if(Is_Error(DB_Transaction($TransactionID = UniqID('WorksComplite'))))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$IsDelete = DB_Delete('WorksComplite',Array('Where'=>SPrintF('`ContractID` = %u',$Contract['ID'])));
			if(Is_Error($IsDelete))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			foreach($WorksComplite as $WorkComplite){
				#-------------------------------------------------------------------------------
				$IsInsert = DB_Insert('WorksComplite',$WorkComplite);
				if(Is_Error($IsInsert))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			if(Is_Error(DB_Commit($TransactionID)))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		$Count++;
		#-------------------------------------------------------------------------------
		if($Count == 1000){
			#-------------------------------------------------------------------------------
			Sleep(1);
			#-------------------------------------------------------------------------------
			$Count = 0;	// обнуляем счётчик
			#-------------------------------------------------------------------------------
		}
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
return 604800;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
