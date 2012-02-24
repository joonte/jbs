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
$ProfileID = (integer) @$Args['ProfileID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class','libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Profile = DB_Select('Profiles',Array('ID','UserID','CreateDate','TemplateID','LENGTH(`Document`) as `Length`','StatusID','StatusDate'),Array('UNIQ','ID'=>$ProfileID));
#-------------------------------------------------------------------------------
switch(ValueOf($Profile)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $__USER = $GLOBALS['__USER'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('ProfileRead',(integer)$__USER['ID'],(integer)$Profile['UserID']);
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
        $DOM->AddText('Title','Профиль');
        #-----------------------------------------------------------------------
        $Table = Array('Общая информация');
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Profile/Number',$Profile['ID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Номер',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Date/Extended',$Profile['CreateDate']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Дата создания',$Comp);
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Formats/Profile/Template/Name',$Profile['TemplateID']);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Table[] = Array('Шаблон',$Comp);
        #-----------------------------------------------------------------------
        $Compile = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>$ProfileID,'IsFull'=>TRUE));
        #-----------------------------------------------------------------------
        switch(ValueOf($Compile)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'array':
            #-------------------------------------------------------------------
            $Template = System_XML(SPrintF('profiles/%s.xml',$Profile['TemplateID']));
            if(Is_Error($Template))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Attribs = $Template['Attribs'];
            #-------------------------------------------------------------------
            foreach(Array_Keys($Attribs) as $AttribID){
              #-----------------------------------------------------------------
              $Attrib = $Attribs[$AttribID];
              #-----------------------------------------------------------------
              if(IsSet($Attrib['Title']))
                $Table[] = $Attrib['Title'];
              #-----------------------------------------------------------------
              $Table[] = $Compile['Attribs'][$AttribID];
            }
            #-------------------------------------------------------------------
            $Table[] = 'Подтверждение введенных данных';
            #-------------------------------------------------------------------
	    $FileLength = GetUploadedFileSize('Profiles', $ProfileID);
            #$Document = $ProfileID?$Profile['Length']:0;
            #-------------------------------------------------------------------
            #$Table[] = Array('Копия документа подтверждающего достоверность данных',$Document?new Tag('TD',Array('class'=>'Standard'),new Tag('SPAN',SPrintF('%01.2f Кб.',$Document/1024)),new Tag('A',Array('href'=>SPrintF("javascript:AjaxCall('/ProfileDocumentDownload',{ProfileID:%u},'Загрузка документа','document.location = \$Answer.Location');",$Profile['ID'])),'[скачать]')):'не загружены');
	    $Table[] = Array('Копия документа подтверждающего достоверность данных',$FileLength?new Tag('TD',Array('class'=>'Standard'),new Tag('SPAN',SPrintF('%01.2f Кб.',$FileLength/1024)),new Tag('A',Array('href'=>SPrintF('/FileDownload?TypeID=Profiles&FileID=%s',$Profile['ID'])),'[скачать]')):'не загружены');
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Statuses/State','Profiles',$Profile);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $Table = Array_Merge($Table,$Comp);
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Tables/Standard',$Table,Array('style'=>'width:500px;'));
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $DOM->AddChild('Into',$Comp);
            #-------------------------------------------------------------------
            if(Is_Error($DOM->Build(FALSE)))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            return Array('Status'=>'Ok','DOM'=>$DOM->Object);
          default:
            return ERROR | @Trigger_Error(101);
        }
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
