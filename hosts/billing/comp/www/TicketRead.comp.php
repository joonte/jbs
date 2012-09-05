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
$TicketID = (integer) @$Args['TicketID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Ticket = DB_Select('Edesks',Array('ID','UserID','Theme','UpdateDate','StatusID','SeenByPersonal','LastSeenBy','(SELECT `IsDepartment` FROM `Groups` WHERE `Groups`.`ID` = (SELECT `GroupID` FROM `Users` WHERE `Users`.`ID` = `Edesks`.`UserID`)) as `IsDepartment`','Flags'),Array('UNIQ','ID'=>$TicketID));
#-------------------------------------------------------------------------------
switch(ValueOf($Ticket)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('TicketRead',(integer)$__USER['ID'],(integer)$Ticket['UserID']);
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
        $Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'SRC:{Js/Pages/TicketRead.js}'));
        #-----------------------------------------------------------------------
        $DOM->AddChild('Head',$Script);
        #-----------------------------------------------------------------------
        $DOM->AddText('Title',HtmlSpecialChars($Ticket['Theme']));
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'name'  => 'TicketID',
            'type'  => 'hidden',
            'value' => $Ticket['ID']
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Form = new Tag('FORM',Array('name'=>'TicketReadForm','onsubmit'=>'return false;'),$Comp);
        #-----------------------------------------------------------------------
        //$Smiles = System_XML('config/Smiles.xml');
        //if(Is_Error($Smiles))
        //  return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        //$Options = Array('NO'=>'Не выбран');
        #-----------------------------------------------------------------------
        //foreach($Smiles as $Smile)
        //  $Options[$Smile['Pattern']] = $Smile['Name'];
        #-----------------------------------------------------------------------
        //$Comp = Comp_Load('Form/Select',Array('name'=>'Smile','onchange'=>"if(value != 'NO'){ form.Message.value += value; }"),$Options);
        //if(Is_Error($Comp))
        //  return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Tr = new Tag('TR');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Upload','TicketMessageFile');
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Tr->AddChild(new Tag('NOBODY',new Tag('TD',Array('class'=>'Comment'),'Прикрепить файл'),new Tag('TD',$Comp)));
        #-----------------------------------------------------------------------
	if($__USER['ID'] == $Ticket['UserID']){	# is ordinar user
#		$Td = new Tag('TD');
#		#-----------------------------------------------------------------------
#		$VoteTitle = Array(
#					'0'	=> 'Лучше бы не отвечали',
#					'1'	=> 'Совсем плохо',
#					'2'	=> 'Очень плохо',
#					'3'	=> 'Плохо',
#					'4'	=> 'Не очень хорошо',
#					'5'	=> 'Нейтрально',
#					'6'	=> 'Удовлетворительно',
#					'7'	=> 'Хорошо',
#					'8'	=> 'Очень хорошо',
#					'9'	=> 'Отлично'
#				);
#		#-----------------------------------------------------------------------
#		for ($i = 0; $i < 9; $i++) {
#			#---------------------------------------------------------------------
#			$Img = new Tag('IMG',
#					Array(
#						'id'		=>SPrintF('star_%d_%d', $Ticket['ID'], $i),
#						'src'		=>'SRC:{Images/Icons/DisableStar.png}',
#						'onMouseOver'	=> SPrintF('selectStars(event, %d, %d);',$Ticket['ID'], $i),
#						'onClick'	=> SPrintF("AjaxCall('/API/TicketVote',{TicketID:%u,VoteBall:%u},'Оценка сообщения','ShowTick(\"Ваша оценка \'%s\' успешно сохранена\");');",$Ticket['ID'], $i + 1,$VoteTitle[$i]),
#						'title'		=> $VoteTitle[$i]
#					));
#			$Td->AddChild($Img);
#		}
#		#-----------------------------------------------------------------------
#		$Tr->AddChild($Td);
		# add SeenByUser field
		$IsUpdate = DB_Update('Edesks',Array('SeenByUser'=>Time()),Array('ID'=>$TicketID));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
	}else{	# is support
		$Articles = DB_Select('Clauses','*',Array('Where'=>"`Partition` LIKE '/Administrator/Buttons:%' AND `IsPublish`='yes'",'Order'=>'Partition'));
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
						Array('onclick'	=> "form.Message.value += '" . $Text . "';",'style'=>'cursor: pointer;'),
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
		# add SeenByPersonal/LastSeenBy fields
		$IsUpdate = DB_Update('Edesks',Array('SeenByPersonal'=>Time(),'LastSeenBy'=>$__USER['ID']),Array('ID'=>$TicketID));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
	}
        #-----------------------------------------------------------------------
        $Table[] = new Tag('TABLE',$Tr);
        #-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
	if($__USER['ID'] == $Ticket['UserID']){	# ordinar user
		$color = "white";
	}else{	# support
		if($Ticket['LastSeenBy'] == $__USER['ID']){
			$color = "white";
		}else{
			$TimePeriod = time() - $Ticket['SeenByPersonal'];
			if($TimePeriod < 60){
				$color = "lightcoral";
			}elseif($TimePeriod < 120){
				$color = "lightpink";
			}elseif($TimePeriod < 180){
				$color = "khaki";
			}elseif($TimePeriod < 240){
				$color = "lemonchiffon";
			}elseif($TimePeriod < 300){
				$color = "gainsboro";
			}else{
				$color = "white";
			}
		}
	}
	#-----------------------------------------------------------------------
	#-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/TextArea',
          Array(
            'name'       => 'Message',
            'onkeypress' => 'ctrlEnterEvent(event);',
            'style'      => SPrintF('background:%s; width:%u;',$color,Max(@$_COOKIE['wScreen']/1.5,630)),
            'rows'       => 5
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = $Comp;
        #-----------------------------------------------------------------------
        $Disabled = Array();
        #-----------------------------------------------------------------------
        if($__USER['ID'] == $Ticket['UserID'])
          $Disabled[] = 'hidden';
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Edesks/Panel',$Disabled);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Tr = new Tag('TR',$Comp);
        #-----------------------------------------------------------------------
        $Img = new Tag('IMG',Array('width'=>1,'height'=>20,'src'=>'SRC:{Images/SeparateLine.png}'));
        #-----------------------------------------------------------------------
        $Tr->AddChild(new Tag('TD',Array('align'=>'center','width'=>10),$Img));
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Buttons/Standard',Array(),'Предыдущий запрос','Previos.gif');
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Query = Array("`StatusID` != 'Closed'","(SELECT `IsDepartment` FROM `Groups` WHERE `Groups`.`ID` = `Edesks`.`TargetGroupID`) = 'yes'",($Ticket['UserID'] != $__USER['ID']?SPrintF("(SELECT `IsDepartment` FROM `Groups` WHERE `Groups`.`ID` = (SELECT `GroupID` FROM `Users` WHERE `Users`.`ID` = `Edesks`.`UserID`)) = '%s'",$Ticket['IsDepartment']?'yes':'no'):SPrintF('`UserID` = %u',$Ticket['UserID'])));
        #-----------------------------------------------------------------------
        $Where = $Query;
        #-----------------------------------------------------------------------
        $Where[] = SPrintF('`UpdateDate` < %u',$Ticket['UpdateDate']);
        #-----------------------------------------------------------------------
        $Previos = DB_Select('Edesks','ID',Array('UNIQ','Where'=>$Where,'SortOn'=>'UpdateDate','Limits'=>Array(0,1)));
        #-----------------------------------------------------------------------
        switch(ValueOf($Previos)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            $Comp->AddAttribs(Array('disabled'=>'true'));
          break;
          case 'array':
            $Comp->AddAttribs(Array('onclick'=>SPrintF("ShowWindow('/TicketRead',{TicketID:%u});",$Previos['ID'])));
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
        #-----------------------------------------------------------------------
        $Tr->AddChild(new Tag('TD',Array('width'=>30),$Comp));
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Buttons/Standard',Array(),'Следующий запрос','Next.gif');
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Where = $Query;
        #-----------------------------------------------------------------------
        $Where[] = SPrintF('`UpdateDate` > %u',$Ticket['UpdateDate']);
        #-----------------------------------------------------------------------
        $Next = DB_Select('Edesks','ID',Array('UNIQ','Where'=>$Where,'SortOn'=>'UpdateDate','Limits'=>Array(0,1)));
        #-----------------------------------------------------------------------
        switch(ValueOf($Next)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            $Comp->AddAttribs(Array('disabled'=>'true'));
          break;
          case 'array':
            $Comp->AddAttribs(Array('onclick'=>SPrintF("ShowWindow('/TicketRead',{TicketID:%u});",$Next['ID'])));
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
        #-----------------------------------------------------------------------
        $Tr->AddChild(new Tag('TD',Array('width'=>30),$Comp));
        #-----------------------------------------------------------------------
        $Tr->AddChild(new Tag('TD'));
        #-----------------------------------------------------------------------
        $Comp = Comp_Load(
          'Form/Input',
          Array(
            'type'    => 'button',
            'onclick' => 'TicketAddMessage();',
            'value'   => 'Добавить'
          )
        );
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Div = new Tag('DIV',$Comp,new Tag('SPAN','и'));

	if($__USER['ID'] == $Ticket['UserID']){ # is ordinar user
	        #-----------------------------------------------------------------------
	        $Comp = Comp_Load(
			'Form/Input',
			Array(
				'name'  => 'Flags',
				'type'  => 'checkbox',
				'value' => 'Closed'
			)
		);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
	        #-----------------------------------------------------------------------
		$Div->AddChild(new Tag('NOBODY',$Comp,new Tag('SPAN','закрыть запрос (проблема решена)')));
	}else{ # user -> support
		$Config = Config();
		$Positions = $Config['Edesks']['Flags'];
		#-----------------------------------------------------------------------
		$Comp = Comp_Load('Form/Select',
		        Array('name'=>'Flags'),
			$Positions,
			$Ticket['Flags']);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		$Div->AddChild(new Tag('NOBODY',$Comp));
	}
        #-----------------------------------------------------------------------
        $Where = $Query;
        #-----------------------------------------------------------------------
        $Where[] = SPrintF("`UpdateDate` > %u AND EDESKS_MESSAGES(`ID`,%u) > 0",$Ticket['UpdateDate'],$__USER['ID']);
        #-----------------------------------------------------------------------
        $Next = DB_Select('Edesks',Array('ID','Theme'),Array('UNIQ','Where'=>$Where,'SortOn'=>'UpdateDate','Limits'=>Array(0,1)));
        #-----------------------------------------------------------------------
        switch(ValueOf($Next)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            # No more...
          break;
          case 'array':
            #-------------------------------------------------------------------
            $Comp = Comp_Load(
              'Form/Input',
              Array(
                'name'    => 'IsNext',
                'type'    => 'checkbox',
                'value'   => $Next['ID']
              )
            );
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Div->AddChild(new Tag('NOBODY',$Comp,new Tag('SPAN','к следующему')));
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }
        #-----------------------------------------------------------------------
        $Tr->AddChild(new Tag('TD',Array('align'=>'right'),$Div));
        #-----------------------------------------------------------------------
        $Table[] = new Tag('TABLE',Array('width'=>'100%'),$Tr);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Tables/Standard',$Table);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Iframe = new Tag('IFRAME',Array('id'=>'TicketReadMessages','src'=>SPrintF('/TicketMessages?TicketID=%u',$Ticket['ID']),'width'=>'100%','style'=>SPrintF('height:%u;',Max(@$_COOKIE['hScreen']/2.5,240))),'Загрузка...');
        #-----------------------------------------------------------------------
        $Form->AddChild(new Tag('TABLE',new Tag('TR',new Tag('TD',$Iframe)),new Tag('TR',new Tag('TD',$Comp))));
        #-----------------------------------------------------------------------
        $DOM->AddChild('Into',$Form);
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
