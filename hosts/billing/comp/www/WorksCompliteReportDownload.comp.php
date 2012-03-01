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
$ContractID = (integer) @$Args['ContractID'];
$IsStamp    = (boolean) @$Args['IsStamp'];
$Month      = (integer) @$Args['Month'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Wizard.php','classes/DOM.class.php','libs/HTMLDoc.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Contract = DB_Select('Contracts',Array('ID','CreateDate','UserID','TypeID','IsUponConsider','ProfileID'),Array('UNIQ','ID'=>$ContractID));
#-------------------------------------------------------------------------------
switch(ValueOf($Contract)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('CONTRACT_NOT_FOUND','Указанный договор не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $ContractID = (integer)$Contract['ID'];
    #---------------------------------------------------------------------------
    $Permission = Permission_Check('WorksCompliteRead',(integer)$GLOBALS['__USER']['ID'],(integer)$Contract['UserID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($Permission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        return ERROR | @Trigger_Error(700);
      case 'true':
        #-----------------------------------------------------------------------
        $Where = SPrintF('`ContractID` = %u AND `Month` = %u',$ContractID,$Month);
        #-----------------------------------------------------------------------
        $WorksComplite = DB_Select('WorksCompliteAgregate','*',Array('Where'=>$Where));
        #-----------------------------------------------------------------------
        switch(ValueOf($WorksComplite)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return new gException('WORKS_COMPLITE_NOT_FOUND','Выполненные работы не найдены');
          case 'array':
            #-------------------------------------------------------------------
            $Result = DB_Select('WorksCompliteAgregate','SUM(`Amount`*`Cost`*(1-`Discont`)) as `Summ`',Array('UNIQ','Where'=>$Where));
            #-------------------------------------------------------------------
            switch(ValueOf($Result)){
              case 'error':
                return ERROR | @Trigger_Error(500);
              case 'exception':
                return ERROR | @Trigger_Error(400);
              case 'array':
                #---------------------------------------------------------------
                $Comp = Comp_Load('Formats/Contract/Number',$ContractID);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Contract['Number'] = $Comp;
                #---------------------------------------------------------------
                $Comp = Comp_Load('Formats/Date/Standard',$Contract['CreateDate']);
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Contract['CreateDate'] = $Comp;
                #---------------------------------------------------------------
                $Replace = Array('Contract'=>$Contract);
                #---------------------------------------------------------------
                $Year = (integer)($Month/12);
                /* Date('t',MkTime(0,0,0,$Month - $Year*12,1,$Year + 1970)) */
                #---------------------------------------------------------------
		$Config = Config();
		$ReportDate = $Config['Executor']['WorksCompliteDate'];
		#---------------------------------------------------------------
		# $Comp = Comp_Load('Formats/Date/Standard',MkTime(0,0,0,($Month + 1) - $Year*12,1,$Year + 1970));
                $Comp = Comp_Load('Formats/Date/Standard',MkTime(0,0,0,($Month + 1) - $Year*12,$ReportDate,$Year + 1970));
                if(Is_Error($Comp))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Replace['SignDate'] = $Comp;
                #---------------------------------------------------------------
                $ProfileID = (integer)$Contract['ProfileID'];
                #---------------------------------------------------------------
                if($ProfileID){
                  #-------------------------------------------------------------
                  $Profile = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>$ProfileID));
                  #-------------------------------------------------------------
                  switch(ValueOf($Profile)){
                    case 'error':
                      return ERROR | @Trigger_Error(500);
                    case 'exception':
                      return ERROR | @Trigger_Error(400);
                    case 'array':
                      $Replace['Customer'] = $Profile['Attribs'];
                    break;
                    default:
                      return ERROR | @Trigger_Error(101);
                  }
                }
                #---------------------------------------------------------------
                $Summ = (double)$Result['Summ'];
                #---------------------------------------------------------------
                $DOM = new DOM();
                #---------------------------------------------------------------
                if(Is_Error($DOM->Load('WorksComplite/Reports/Template')))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $Adding = System_Read(SPrintF('templates/WorksComplite/Reports/Head.%s.xml',$Contract['IsUponConsider']?'Upon':'Use'));
                if(Is_Error($Adding))
                  return ERROR | @Trigger_Error(500);
                #---------------------------------------------------------------
                $DOM->AddHTML('Body',$Adding,TRUE);
                #---------------------------------------------------------------
                $Executor = DB_Select('Profiles','TemplateID',Array('UNIQ','ID'=>100));
                #---------------------------------------------------------------
                switch(ValueOf($Executor)){
                  case 'error':
                    return ERROR | @Trigger_Error(500);
                  case 'exception':
                    return $Executor;
                  case 'array':
                    #-----------------------------------------------------------
                    $Profile = Comp_Load('www/Administrator/API/ProfileCompile',Array('ProfileID'=>100));
                    #-----------------------------------------------------------
                    switch(ValueOf($Profile)){
                      case 'error':
                        return ERROR | @Trigger_Error(500);
                      case 'exception':
                        return ERROR | @Trigger_Error('Профиль исполнителя не найден');
                      case 'array':
                        #-------------------------------------------------------
                        $Replace['Executor'] = $Profile['Attribs'];
                        #-------------------------------------------------------
                        $Adding = System_Read(SPrintF('templates/WorksComplite/Reports/Types/%s/Agreement.%s.xml',$Contract['TypeID'],$Executor['TemplateID']));
                        if(Is_Error($Adding))
                          return ERROR | @Trigger_Error(500);
                        #-------------------------------------------------------
                        $DOM->AddHTML('Agreement',$Adding);
                        #-------------------------------------------------------
                        $Comp = Comp_Load('Clauses/Load',SPrintF('Contracts/Types/%s/Footer/%s',$Contract['TypeID'],$Executor['TemplateID']));
                        if(Is_Error($Comp))
                          return ERROR | @Trigger_Error(500);
                        #-------------------------------------------------------
                        $DOM->AddChild('Footer',$Comp['DOM']);
                        #-------------------------------------------------------
                        $Childs = $DOM->Links['WorkComplite']->Childs;
                        #-------------------------------------------------------
                        for($i=0;$i<Count($WorksComplite);$i++){
                          #-----------------------------------------------------
                          $WorkComplite = $WorksComplite[$i];
                          #-----------------------------------------------------
                          $Service = DB_Select('Services','*',Array('UNIQ','ID'=>$WorkComplite['ServiceID']));
                          #-----------------------------------------------------
                          switch(ValueOf($Service)){
                            case 'error':
                              return ERROR | @Trigger_Error(500);
                            case 'exception':
                              return ERROR | @Trigger_Error(400);
                            case 'array':
                              #-------------------------------------------------
                              $WorkComplite['Service'] = $Service;
                              #-------------------------------------------------
                              $WorkCompliteSumm = $WorkComplite['Amount']*$WorkComplite['Cost']*(1 - $WorkComplite['Discont']);
                              #-------------------------------------------------
                              $Comp = Comp_Load('Formats/Currency',$WorkCompliteSumm);
                              if(Is_Error($Comp))
                                return ERROR | @Trigger_Error(500);
                              #-------------------------------------------------
                              $WorkComplite['Summ'] = $Comp;
                              #-------------------------------------------------
                              $WorkComplite['Number'] = $i+1;
                              #-------------------------------------------------
                              $Comp = Comp_Load('Formats/Percent',$WorkComplite['Discont']);
                              if(Is_Error($Comp))
                                return ERROR | @Trigger_Error(500);
                              #-------------------------------------------------
                              $WorkComplite['Discont'] = $Comp;
                              #-------------------------------------------------
                              $Replacing = Array_ToLine($WorkComplite,'%');
                              #-------------------------------------------------
                              $Tr = new Tag('TR');
                              #-------------------------------------------------
                              foreach($Childs as $Child){
                                #-----------------------------------------------
                                $Td = Clone($Child);
                                #-----------------------------------------------
                                foreach(Array_Keys($Replacing) as $Pattern){
                                  #---------------------------------------------
                                  $String = ($Replacing[$Pattern]?$Replacing[$Pattern]:'-');
                                  #---------------------------------------------
                                  $Td->Text = Str_Replace($Pattern,$String,$Td->Text);
                                }
                                #-----------------------------------------------
                                $Tr->AddChild($Td);
                              }
                              #-------------------------------------------------
                              $DOM->AddChild('WorksComplite',$Tr);
                            break;
                            default:
                              return ERROR | @Trigger_Error(101);
                          }
                        }
                        #-------------------------------------------------------
                        $DOM->Delete('WorkComplite');
                        #-------------------------------------------------------
                        $Comp = Comp_Load('Formats/WorkComplite/Report/Number',$ContractID,$Month);
                        if(Is_Error($Comp))
                          return ERROR | @Trigger_Error(500);
                        #-------------------------------------------------------
                        $Report = Array('Number'=>$Comp);
                        #-------------------------------------------------------
                        $Comp = Comp_Load('Formats/Date/Month',$Month);
                        if(Is_Error($Comp))
                          return ERROR | @Trigger_Error(500);
                        #-------------------------------------------------------
                        $Report['Month'] = $Comp;
                        #-------------------------------------------------------
                        $Wizard = Wizard_ToString((double)$Summ);
                        if(Is_Error($Wizard))
                          return ERROR | @Trigger_Error(500);
                        #-------------------------------------------------------
                        $Nds = Comp_Load('Formats/Currency',($Summ*18)/118);
                        if(Is_Error($Nds))
                          return ERROR | @Trigger_Error(500);
                        #-------------------------------------------------------
                        $Config = Config();
                        #-------------------------------------------------------
                        $Wizard = SPrintF('%s. %s',$Wizard,$Config['Executor']['IsNds']?SPrintF('(в том числе НДС %s)',$Nds):'(НДС не облагается)');
                        #-------------------------------------------------------
                        $Report['Wizard'] = $Wizard;
                        #-------------------------------------------------------
                        $Replace['Report'] = $Report;
                        #-------------------------------------------------------
                        $Replace = Array_ToLine($Replace);
                        #-------------------------------------------------------
                        foreach(Array_Keys($Replace) as $LinkID){
                          #-----------------------------------------------------
                          $Text = (string)$Replace[$LinkID];
                          #-----------------------------------------------------
                          $DOM->AddText($LinkID,($Text?$Text:'-'),TRUE);
                        }
                        #-------------------------------------------------------
                        if($IsStamp){
                          #-----------------------------------------------------
                          @$DOM->Links['Sign']->Childs = Array();
                          #-----------------------------------------------------
                          $DOM->AddChild('Sign',new Tag('IMG',Array('src'=>'SRC:{Images/dSign.bmp}')));
                          #-----------------------------------------------------
                          @$DOM->Links['Stamp']->Childs = Array();
                          #-----------------------------------------------------
                          $DOM->AddChild('Stamp',new Tag('IMG',Array('src'=>'SRC:{Images/Stamp.bmp}')));
                        }
                        #-------------------------------------------------------
                        $Document = $DOM->Build();
                        if(Is_Error($Document))
                          return ERROR | @Trigger_Error(500);
                        #-------------------------------------------------------
                        foreach(Array_Keys($Replace) as $LinkID){
                          #-----------------------------------------------------
                          $Text = (string)$Replace[$LinkID];
                          #-----------------------------------------------------
                          $Document = Str_Replace(SPrintF('%%%s%%',$LinkID),$Text?$Text:'-',$Document);
                        }
                        #-------------------------------------------------------
                        $PDF = HTMLDoc_CreatePDF('WorksCompliteReport',$Document);
                        #-------------------------------------------------------
                        switch(ValueOf($PDF)){
                          case 'error':
                            return ERROR | @Trigger_Error(500);
                          case 'exception':
                            return ERROR | @Trigger_Error(400);
                          case 'string':
                            #---------------------------------------------------
                            $Comp = Comp_Load('Formats/WorkComplite/Report/Number',$ContractID,$Month);
                            if(Is_Error($Comp))
                              return ERROR | @Trigger_Error(500);
                            #---------------------------------------------------
                            $Tmp = System_Element('tmp');
                            if(Is_Error($Tmp))
                              return ERROR | @Trigger_Error(500);
                            #---------------------------------------------------
                            $File = SPrintF('Report%s.pdf',Md5($_SERVER['REMOTE_ADDR']));
                            #---------------------------------------------------
                            $IsWrite = IO_Write(SPrintF('%s/files/%s',$Tmp,$File),$PDF,TRUE);
                            if(Is_Error($IsWrite))
                              return ERROR | @Trigger_Error(500);
                            #---------------------------------------------------
                            $Location = SPrintF('/GetTemp?File=%s&Name=Report%s.pdf&Mime=application/pdf',$File,$Comp);
                            #---------------------------------------------------
                            if(!XML_HTTP_REQUEST){
                              #-------------------------------------------------
                              Header(SPrintF('Location: %s',$Location));
                              #-------------------------------------------------
                              return TRUE;
                            }
                            #---------------------------------------------------
                            return Array('Status'=>'Ok','Location'=>$Location);
                          default:
                            return ERROR | @Trigger_Error(101);
                        }
                      default:
                        return ERROR | @Trigger_Error(101);
                    }
                  default:
                    return ERROR | @Trigger_Error(101);
                }
              default:
                return ERROR | @Trigger_Error(101);
            }
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
