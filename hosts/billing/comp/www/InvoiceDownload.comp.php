<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$InvoiceID	= (integer) @$Args['InvoiceID'];
$IsStamp	= (boolean) @$Args['IsStamp'];
$IsTIFF		= (boolean) @$Args['IsTIFF'];
$IsNoHeaders	= (boolean) @$Args['IsNoHeaders'];
#-------------------------------------------------------------------------------
#Debug(SPrintF('[comp/www/InvoiceDownload]: Args = %s',print_r($Args,true)));
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/HTMLDoc.php','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Invoice = DB_Select('InvoicesOwners',Array('ID','UserID'),Array('UNIQ','ID'=>$InvoiceID));
#-------------------------------------------------------------------------------
switch(ValueOf($Invoice)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('ACCOUNT_NOT_FOUND','Счет не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $Permission = Permission_Check('InvoiceRead',(integer)$GLOBALS['__USER']['ID'],(integer)$Invoice['UserID']);
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
	if(!GetUploadedFileSize('Invoices',$Invoice['ID']))
		Debug(SPrintF('[comp/www/InvoiceDownload]: файл отсутствует ID = %s',$Invoice['ID']));
	#-------------------------------------------------------------------------------
	$File = GetUploadedFile('Invoices',$Invoice['ID']);
	$Document = $File['Data'];
        #-----------------------------------------------------------------------
        $DOM = new DOM($Document);
        #-----------------------------------------------------------------------
        $DOM->AddChild('Logo',new Tag('IMG',Array('src'=>'SRC:{Images/Logo.bmp}')));
        #-----------------------------------------------------------------------
        if($IsStamp){
          #---------------------------------------------------------------------
          $DOM->AddChild('dSign',new Tag('IMG',Array('src'=>'SRC:{Images/dSign.bmp}')));
          $DOM->AddChild('aSign',new Tag('IMG',Array('src'=>'SRC:{Images/aSign.bmp}')));
          $DOM->AddChild('Stamp',new Tag('IMG',Array('src'=>'SRC:{Images/Stamp.bmp}')));
        }
        #-----------------------------------------------------------------------
        $Out = $DOM->Build();
        if(Is_Error($Out))
         return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
	#Debug(print_r($Out,true));
        $File = HTMLDoc_CreatePDF('Invoice',$Out);
        #-----------------------------------------------------------------------
        switch(ValueOf($File)){
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'exception':
            return ERROR | @Trigger_Error(400);
          case 'string':
            #-------------------------------------------------------------------
            if($IsTIFF){
              #-----------------------------------------------------------------
              $Tmp = System_Element('tmp');
              if(Is_Error($Tmp))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $UniqID = UniqID('ImageMagick');
              #-----------------------------------------------------------------
              $File = IO_Write($PDF = SPrintF('%s/%s.pdf',$Tmp,$UniqID),$File);
              if(Is_Error($File))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              $Command = SPrintF('convert -density 120 -compress group4 %s %s',$PDF,$Tiff = SPrintF('%s/%s.tiff',$Tmp,$UniqID));
              #-----------------------------------------------------------------
              Debug($Command);
              #-----------------------------------------------------------------
              $ImageMagick = @Proc_Open($Command,Array(Array('pipe','r'),Array('pipe','w'),Array('file',SPrintF('%s/logs/ImageMagic.log',$Tmp),'a')),$Pipes);
              if(!Is_Resource($ImageMagick))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              Proc_Close($ImageMagick);
              #-----------------------------------------------------------------
              UnLink($PDF);
              #-----------------------------------------------------------------
              $File = IO_Read($Tiff);
              if(Is_Error($File))
                return ERROR | @Trigger_Error(500);
              #-----------------------------------------------------------------
              UnLink($Tiff);
              #-----------------------------------------------------------------
              $Extension = 'tiff';
            }else
              $Extension = 'pdf';
            #-------------------------------------------------------------------
            $Length =  MB_StrLen($File,'ASCII');
            #-------------------------------------------------------------------
            $Comp = Comp_Load('Formats/Invoice/Number',$Invoice['ID']);
            if(Is_Error($Comp))
              return ERROR | @Trigger_Error(500);
            #-------------------------------------------------------------------
	    if(!$IsNoHeaders){
	      #-------------------------------------------------------------------
              Header(SPrintF('Content-Type: application/%s; charset=utf-8',$Extension));
              Header(SPrintF('Content-Length: %u',$Length));
              Header(SPrintF('Content-Disposition: attachment; filename="Invoice%s.%s";',$Comp,$Extension));
              Header('Pragma: nocache');
	      #-------------------------------------------------------------------
	    }
            #-------------------------------------------------------------------
            return $File;
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
#-------------------------------------------------------------------------------
?>
