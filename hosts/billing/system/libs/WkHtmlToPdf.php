<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function WkHtmlToPdf_CreatePDF($ModeID,$HTML,$Prefix = '/'){
	/******************************************************************************/
	$__args_types = Array('string','string,object','string');
	#-------------------------------------------------------------------------------
	$__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
	/******************************************************************************/
	if(Is_Object($HTML)){
		#-------------------------------------------------------------------------------
		$Tables = $HTML->GetByTagName('TABLE');
		#-------------------------------------------------------------------------------
		for($i=0;$i<Count($Tables);$i++){
			#-------------------------------------------------------------------------------
			$Table = &$Tables[$i];
			#-------------------------------------------------------------------------------
			switch(@$Table->Attribs['class']){
			case 'Standard':
				#-------------------------------------------------------------------------------
				$Table->AddAttribs(Array('border'=>2,'cellspacing'=>0,'cellpadding'=>5),TRUE);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				# No more...
			}
			#-------------------------------------------------------------------------------
			// задаём 100% ширину таблиц
			$Table->AddAttribs(Array('width'=>'100%'),TRUE);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Tds = $HTML->GetByTagName('TD');
		#-------------------------------------------------------------------------------
		for($i=0;$i<Count($Tds);$i++){
			#-------------------------------------------------------------------------------
			$Td = &$Tds[$i];
			#-------------------------------------------------------------------------------
			switch(@$Td->Attribs['class']){
			case 'Head':
				#-------------------------------------------------------------------------------
				$Td->AddAttribs(Array('bgcolor'=>'#ADC1F0'),TRUE);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			case 'Separator':
				#-------------------------------------------------------------------------------
				$Td->AddAttribs(Array('bgcolor'=>'#EAEAEA'),TRUE);
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				# No more...
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Imgs = $HTML->GetByTagName('IMG');
		#-------------------------------------------------------------------------------
		for($i=0;$i<Count($Imgs);$i++){
			#-------------------------------------------------------------------------------
			$Img = &$Imgs[$i];
			#-------------------------------------------------------------------------------
			$Img->AddAttribs(Array('src'=>SPrintF('%s/%s',$Prefix,$Img->Attribs['src'])),TRUE);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$HTML = $HTML->Build();
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Config = Config();
	#-------------------------------------------------------------------------------
	$Settings = $Config['WkHtmlToPdf'];
	#-------------------------------------------------------------------------------
	$Modes = $Settings['Modes'];
	#-------------------------------------------------------------------------------
	$Mode = (IsSet($Modes[$ModeID])?$Modes[$ModeID]:$ModeID);
	#-------------------------------------------------------------------------------
	$Tmp = System_Element('tmp');
	if(Is_Error($Tmp))
		return ERROR | @Trigger_Error('[WkHtmlToPdf_CreatePDF]: временная папка не найдена');
	#-------------------------------------------------------------------------------
	$Logs = SPrintF('%s/logs',$Tmp);
	#-------------------------------------------------------------------------------
	if(!File_Exists($Logs))
		if(!@MkDir($Logs,0777,TRUE))
			return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$UniqID = UniqID('WkHtmlToPdf');
	#-------------------------------------------------------------------------------
	$File = IO_Write($Path = SPrintF('%s/%s.html',$Tmp,$UniqID),$HTML);
	//Debug($File);
	if(Is_Error($File))
		return ERROR | @Trigger_Error('[WkHtmlToPdf_CreatePDF]: не удалось создать временный файл');
	#-------------------------------------------------------------------------------
	$Command = SPrintF("wkhtmltopdf --allow %s --margin-left 20mm --margin-top 20mm --margin-right 20mm --margin-bottom 20mm --encoding utf-8 --custom-header 'meta' 'charset=utf-8' %s %s -",$Tmp,$Mode,$Path);
	#-------------------------------------------------------------------------------
	//Debug($Command);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$WkHtmlToPdf = @Proc_Open($Command,Array(Array('pipe','r'),Array('pipe','w'),Array('file',$Log = SPrintF('%s/WkHtmlToPdf.log',$Logs),'a')),$Pipes);
	if(!Is_Resource($WkHtmlToPdf))
		return ERROR | @Trigger_Error('[WkHtmlToPdf_CreatePDF]: не удалось открыть процесс');
	#-------------------------------------------------------------------------------
	$StdOut = &$Pipes[1];
	#-------------------------------------------------------------------------------
	$Result = '';
	#-------------------------------------------------------------------------------
	while(!Feof($StdOut))
		$Result .= FRead($StdOut,1024);
	#-------------------------------------------------------------------------------
	Proc_Close($WkHtmlToPdf);
	#-------------------------------------------------------------------------------
	if(!UnLink($Path))
		return ERROR | @Trigger_Error('[WkHtmlToPdf_CreatePDF]: не удалось удалить временный файл');
	#-------------------------------------------------------------------------------
	if(!$Result)
		return ERROR | @Trigger_Error(SPrintF('[WkHtmlToPdf_CreatePDF]: ошибка формирования PDF, смотрите (%s)',$Log));
	#-------------------------------------------------------------------------------
	return $Result;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------

?>
