
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DOM.class','modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Main')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Требования');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/Features.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Permission = FALSE;
#-------------------------------------------------------------------------------
if(IsSet($GLOBALS['__USER'])){
  #-----------------------------------------------------------------------------
  $Permission = Permission_Check('/Administrator/',(integer)$GLOBALS['__USER']['ID']);
  #-----------------------------------------------------------------------------
  switch(ValueOf($Permission)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return ERROR | @Trigger_Error(400);
    case 'true':
      # No more...
    case 'false':
      # No more...
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$Into = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Information','Требования - это Ваша возможность активного участия в разработке биллиговой системы JBs. Требования собираются нами исходя из Ваших запросов в центр поддержки, а так же участия в обсуждениях. Вы можете оценивать нужные Вам требования. Требования с наиболее высокими оценками попадают в следующую версию системы и реализуются нами в первую очередь.','Notice');
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Into->AddChild($Comp);
#-------------------------------------------------------------------------------
if($Permission){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Buttons/Standard',Array('onclick'=>"ShowWindow('/Administrator/FeatureEdit');"),'Новое изменение','Add.gif');
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Buttons/Panel',Array('Comp'=>$Comp,'Name'=>'Новое требование'));
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Into->AddChild($Comp);
}
#-------------------------------------------------------------------------------
$Features = DB_Select('Features',Array('ID','TypeID','Title','Comment','(SELECT SUM(`Rating`) as `Rating` FROM `FeaturesRating` WHERE `FeatureID` = `Features`.`ID` GROUP BY `FeatureID`) as `gRating`',SPrintF("(SELECT AVG(`Rating`) as `Rating` FROM `FeaturesRating` WHERE `FeatureID` = `Features`.`ID` AND `UserID` = '%u' GROUP BY `FeatureID`) as `uRating`",@$GLOBALS['__USER']['ID'])),Array('Where'=>'`VersionID` IS NULL','SortOn'=>'gRating','IsDesc'=>TRUE));
#-------------------------------------------------------------------------------
switch(ValueOf($Features)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Information','Требования не найдены.','Notice');
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Into->AddChild($Comp);
  break;
  case 'array':
    #---------------------------------------------------------------------------
    $Table = new Tag('TABLE',Array('class'=>'Standard','cellspacing'=>5));
    #---------------------------------------------------------------------------
    $Types = Array('new'=>'Новая возможность','bug'=>'Исправление ошибки','update'=>'Улучшение');
    #---------------------------------------------------------------------------
    foreach($Features as $Feature){
      #-------------------------------------------------------------------------
      $Tr = new Tag('TR');
      #-------------------------------------------------------------------------
      $TypeID = $Feature['TypeID'];
      #-------------------------------------------------------------------------
      $Img = new Tag('IMG',Array('height'=>24,'width'=>24,'src'=>SPrintF('SRC:{Images/Icons/Version/%s.gif}',$TypeID)));
      #-------------------------------------------------------------------------
      $Tr->AddChild(new Tag('TD',Array('align'=>'center'),$Img));
      #-------------------------------------------------------------------------
      $NoBody = new Tag('NOBODY',new Tag('SPAN',Array('style'=>'color:#848484;font-size:11px;'),$Types[$TypeID]),new Tag('BR'),new Tag('SPAN',Array('style'=>'color:#5B8B15;font-size:14px;'),$Feature['Title']),new Tag('BR'));
      #-------------------------------------------------------------------------
      $Comment = Comp_Load('Edesks/Text',$Feature['Comment']);
      if(Is_Error($Comment))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Span = new Tag('SPAN');
      $Span->AddHTML(SPrintF('<NOBODY>%s</NOBODY>',$Comment));
      #-------------------------------------------------------------------------
      $NoBody->AddChild($Span);
      #-------------------------------------------------------------------------
      if($Permission)
        $NoBody->AddChild(new Tag('DIV',Array('align'=>'right'),new Tag('HR'),new Tag('A',Array('style'=>'font-size:11px;','href'=>SPrintF("javascript:ShowWindow('/Administrator/FeatureEdit',{FeatureID:%u});",$Feature['ID'])),'[редактировать]')));
      #-------------------------------------------------------------------------
      $Tr->AddChild(new Tag('TD',Array('class'=>'Standard'),$NoBody));
      #-------------------------------------------------------------------------
      if(IsSet($GLOBALS['__USER'])){
        #-----------------------------------------------------------------------
        $Inner = new Tag('TABLE',Array('style'=>'white-space:nowrap;','cellspacing'=>5));
        #-----------------------------------------------------------------------
        $Options = Array('NONE'=>'-',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5');
        #-----------------------------------------------------------------------
        $uRating = $Feature['uRating'];
        $gRating = $Feature['gRating'];
        #-----------------------------------------------------------------------
        $Inner->AddChild(new Tag('TR',new Tag('TD',Array('class'=>'Comment'),'Общий рейтинг'),new Tag('TD',Array('id'=>'gRating'),($uRating?SPrintF('%01.2f',$gRating):'-'))));
        #-----------------------------------------------------------------------
        if($uRating){
          #---------------------------------------------------------------------
          $Inner->AddChild(new Tag('TR',new Tag('TD',Array('class'=>'Comment'),'Ваша оценка'),new Tag('TD',SPrintF('%01.2f',$uRating))));
          #---------------------------------------------------------------------
          $Count = DB_Count('FeaturesRating',Array('Where'=>SPrintF('`FeatureID` = %u',$Feature['ID'])));
          if(Is_Error($Count))
            return ERROR | Trigger_Error(500);
          #---------------------------------------------------------------------
          $Inner->AddChild(new Tag('TR',new Tag('TD',Array('class'=>'Comment'),'Всего оценок'),new Tag('TD',$Count)));
        }else{
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Form/Select',Array('name'=>'Rating','onchange'=>SPrintF('FeatureSetRating(%u,value,this);',$Feature['ID'])),$Options);
          if(Is_Error($Comp))
            return ERROR | Trigger_Error(500);
          #---------------------------------------------------------------------
          $Inner->AddChild(new Tag('TR',new Tag('TD',Array('class'=>'Comment'),'Ваша оценка'),new Tag('TD',$Comp)));
        }
        #-----------------------------------------------------------------------
        $Tr->AddChild(new Tag('TD',Array('class'=>'Standard','style'=>'background-color:white;','valign'=>'top'),$Inner));
      }
      #-------------------------------------------------------------------------
      $Table->AddChild($Tr,Is_Null($uRating));
    }
    #---------------------------------------------------------------------------
    $Into->AddChild($Table);
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tab','Versions',$Into);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
if(Is_Error($Out))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
