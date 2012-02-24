<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$TaskID = (integer) @$Args['TaskID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Task = DB_Select('Tasks','*',Array('UNIQ','ID'=>$TaskID));
#-------------------------------------------------------------------------------
switch(ValueOf($Task)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('TASK_NOT_FOUND','Задача не найдена');
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('TaskRead',(integer)$__USER['ID'],(integer)$Task['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $DOM = new DOM();
        #-----------------------------------------------------------------------
        $Links = &Links();
        # Коллекция ссылок
        $Links['DOM'] = &$DOM;
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Load('Window')))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddText('Title','Задача системы');
        #-----------------------------------------------------------------------
        $Table = Array('Общая информация');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Task/Number',$Task['ID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Номер',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$Task['CreateDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата создания',$Comp);
        #-----------------------------------------------------------------------
        $TypeID = $Task['TypeID'];
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Task/Type',$TypeID);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Тип',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$Task['ExecuteDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата выполнения',$Comp);
        #-----------------------------------------------------------------------
        $Config = Config();
        #-----------------------------------------------------------------------
        $Type = $Config['Tasks']['Types'][$TypeID];
        #-----------------------------------------------------------------------
        $Params = $Task['Params'];
        #-----------------------------------------------------------------------
        if(Count($Params)){
          #---------------------------------------------------------------------
          $Table[] = 'Параметры';
          #---------------------------------------------------------------------
          foreach(Array_Keys($Params) as $ParamID){
            if(Is_String($Params[$ParamID])){
              #-------------------------------------------------------------------
              $Text = Str_Replace("\n",'<BR />',HtmlSpecialChars($Params[$ParamID]));
              #-------------------------------------------------------------------
              $Text = Preg_Replace('/(http\:\/\/[a-zA-Z0-9\/\:\?\&\=\@\-\.\;]+)/','<A href="\\1">[ссылка]</A>',$Text);
              #-------------------------------------------------------------------
              $Td = new Tag('TD',Array('class'=>'Standard','style'=>'max-width:400px;'));
              #-------------------------------------------------------------------
              $Td->AddHTML(SPrintF('<SPAN>%s</SPAN>',$Text));
              #-------------------------------------------------------------------
              $Table[] = Array($Type['Params'][$ParamID],$Td);
            }
          }
        }
        #-----------------------------------------------------------------------
        $Table[] = 'Текущее состояние';
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Logic',$Task['IsExecuted']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Выполнено',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Standard',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$Comp);
        #-----------------------------------------------------------------------
        if(Is_Error($DOM->Build(FALSE)))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok','DOM'=>$DOM->Object);
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
