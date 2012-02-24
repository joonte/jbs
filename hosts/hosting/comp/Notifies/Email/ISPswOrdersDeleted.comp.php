<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Replace');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Replace['ISPswOrder'] = $Replace['Row'];
#-------------------------------------------------------------------------------
$ISPswOrder = &$Replace['ISPswOrder'];
#-------------------------------------------------------------------------------
$StatusDate = Comp_Load('Formats/Date/Standard',$ISPswOrder['StatusDate']);
if(Is_Error($StatusDate))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ISPswOrder['StatusDate'] = $StatusDate;
#-------------------------------------------------------------------------------
$Number = Comp_Load('Formats/Order/Number',$ISPswOrder['OrderID']);
if(Is_Error($Number))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ISPswOrder['Number'] = $Number;
#-------------------------------------------------------------------------------
$Message = <<<EOT
Здравствуйте, %User.Name%!

Уведомляем Вас о том, что %ISPswOrder.StatusDate% Ваш заказ №%ISPswOrder.Number% на ПО ISPsystem, IP адрес %ISPswOrder.IP%, был удален.

%From.Sign%
EOT;
#-------------------------------------------------------------------------------
$Replace = Array_ToLine($Replace,'%');
#-------------------------------------------------------------------------------
foreach(Array_Keys($Replace) as $Key)
  $Message = Str_Replace($Key,$Replace[$Key],$Message);
#-------------------------------------------------------------------------------
return Array('Theme'=>'Заказ программного обеспечения удален','Message'=>$Message);
#-------------------------------------------------------------------------------

?>
