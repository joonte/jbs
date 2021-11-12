<?php

#-------------------------------------------------------------------------------
/** @author Sergey N. Sedov (HOST-FOOD) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$IsCreate	= (boolean) @$Args['IsCreate'];
$StartDate	= (integer) @$Args['StartDate'];
$FinishDate	= (integer) @$Args['FinishDate'];
$Details	=   (array) @$Args['Details'];
$ShowTables	= (boolean) @$Args['ShowTables'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Result = Array('Title'=>'Акты выполненных работ юр. лиц*');
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
if(!$IsCreate)
  return $Result;
#-------------------------------------------------------------------------------
$StartMonth = (integer)((Date("Y",$StartDate) - 1970)*12 + Date("n",$StartDate));
$FinishMonth = (integer)((Date("Y",$FinishDate) - 1970)*12 + Date("n",$FinishDate));
#-------------------------------------------------------------------------------
$Columns = Array('`WorksComplite`.`ContractID`','`Contracts`.`ProfileID`','SUM(`WorksComplite`.`Amount` * `WorksComplite`.`Cost` * (1 - `WorksComplite`.`Discont`)) AS Summ' ,'`WorksComplite`.`Month`');
#-------------------------------------------------------------------------------
$Where = SPrintF('`WorksComplite`.`ContractID` = `Contracts`.`ID` AND `WorksComplite`.`Month` BETWEEN \'%s\' AND \'%s\' AND `Contracts`.`TypeID` = \'Juridical\'',$StartMonth,$FinishMonth);
#-------------------------------------------------------------------------------
$Contracts = DB_Select(Array('WorksComplite','Contracts'),$Columns,Array('Where'=>$Where,'SortOn'=>'`WorksComplite`.`Month`','GroupBy'=>Array('WorksComplite`.`ContractID','Month')));
#-------------------------------------------------------------------------------
switch(ValueOf($Contracts)){
 case 'error':
   return ERROR | @Trigger_Error(500);
 case 'exception':
   return $Result;
 case 'array':
   #----------------------------------------------------------------------------
   $NoBody->AddChild(new Tag('P','Данный вид статистики содержит информацию об актах выполненных услуг сформированных биллингом для юр. лиц.'));
   #----------------------------------------------------------------------------
   #$NoBody->AddChild(new Tag('P',SPrintF('Месяц начала %s',$StartMonth)));
   #$NoBody->AddChild(new Tag('P',SPrintF('Месяц конца %s',$FinishMonth)));
   #----------------------------------------------------------------------------
   $Table = Array(Array(new Tag('TD',Array('class'=>'Head'),'Наименование юр. лица'),new Tag('TD',Array('class'=>'Head'),'ИНН'),new Tag('TD',Array('class'=>'Head'),'Дата создания акта'),new Tag('TD',Array('class'=>'Head'),'Сумма выполенных работ'),new Tag('TD',Array('class'=>'Head'),'Номер акта'),new Tag('TD',Array('class'=>'Head'),'Номер договора')));
   #----------------------------------------------------------------------------
   foreach($Contracts as $Contract){
   $Profile = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>$Contract['ProfileID']));
    #--------------------------------------------------------------------------
     $Row = Array(new Tag('TD',Array('class'=>'Standard'),$Profile['Attribs']['CompanyName']));
     #--------------------------------------------------------------------------
     $Row[] = $Profile['Attribs']['Inn'];
     #--------------------------------------------------------------------------
     $Config = Config();
     $ReportDate = $Config['Executor']['WorksCompliteDate'];
     $Year = (integer)Floor($Contract['Month']/12) + 1970;
     $Month = (integer)Fmod($Contract['Month'],12) + 1;
     $Comp = Comp_Load('Formats/Date/Standard',MkTime(0,0,0,$Month,$ReportDate,$Year));
     if(Is_Error($Comp))
       return ERROR | @Trigger_Error(500);
     #--------------------------------------------------------------------------
     $Row[] = $Comp;
     #--------------------------------------------------------------------------
     $Comp = Comp_Load('Formats/Currency',$Contract['Summ']);
     if(Is_Error($Comp))
       return ERROR | @Trigger_Error(500);
     #--------------------------------------------------------------------------
     $Row[] = $Comp;
     #--------------------------------------------------------------------------
     $Comp = Comp_Load('Formats/WorkComplite/Report/Number',$Contract['ContractID'],$Contract['Month']);
     if(Is_Error($Comp))
       return ERROR | @Trigger_Error(500);
     #--------------------------------------------------------------------------
     $Row[] = $Comp;
     #--------------------------------------------------------------------------
     $Row[] = $Contract['ContractID'];
     #--------------------------------------------------------------------------
     $Table[] = $Row;
   }
   #----------------------------------------------------------------------------
   $Comp = Comp_Load('Tables/Extended',$Table);
   if(Is_Error($Comp))
     return ERROR | @Trigger_Error(500);
   #----------------------------------------------------------------------------
   if($ShowTables)
	   $NoBody->AddChild($Comp);
   #----------------------------------------------------------------------------
   $Result['DOM'] = $NoBody;
   #----------------------------------------------------------------------------
   return $Result;
 default:
   return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
