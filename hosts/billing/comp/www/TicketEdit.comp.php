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
$UserID = (integer) @$Args['UserID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($UserID){
  #-----------------------------------------------------------------------------
  $User = DB_Select('Users',Array('ID','GroupID','Name'),Array('UNIQ','ID'=>$UserID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($User)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('USER_NOT_FOUND','Пользователь не найден');
    case 'array':
      # No more...
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$DOM->AddText('Title',$UserID?SPrintF('Новый запрос для [%s]',$User['Name']):'Новый запрос');
#-------------------------------------------------------------------------------
$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/TicketEdit.js}'));
#-------------------------------------------------------------------------------
$DOM->AddChild('Head',$Script);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('name'=>'TicketEditForm','onsubmit'=>'return false;'));
#-------------------------------------------------------------------------------
$Table = Array('Общие параметры');
#-------------------------------------------------------------------------------
$Comp = Comp_Load(
  'Form/Input',
  Array(
    'name'  => 'Theme',
    'size'  => 65,
    'type'  => 'text',
    'prompt'=> "Краткое описание Вашей проблемы или вопроса.\nНапример: Проблемы с почтой"
  )
);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Тема запроса',$Comp);
#-------------------------------------------------------------------------------
$Groups = DB_Select('Groups',Array('ID','Name','Comment'),Array('Where'=>"`IsDepartment` = 'yes'"));
#-------------------------------------------------------------------------------
switch(ValueOf($Groups)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DEPARTMENTS_NOT_FOUND','Отделы не определены');
  case 'array':
    #---------------------------------------------------------------------------
    $Options = Array();
    #---------------------------------------------------------------------------
    foreach($Groups as $Group)
      $Options[$Group['ID']] = SPrintF('%s (%s)',$Group['Name'],$Group['Comment']);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Select',Array('name'=>'TargetGroupID'),$Options);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Отдел',$Comp);
    #---------------------------------------------------------------------------
    if($GLOBALS['__USER']['IsAdmin']){
      #-------------------------------------------------------------------------
      $Workers = DB_Select('Users',Array('ID','Name'),Array('Where'=>SPrintF("(SELECT `IsDepartment` FROM `Groups` WHERE `Groups`.`ID` = `Users`.`GroupID`) = 'yes' OR `ID` = 100")));
      #-------------------------------------------------------------------------
      switch(ValueOf($Workers)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          return new gException('WORKERS_NOT_FOUND','Сотрудники не определены');
        case 'array':
          #---------------------------------------------------------------------
          $Options = Array();
          #---------------------------------------------------------------------
          foreach($Workers as $Worker){
            #-------------------------------------------------------------------
            $WorkerID = $Worker['ID'];
            #-------------------------------------------------------------------
            $Options[$WorkerID] = $Worker['Name'];
          }
          #---------------------------------------------------------------------
          $Comp = Comp_Load('Form/Select',Array('name'=>'TargetUserID'),$Options,$GLOBALS['__USER']['ID']);
          if(Is_Error($Comp))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          $Table[] = Array('Сотрудник',$Comp);
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
    }
    #---------------------------------------------------------------------------
    $Config = Config();
    #---------------------------------------------------------------------------
    $Priorities = $Config['Edesks']['Priorities'];
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Form/Select',Array('name'=>'PriorityID'),$Priorities);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = Array('Приоритет',$Comp);
    #---------------------------------------------------------------------------
    $Table[] = 'Сообщение';
    #---------------------------------------------------------------------------
    //$Smiles = System_XML('config/Smiles.xml');
    //if(Is_Error($Smiles))
    //  return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    //$Options = Array('NO'=>'Не выбран');
    #---------------------------------------------------------------------------
    //foreach($Smiles as $Smile)
    //  $Options[$Smile['Pattern']] = $Smile['Name'];
    #---------------------------------------------------------------------------
    //$Comp = Comp_Load('Form/Select',Array('name'=>'Smile','onchange'=>"if(value != 'NO'){ form.Message.value += value; }"),$Options);
    //if(Is_Error($Comp))
    //  return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $Tr = new Tag('TR');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Upload','TicketMessageFile');
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Tr->AddChild(new Tag('NOBODY',new Tag('TD',Array('class'=>'Comment'),'Прикрепить файл'),new Tag('TD',$Comp)));
    #---------------------------------------------------------------------------
    if($GLOBALS['__USER']['IsAdmin']){ # is suppor
      $Articles = DB_Select('Clauses','*',Array('Where'=>"`Partition` LIKE '/Administrator/ButtonsNew:%' AND `IsPublish`='yes'",'Order'=>'Partition'));
      #-----------------------------------------------------------------------
      switch(ValueOf($Articles)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        $A = new Tag('A',Array('title'=>'как добавить шаблоны быстрых ответов',
                               'href'=>'http://wiki.joonte.com/index.php?title=TiketAnswerTemplate'),
                               'шаблоны ответов');
        $Td = new Tag('TD',$A);
        $Tr->AddChild($Td);
        break;
      case 'array':
        #-------------------------------------------------------------------
        foreach($Articles as $Article){
          # prepare text: delete tags, begin/end space
          $Text = trim(Strip_Tags($Article['Text']));
          # delete space on string begin
          $Text = Str_Replace("\n ","\n",$Text);
          # delete double spaces
          $Text = Str_Replace("  "," ",$Text);
          # delete carrier return
          $Text = Str_Replace("\r","",$Text);
          # delete many \n
          $Text = Str_Replace("\n\n","\n",$Text);
          # prepare for java script
          $Text = Str_Replace("\n",'\\n',$Text);
          # format: /Administrator/Buttons:SortOrder:ImageName.gif
          # button image, get image name
          $Partition = explode(":", $Article['Partition']);
          if(IsSet($Partition[2])){
            # button image, get image extension
            $Extension = explode(".", StrToLower($Partition[2]));
          }else{
            $Extension = '';
          }
          # если есть чё-то после точки, и если оно похоже на расширение картинки, ставим это как картинку
          if(IsSet($Extension[1]) && In_Array($Extension[1],Array('png','gif','jpg','jpeg'))){
            $Image = $Partition[2];
          }else{
            # иначе - дефолтовую информационную картинку
            $Image = 'Info.gif';
          }
          # делаем кнопку
          $Comp = Comp_Load('Buttons/Standard',
                             Array('onclick' => "form.Message.value += '" . $Text . "';",'style'=>'cursor: pointer;'),
                                   $Article['Title'],
                                   $Image);
          if(Is_Error($Comp))
             return ERROR | @Trigger_Error(500);
          $Td = new Tag('TD');
          $Td->AddChild($Comp);
          #$NoBody->AddChild(new Tag('TD',Array('width'=>25),$Comp));
          $Tr->AddChild($Td);
        }
        break;
      default:
        return ERROR | @Trigger_Error(101);
      }
    } # is support
    #---------------------------------------------------------------------------
    $Table[] = new Tag('TABLE',$Tr);
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/TextArea',
      Array(
        'name'  => 'Message',
        'style' => 'width:100%;',
        'rows'  => 10
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Table[] = $Comp;
    #---------------------------------------------------------------------------
    $Disabled = Array();
    #---------------------------------------------------------------------------
    if(!$GLOBALS['__USER']['IsAdmin'])
      $Disabled[] = 'hidden';
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Edesks/Panel',$Disabled);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $Tr = new Tag('TR',$Comp);
    #---------------------------------------------------------------------------
    $Comp = Comp_Load(
      'Form/Input',
      Array(
        'type'    => 'button',
        'onclick' => 'TicketEdit();',
        'value'   => 'Добавить'
      )
    );
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if($GLOBALS['__USER']['IsAdmin']){
        # сотрудник, добавляем флаги
	$Config = Config();
	$Positions = $Config['Edesks']['Flags'];
	#---------------------------------------------------------------------------
	$Comp1 = Comp_Load('Form/Select',
			Array('name'=>'Flags'),
			$Positions,
			'CloseOnSee');
	if(Is_Error($Comp1))
		return ERROR | @Trigger_Error(500);
	
	$Div = new Tag('DIV',$Comp1,new Tag('SPAN','и'),$Comp);
    }else{
        # юзер. тока кнопка
        $Div = new Tag('DIV',$Comp);
    }
    #---------------------------------------------------------------------------
    $Tr->AddChild(new Tag('TD',Array('align'=>'right'),$Div));
    $Table[] = new Tag('TABLE',Array('width'=>'100%'),$Tr);
    #---------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    if($UserID){
      #-------------------------------------------------------------------------
      $Comp = Comp_Load(
        'Form/Input',
        Array(
          'type'  => 'hidden',
          'name'  => 'UserID',
          'value' => $UserID
        )
      );
      if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $Form->AddChild($Comp);
    }
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Tables/Standard',$Table);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Form->AddChild($Comp);
    #---------------------------------------------------------------------------
    $Tr = new Tag('TR',new Tag('TD',Array('valign'=>'top'),$Form));
    #---------------------------------------------------------------------------
    if(!$UserID){
      #-------------------------------------------------------------------------
      $Users = DB_Select('Users',Array('ID','Name','(SELECT `Name` FROM `Groups` WHERE `Users`.`GroupID` = `Groups`.`ID`) as `GroupName`'),Array('Where'=>"(SELECT `IsDepartment` FROM `Groups` WHERE `Groups`.`ID` = `Users`.`GroupID`) = 'yes' AND `IsHidden` = 'no' AND UNIX_TIMESTAMP() - `EnterDate` < 600"));
      #-------------------------------------------------------------------------
      switch(ValueOf($Users)){
        case 'error':
          return ERROR | @Trigger_Error(500);
        case 'exception':
          # No more...
        break;
        case 'array':
          #---------------------------------------------------------------------
          $Table = new Tag('TABLE',Array('class'=>'Standard','cellspacing'=>5),new Tag('CAPTION','Сейчас в сети'));
          #---------------------------------------------------------------------
          $Block = new Tag('TR');
          #---------------------------------------------------------------------
          foreach($Users as $User){
	    #-------------------------------------------------------------------
            $Block->AddHTML(TemplateReplace('www.TicketEdit',$User));
            #-------------------------------------------------------------------
            if(Count($Block->Childs)%2 == 0){
              #-----------------------------------------------------------------
              $Table->AddChild($Block);
              #-----------------------------------------------------------------
              $Block = new Tag('TR');
            }
          }
          #---------------------------------------------------------------------
          if(Count($Block->Childs))
            $Table->AddChild($Block);
          #---------------------------------------------------------------------
          $Tr->AddChild(new Tag('TD',Array('valign'=>'top'),$Table));
        break;
        default:
          return ERROR | @Trigger_Error(101);
      }
    }
    #---------------------------------------------------------------------------
    $DOM->AddChild('Into',new Tag('TABLE',$Tr));
    #---------------------------------------------------------------------------
    if(Is_Error($DOM->Build(FALSE)))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok','DOM'=>$DOM->Object);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
