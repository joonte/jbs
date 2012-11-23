<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$OrderID= (integer) @$Args['OrderID'];
$Table	=   (array) @$Args['Table'];
$Code	=  (string) @$Args['Code'];
$UserID	= (integer) @$Args['UserID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsConsiderManage = Permission_Check(SPrintF('%sOrdersConsider',$Code),(integer)$GLOBALS['__USER']['ID'],(integer)$UserID);
#-------------------------------------------------------------------------------
switch(ValueOf($IsConsiderManage)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	# No more...
	break;
case 'true':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OrdersConsider = DB_Select('OrdersConsider','*',Array('Where'=>SPrintF('`OrderID` = %u',$OrderID)));
#-----------------------------------------------------------------------
switch(ValueOf($OrdersConsider)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------
	$Table[] = 'Способ учета';
	#-------------------------------------------------------------------
	$Row = Array();
	#-------------------------------------------------------------------
	foreach(Array('Дн. зарез.','Дн. ост.','Дн. не учт.','Цена','Скидка') as $Text)
		$Row[] = new Tag('TD',Array('class'=>'Head'),$Text);
	#-------------------------------------------------------------------
	$Rows = Array($Row);
	#-------------------------------------------------------------------
	$RemainderSumm = 0.00;
	#-------------------------------------------------------------------
	for($i=0;$i<Count($OrdersConsider);$i++){
		#-----------------------------------------------------------------
		$ConsiderItem = $OrdersConsider[$i];
		#-----------------------------------------------------------------
		$Comp = Comp_Load('Formats/Percent',$ConsiderItem['Discont']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-----------------------------------------------------------------
		$Row = Array();
		#-----------------------------------------------------------------
		if($IsConsiderManage){
			#---------------------------------------------------------------
			foreach(Array('DaysReserved','DaysRemainded','DaysConsidered','Cost','Discont') as $ParamID){
				#-------------------------------------------------------------
				$Comp = Comp_Load(
							'Form/Input',
							Array(
								'type'  => 'text',
								'name'  => SPrintF('OrdersConsider[%u][]',$i),
								'size'  => 6,
								'value' => $ConsiderItem[$ParamID]
								)
						);
				if(Is_Error($Comp))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------
				$Row[] = new Tag('TD',$Comp);
			}
		}else{
			#---------------------------------------------------------------
			$Row[] = (integer)$ConsiderItem['DaysReserved'];
			$Row[] = (integer)$ConsiderItem['DaysRemainded'];
			$Row[] = (integer)$ConsiderItem['DaysConsidered'];
			$Row[] = (float)$ConsiderItem['Cost'];
			$Row[] = $Comp;
		}
		#-----------------------------------------------------------------
		$RemainderSumm += (float)$ConsiderItem['Cost']*(integer)$ConsiderItem['DaysRemainded']*(1 - (float)$ConsiderItem['Discont']);
		#-----------------------------------------------------------------
		$Rows[] = $Row;
	}
	#-------------------------------------------------------------------
	#$Comp = Comp_Load('Tables/Extended',$Rows,'Способ учета');
	$Comp = Comp_Load('Tables/Extended',$Rows);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------
	$Table[] = new Tag('DIV',Array('align'=>'center'),$Comp);
	#-------------------------------------------------------------------
	if($IsConsiderManage){
		#-----------------------------------------------------------------
		$Comp = Comp_Load(
					'Form/Input',
					Array(
						'type'    => 'button',
						'onclick' => "AjaxCall('/Administrator/API/OrderConsider',FormGet(form),'Сохрание способа учета','GetURL(document.location);');",
						'value'   => 'Сохранить'
					)
				);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-----------------------------------------------------------------
		$Div = new Tag('DIV',Array('align'=>'right'),$Comp);
		#-----------------------------------------------------------------
		if($RemainderSumm){
			#---------------------------------------------------------------
			$Comp = Comp_Load('Formats/Currency',$RemainderSumm);
			if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
			#---------------------------------------------------------------
			$Comp = Comp_Load(
					'Form/Input',
					Array(
						'type'    => 'button',
						'onclick' => "javascript:ShowConfirm('Вы действительно хотите осуществить возврат средств?','AjaxCall(\'/Administrator/API/OrderRestore\',FormGet(OrderInfo),\'Возвращение денег\',\'GetURL(document.location);\');');",
						'value'   => SPrintF('Вернуть %s',$Comp)
						)
					);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#---------------------------------------------------------------
			$Div->AddChild($Comp);
		}
		#-----------------------------------------------------------------
		$Table[] = $Div;
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return $Table;
#-------------------------------------------------------------------------------


?>
