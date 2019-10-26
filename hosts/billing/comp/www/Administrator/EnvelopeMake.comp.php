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
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/WkHtmlToPdf.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Executor = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>100));
#-------------------------------------------------------------------------------
switch(ValueOf($Executor)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('EXECUTOR_PROFILE_NOT_FOUND','Профиль исполнителя не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $Replace = Array('Executor'=>$Executor['Attribs']);
    #---------------------------------------------------------------------------
    $Customer = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>$ProfileID));
    #---------------------------------------------------------------------------
    switch(ValueOf($Customer)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'array':
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Clauses/Load',SPrintF('Envelopes/%s/Template',$Executor['TemplateID']));
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM = new DOM($Comp['DOM']);
        #-----------------------------------------------------------------------
        $Links = &Links();
        # Коллекция ссылок
        $Links['DOM'] = &$DOM;
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('Clauses/Load',SPrintF('Envelopes/%s',$Customer['TemplateID']));
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Recipient',$Comp['DOM']);
        #-----------------------------------------------------------------------
        $Replace['Customer'] = $Customer['Attribs'];
        #-----------------------------------------------------------------------
        $Im = ImageCreate(300,90);
        #-----------------------------------------------------------------------
        $Color = ImageColorAllocate($Im,255,255,255);
        #-----------------------------------------------------------------------
        $pIndex = $Customer['Attribs']['pIndex'];
        #-----------------------------------------------------------------------
        $Font = System_Element('share/fonts/Posti.ttf');
        if(Is_Error($Font))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Color = ImageColorAllocate($Im,0,0,0);
        #-----------------------------------------------------------------------
        ImageTTFText($Im,60,0,0,80,$Color,$Font,SPrintF('$%s',$pIndex));
        #-----------------------------------------------------------------------
        $Tmp = System_Element('tmp');
        if(Is_Error($Tmp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Image = SPrintF('%s/%s.png',$Tmp,UniqID('tmp'));
        #-----------------------------------------------------------------------
        ImagePng($Im,$Image);
        #-----------------------------------------------------------------------
        $DOM->AddAttribs('pIndexImage',Array('src'=>$Image),TRUE);
        #-----------------------------------------------------------------------
        $Out = $DOM->Build();
        #-----------------------------------------------------------------------
        if(Is_Error($Out))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        $Replace = Array_ToLine($Replace);
        #-----------------------------------------------------------------------
        foreach(Array_Keys($Replace) as $LinkID){
          #---------------------------------------------------------------------
          $Text = (string)$Replace[$LinkID];
          #---------------------------------------------------------------------
          $Out = Str_Replace(SPrintF('%%%s%%',$LinkID),$Text?$Text:'-',$Out);
        }
        #-----------------------------------------------------------------------
        $PDF = WkHtmlToPdf_CreatePDF('Envelope',$Out);
        #-----------------------------------------------------------------------
        switch(ValueOf($PDF)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'string':
            #-------------------------------------------------------------------
            UnLink($Image);
            #-------------------------------------------------------------------
            $Tmp = System_Element('tmp');
            if(Is_Error($Tmp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            $File = SPrintF('Envelope%s.pdf',Md5($_SERVER['REMOTE_ADDR']));
            #-------------------------------------------------------------------
            $IsWrite = IO_Write(SPrintF('%s/files/%s',$Tmp,$File),$PDF,TRUE);
            if(Is_Error($IsWrite))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
            return Array('Status'=>'Ok','Location'=>SPrintF('/GetTemp?File=%s&Name=Envelope.pdf&Mime=application/pdf',$File));
          default:
            return ERROR | @Trigger_Error(101);
        }
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
