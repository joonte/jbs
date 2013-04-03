<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('MessageID','CreateDate','UserID','OwnerID','Content','FileName','IsVisible','VoteBall');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$IsVisible && !$GLOBALS['__USER']['IsAdmin'])
	#return SPrintF('<!-- invisible #%s -->', $MessageID);
	return "";
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$User = DB_Select('Users',Array('ID','GroupID','EnterDate','Email','Name','Sign','(SELECT SUM(`Summ`) FROM `InvoicesOwners` WHERE `InvoicesOwners`.`StatusID`="Payed" AND `InvoicesOwners`.`UserID`=`Users`.`ID`) AS `TotalPayments`'),Array('UNIQ','ID'=>$UserID));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
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
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$CreateDate = Comp_Load('Formats/Date/Extended',$CreateDate);
if(Is_Error($CreateDate))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Group = DB_Select('Groups','Name',Array('UNIQ','ID'=>$User['GroupID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Group)){
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
$Text = Comp_Load('Edesks/Text',Array('String'=>$Content,'IsLockText'=>($OwnerID != @$GLOBALS['__USER']['ID'])));
if(Is_Error($Text))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table = new Tag('TABLE',Array('class'=>'EdeskMessage','cellspacing'=>5,'height'=>'100%','width'=>'100%'));
#-------------------------------------------------------------------------------
$String = <<<EOD
<NOBODY>
 <TR>
  <TD rowspan="2" valign="top" width="90">
   <IMG class="UserFoto" alt="Персональная фотография" height="110" width="90" src="/UserFoto?UserID=%u" />
   <TABLE width="100%%" cellspacing="0" cellpadding="0">
    <TR>
     <TD style="font-size:10px;">%s</TD>
     <TD width="51">
      <IMG alt="Статус пользователя" height="21" width="51" src="SRC:{Images/Icons/%s.gif}" style="margin-top:5px;"/>
     </TD>
    </TR>
   </TABLE>
  </TD>
  <TD height="25" class="EdeskMessageInfo">
   <DIV>
    <DIV id="" class="LeftDiv">%s</DIV>
    <DIV id="" class="RightDiv">Сообщение №%06u | %s | %s [%s]</DIV>
   </DIV>
  </TD>
 </TR>
 <TR>
  <TD height="100" class="EdeskMessageContent" style="background-color:#%s;">
    <SPAN>%s</SPAN>
   <HR />
   <PRE style="font-size:10px;">%s</PRE>
  </TD>
 </TR>
</NOBODY>
EOD;
#-------------------------------------------------------------------------------
$EnterDate = Time() - $User['EnterDate'];
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Remainder',$EnterDate);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($__USER['IsAdmin']){$Delete = SPrintF('<a onclick="ShowConfirm(\'Вы подтверждаете удаление?\',\'AjaxCall(\\\'/API/EdeskMessageDelete\\\',{MessageID:%u},\\\'Удаление сообщения\\\',\\\'GetURL(document.location);\\\');\');" onmouseover="PromptShow(event,\'Удалить это сообщение\',this);">[удалить]</a>',$MessageID);}else{$Delete = '';}
#-------------------------------------------------------------------------------
$BgColor = ($UserID != $OwnerID?'FFFFFF':'FDF6D3');
if(!$IsVisible)
	$BgColor = 'FFE4E1';
#-------------------------------------------------------------------------------
$Table->AddHTML(SPrintF($String,$User['ID'],$Comp,($EnterDate < 600?'OnLine':'OffLine'),$Delete,$MessageID,$CreateDate,$User['Name'],$Group['Name'],$BgColor,$Text,$User['Sign']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$FileLength = GetUploadedFileSize('EdesksMessages', $MessageID);
#-------------------------------------------------------------------------------
if((integer)$FileLength){
  #-----------------------------------------------------------------------------
  $Span = new Tag('SPAN',SPrintF('%s (%01.2f Кб.)',$FileName,$FileLength/1024));
  #-----------------------------------------------------------------------------
  $A = new Tag('A',Array('href'=>SPrintF('/FileDownload?TypeID=EdesksMessages&FileID=%s',$MessageID)),'[скачать]');
  #-----------------------------------------------------------------------------
  $Td = new Tag('TD',Array('class'=>'Standard','style'=>'background-color:#FCE5CC;','colspan'=>2,'align'=>'right'),$Span,$A);
  #-----------------------------------------------------------------------------
  $Table->AddChild(new Tag('TR',$Td));
}
#-------------------------------------------------------------------------------
if(!Comp_IsLoaded('Edesks/Message')){
  #-----------------------------------------------------------------------------
  $Links = &Links();
  #-----------------------------------------------------------------------------
  $DOM = &$Links['DOM'];
#-------------------------------------------------------------------------------
$Script = <<<EOD
function EdeskMessageEdit(MessageID,Message){ ShowAnswer('Сообщение','Сохранить',Message,SPrintF('AjaxCall("/API/EdeskMessageEdit",{MessageID:%u,Message:__VALUE__},"Сохранение сообщения","GetURL(document.location);")',MessageID)); }
EOD;
#-------------------------------------------------------------------------------
  $DOM->AddChild('Head',new Tag('SCRIPT',Array('type'=>'text/javascript'),$Script));
  #-------------------------------------------------------------------------------
  # TODO всё что про prompt надо бы причесать как-то...
  $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Prompt.js}'));
  $DOM->AddChild('Head',$Script);
  #-------------------------------------------------------------------------------
  $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/TicketStars.js}'));
  $DOM->AddChild('Head',$Script);
  #-------------------------------------------------------------------------------
  $Comp = Comp_Load('Css',Array('Prompt'));
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  foreach($Comp as $Css)
    $DOM->AddChild('Head',$Css);
}


#-------------------------------------------------------------------------------
if(IsSet($GLOBALS['__USER']) && Mb_StrLen($Content) < 1000){
  #-----------------------------------------------------------------------------
  if(!$__USER['IsAdmin']){
    if($__USER['ID'] != $UserID){
      #-----------------------------------------------------------------------
      $VoteTitle = Array(
			'0'     => 'Совсем плохо',
			'1'     => 'Очень плохо',
			'2'     => 'Плохо',
			'3'     => 'Не очень хорошо',
			'4'     => 'Нейтрально',
			'5'     => 'Удовлетворительно',
			'6'     => 'Хорошо',
			'7'     => 'Очень хорошо',
			'8'     => 'Отлично'
			);
      #-----------------------------------------------------------------------
      if($VoteBall > 0){
        $ArrayKey = $VoteBall - 1;
        $VoteMessage = 'Пожалуйста, оцените ответ сотрудника [' . $VoteTitle[$ArrayKey] . ']';
      }else{
        $VoteMessage = 'Пожалуйста, оцените ответ сотрудника [пока, без оценки]: ';
      }
      #-----------------------------------------------------------------------
      $SPAN = new Tag('SPAN',$VoteMessage);
      $Td = new Tag('TD',Array('colspan'=>2,'align'=>'left','style'=>'font-size:11px;'),$SPAN);
      #-----------------------------------------------------------------------
      for ($i = 0; $i < 9; $i++) {
        #---------------------------------------------------------------------
        $Img = new Tag('IMG',
			Array(
				'id'            =>SPrintF('star_%d_%d', $MessageID, $i),
				'src'           =>'SRC:{Images/Icons/DisableStar.png}',
				'onMouseOver'   => SPrintF('selectStars(event, %d, %d);PromptShow(event,\'%s\',this);',$MessageID, $i, $VoteTitle[$i]),
				'onClick'       => SPrintF("AjaxCall('/API/TicketVote',{MessageID:%u,VoteBall:%u},'Оценка сообщения','ShowTick(\"Ваша оценка \'%s\' успешно сохранена\");');",$MessageID,$i+1,$VoteTitle[$i]),
#				'title'         => $VoteTitle[$i],
				'style'		=> 'cursor: pointer;'
			));
	$Td->AddChild($Img);
      }
      #-----------------------------------------------------------------------
      $Table->AddChild(new Tag('TR',$Td));
    }
    #-----------------------------------------------------------------------
  }else{	# $IsAdmin false->true
      # ссылка на редактирование
      $A = new Tag('A',Array('href'=>SPrintF("javascript:EdeskMessageEdit(%u,'%s');",$MessageID,AddcSlashes($Content,"\0\n\r\\\'"))),'[редактировать]');
      # дополнительно проверяем - не сотрудник ли это, для сотрудников не надо линки в подпись лепить
      Debug("[comp/Edesks/Message]: check for links, user id = " . (integer)$User['ID']);
      $IsPermission = Permission_Check('/Administrator/',(integer)$User['ID']);
      switch(ValueOf($IsPermission)){
        case 'error':
	  return ERROR | @Trigger_Error(500);
	case 'exception':
	  return ERROR | @Trigger_Error(400);
	case 'false':
	  # не сотрудник, выводим всё
          #---------------------------------------------------------------------------
	  $Td = new Tag('TD',Array('colspan'=>2,'align'=>'right','style'=>'font-size:11px;'));
	  # шукаем его заказы на услуги
	  $Columns = Array('Item','Code','Name');
	  $Where = Array('`Services`.`ID`=`OrdersOwners`.`ServiceID`',SPrintF('`OrdersOwners`.`UserID`=%s',$UserID));
	  $Items = DB_Select(Array('OrdersOwners','Services'),$Columns,Array('Where'=>$Where,'GroupBy'=>'Code','SortOn'=>'`Services`.`SortID`'));
	  #---------------------------------------------------------------------------
	  switch(ValueOf($Items)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            # No orders.
            break;
          case 'array':
	    #---------------------------------------------------------------------------
            foreach($Items as $Item){
	      #---------------------------------------------------------------------------
	      if($Item['Code'] == 'Domains'){
	        $UrlPart = 'Domain';
              }elseif($Item['Code'] == 'Default'){
                $UrlPart = 'Services';
              }else{
	        $UrlPart = $Item['Code'];
	      }
	      #---------------------------------------------------------------------------
	      $LinkTarget = SPrintF('/Administrator/%sOrders?Search=%s&PatternOutID=Default',$UrlPart,$User['Email']);
	      #---------------------------------------------------------------------------
	      $UserLinks = Comp_Load('Formats/String',SPrintF('[%s]',$Item['Code']),10,$LinkTarget);
              if(Is_Error($UserLinks))
                return ERROR | @Trigger_Error(500);
              #---------------------------------------------------------------------------
              $UserLinks->AddAttribs(Array('target'=>'_blank','onMouseOver'=>SPrintF('PromptShow(event,\'Заказы на %s (%s)\',this);',$Item['Item'],$Item['Name'])));
              #---------------------------------------------------------------------------
              $Td->AddChild($UserLinks);
	    }
	    #---------------------------------------------------------------------------
	    break;
          default:
            return ERROR | @Trigger_Error(101);
          }
          #---------------------------------------------------------------------------
      if($User['TotalPayments'] > 0){
        $InvoicesText = SPrintF('[%s]',$User['TotalPayments']);
	  }else{
        $InvoicesText = "[Счета]";
	  }
	  $UserLinks = Comp_Load('Formats/String',$InvoicesText,10,SPrintF('/Administrator/Invoices?Search=%s&PatternOutID=Default',$User['Email']));
	  if(Is_Error($UserLinks))
	    return ERROR | @Trigger_Error(500);
	  $UserLinks->AddAttribs(Array('target'=>'_blank','onMouseOver'=>'PromptShow(event,\'Сумма оплаченных счетов пользователя\',this);'));
	  $Td->AddChild($UserLinks);
	  #---------------------------------------------------------------------------
	  $UserLinks = Comp_Load('Formats/String','[Тикеты]',10,SPrintF('/Administrator/Tickets?Search=%s&PatternOutID=Default',$User['Email']));
	  if(Is_Error($UserLinks))
	    return ERROR | @Trigger_Error(500);
	  $UserLinks->AddAttribs(Array('target'=>'_blank','onMouseOver'=>'PromptShow(event,\'Найти все тикеты пользователя\',this);'));
	  $Td->AddChild($UserLinks);
	  #---------------------------------------------------------------------------
	  # ссылка на редактирование
	  $Td->AddChild($A);
          break;
        case 'true':
          # сотрудник, выводим тока редактирование
          $Td = new Tag('TD',Array('colspan'=>2,'align'=>'right','style'=>'font-size:11px;'),$A);
          break;
        default:
          return ERROR | @Trigger_Error(101);
      }
      #-------------------------------------------------------------------------
      $Table->AddChild(new Tag('TR',$Td));
  }
}
#-------------------------------------------------------------------------------
return $Table;
#-------------------------------------------------------------------------------

?>
