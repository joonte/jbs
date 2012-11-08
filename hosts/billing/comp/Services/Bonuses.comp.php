<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru  */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('DaysRemainded','ServiceID','SchemeID','UserID','CostPay','CostDay');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#Debug("[comp/Services/Bonuses]: DaysRemainded = $DaysRemainded; ServiceID = $ServiceID, SchemeID = $SchemeID, UserID = $UserID, CostPay = $CostPay, CostDay = $CostDay");
#-------------------------------------------------------------------------------
$Bonuses = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
while($DaysRemainded){
	#---------------------------------------------------------------
	$Where = SPrintF('`UserID` = %u AND (`SchemeID` = %u OR ISNULL(`SchemeID`)) AND `DaysRemainded` > 0 AND `ServiceID` = %u',$UserID,$SchemeID,$ServiceID);
	#---------------------------------------------------------------
	$Bonus = DB_Select('Bonuses','*',Array('IsDesc'=>TRUE,'SortOn'=>'Discont','Where'=>$Where));
	#---------------------------------------------------------------
	switch(ValueOf($Bonus)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-----------------------------------------------------------
		$CostPay += $CostDay*$DaysRemainded;
		#-----------------------------------------------------------
		$DaysRemainded = 0;
		break;
	case 'array':
		#-----------------------------------------------------------
		$Bonus = Current($Bonus);
		#-----------------------------------------------------------
		$Discont = (1 - $Bonus['Discont']);
		#-----------------------------------------------------------
		if($Bonus['DaysRemainded'] - $DaysRemainded < 0){
			#---------------------------------------------------------
			$CostPay += $CostDay*$Bonus['DaysRemainded']*$Discont;
			#---------------------------------------------------------
			$UBonus = Array('DaysRemainded'=>0);
			#---------------------------------------------------------
			$DaysRemainded -= $Bonus['DaysRemainded'];
			#---------------------------------------------------------
			$Comp = Comp_Load('Formats/Percent',$Bonus['Discont']);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#---------------------------------------------------------
			$Tr = new Tag('TR');
			#---------------------------------------------------------
			foreach(Array($Bonus['DaysRemainded'],$Comp) as $Text)
				$Tr->AddChild(new Tag('TD',Array('class'=>'Standard','align'=>'right'),$Text));
			#---------------------------------------------------------
			$Bonuses[] = $Tr;
		}else{
			#---------------------------------------------------------
			$CostPay += $CostDay*$DaysRemainded*$Discont;
			#---------------------------------------------------------
			$UBonus = Array('DaysRemainded'=>$Bonus['DaysRemainded'] - $DaysRemainded);
			#---------------------------------------------------------
			$Comp = Comp_Load('Formats/Percent',$Bonus['Discont']);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#---------------------------------------------------------
			$Tr = new Tag('TR');
			#---------------------------------------------------------
			foreach(Array($DaysRemainded,$Comp) as $Text)
				$Tr->AddChild(new Tag('TD',Array('class'=>'Standard','align'=>'right'),$Text));
			#---------------------------------------------------------
			$Bonuses[] = $Tr;
			#---------------------------------------------------------
			$DaysRemainded = 0;
		}
		#-----------------------------------------------------------
		$IsUpdate = DB_Update('Bonuses',$UBonus,Array('ID'=>$Bonus['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array('CostPay'=>$CostPay,'Bonuses'=>$Bonuses);
#-------------------------------------------------------------------------------
return $Out;

?>
