<?php

#-------------------------------------------------------------------------------
/** @author Бреславский А.В.-Лапшин С.М. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$IsCreate      = (boolean) @$Args['IsCreate'];
$StartDate     = (integer) @$Args['StartDate'];
$FinishDate    = (integer) @$Args['FinishDate'];
$StatisticsIDs =   (array) @$Args['StatisticsIDs'];
$Details       =   (array) @$Args['Details'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/WkHtmlToPdf.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# Формирование отчетов
#-------------------------------------------------------------------------------
if($IsCreate){
	#-------------------------------------------------------------------------------
	$DOM = new DOM();
	#-------------------------------------------------------------------------------
	$Links = &Links();
	#-------------------------------------------------------------------------------
	$Links['DOM'] = &$DOM;
	#-------------------------------------------------------------------------------
	if(Is_Error($DOM->Load('Standard')))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	// имплантируем Google Charts
	$Script = new Tag('SCRIPT',Array('type'=>'text/javascript','src'=>'https://www.gstatic.com/charts/loader.js'));
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Head',$Script);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$DOM->AddText('Title','Статистика');
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Date/SQL',Time());
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$DOM->AddChild('Into',new Tag('P',$Comp));
	#-------------------------------------------------------------------------------
	echo '<HTML><HEAD><TITLE>Формироване статистики</TITLE><LINK href="/styles/root/Css/Standard.css" rel="stylesheet" type="text/css" /><STYLE>body {margin:5px;} p {margin: 0px 0px 0px 0px;font-size:11px;}</STYLE></HEAD><BODY><P>Очистка кэша</P>';
	#-------------------------------------------------------------------------------
	$Tmp = System_Element('tmp');
	if(Is_Error($Tmp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Public = SPrintF('%s/public',$Tmp);
	#-------------------------------------------------------------------------------
	if(File_Exists($Public)){
		#-------------------------------------------------------------------------------
		$Contents = IO_Scan($Public);
		if(Is_Error($Contents))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		foreach($Contents as $Content){
			#-------------------------------------------------------------------------------
			if(Preg_Match('/^statistics_/',$Content)){
				#-------------------------------------------------------------------------------
				// проверяем дату модификации, удаляем тока старые, более недели
				$FStat = Stat(SPrintF('%s/%s',$Public,$Content));
				#-------------------------------------------------------------------------------
				if($FStat['mtime'] + 7*24*60*60 < Time()){
					#-------------------------------------------------------------------------------
					if(Is_Error(IO_RmDir(SPrintF('%s///%s',$Public,$Content))))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$UniqID = UniqID('statistics_');
	#-------------------------------------------------------------------------------
	$Folder = SPrintF('%s/%s',$Public,$UniqID);
	#-------------------------------------------------------------------------------
	if(!File_Exists($Folder))
		if(!@MkDir($Folder,0777,TRUE))
			return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$HostsIDs = Array_Reverse($GLOBALS['HOST_CONF']['HostsIDs']);
	#-------------------------------------------------------------------------------
	foreach($HostsIDs as $HostID){
		#-------------------------------------------------------------------------------
		$Path = SPrintF('%s/hosts/%s/comp/Statistics',SYSTEM_PATH,$HostID);
		#-------------------------------------------------------------------------------
		if(!File_Exists($Path))
			continue;
		#-------------------------------------------------------------------------------
		$Files = IO_Scan($Path);
		if(Is_Error($Files))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		foreach($Files as $File){
			#-------------------------------------------------------------------------------
			$StatisticID = SubStr($File,0,StriPos($File,'.'));
			#-------------------------------------------------------------------------------
			if(!In_Array($StatisticID,$StatisticsIDs))
				continue;
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load(SPrintF('Statistics/%s',$StatisticID),TRUE,$Folder,$StartDate,$FinishDate + 86400,$Details);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Title = $Comp['Title'];
			#-------------------------------------------------------------------------------
			echo SPrintF("<P><B>[%s]</B></P>",$Title);
			#-------------------------------------------------------------------------------
			$DOM->AddChild('Into',new Tag('H1',$Title));
			#-------------------------------------------------------------------------------
			$DOM->AddChild('Into',IsSet($Comp['DOM'])?$Comp['DOM']:new Tag('P','Статистика недоступна.'));
			#-------------------------------------------------------------------------------
			// графики
			if(IsSet($Comp['Script']))
				$Links['DOM']->AddChild('Head',new Tag('SCRIPT',$Comp['Script']));
			#-------------------------------------------------------------------------------
			// если более одного отчёта, надо чтобы он не залезал к графикам предыдущего
			$DOM->AddChild('Into',new Tag('HR',Array('style'=>'clear:left;')));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(!Count($DOM->Links['Into']->Childs))
		return '<P style="color:#990000;">Статистика не сформирована</P>';
	#-------------------------------------------------------------------------------
	$IsWrite = IO_Write(SPrintF('%s/index.html',$Folder),$DOM->Build(),TRUE);
	if(Is_Error($IsWrite))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	echo '<P style="color:#6F9006;">Статистика сформирована</P>';
	#-------------------------------------------------------------------------------
	$PDF = WkHtmlToPdf_CreatePDF('Statistics',$DOM,$Folder);
	#-------------------------------------------------------------------------------
	switch(ValueOf($PDF)){
	case 'error':
		# No more...
	case 'exception':
		echo '<P style="color:#990000;">Ошибка формирования PDF</P>';
		break;
	case 'string':
		#-------------------------------------------------------------------------------
		$IsWrite = IO_Write(SPrintF('%s/Statistics.pdf',$Folder),$PDF,TRUE);
		if(Is_Error($IsWrite))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		echo '<P style="color:#6F9006;">PDF документ сформирован</P>';
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Parse = '<P>Просмотр статистики в <A target="blank" href="/public/%s/index.html">HTML</A><B>|</B><A target="blank" href="/public/%s/Statistics.pdf">PDF</A></P><SCRIPT>window.scrollTo(0,1000);</SCRIPT></BODY></HTML>';
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return SPrintF($Parse,$UniqID,$UniqID);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# Формирование списка отчетов
#-------------------------------------------------------------------------------
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
#-------------------------------------------------------------------------------
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Base')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddAttribs('MenuLeft',Array('args'=>'Administrator/AddIns'));
#-------------------------------------------------------------------------------
$DOM->AddText('Title','Дополнения → Статистика → Общая статистика');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$NoBody = new Tag('NOBODY');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tab','Administrator/Statistic',$NoBody);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array();
#-------------------------------------------------------------------------------
$Table[] = 'Период формирования';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('jQuery/DatePicker','StartDate',MkTime(0,0,0,Date('n'),Date('j'),Date('Y')-10));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Начальная дата',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('jQuery/DatePicker','FinishDate',Time());
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Конечная дата',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Уровень детализации';
#-------------------------------------------------------------------------------
$Input = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'Details[]','value'=>'ByDays','id'=>'ByDays'));
if(Is_Error($Input))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'ByDays'),'По дням'),$Input);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Input = Comp_Load('Form/Input',Array('type'=>'checkbox','checked'=>'true','name'=>'Details[]','value'=>'ByMonth','id'=>'ByMonth'));
if(Is_Error($Input))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array(new Tag('LABEL',Array('for'=>'ByMonth'),'По месяцам'),$Input);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Виды отчетов';
#-------------------------------------------------------------------------------
$HostsIDs = Array_Reverse($GLOBALS['HOST_CONF']['HostsIDs']);
#-------------------------------------------------------------------------------
foreach($HostsIDs as $HostID){
	#-------------------------------------------------------------------------------
	$Path = SPrintF('%s/hosts/%s/comp/Statistics',SYSTEM_PATH,$HostID);
	#-------------------------------------------------------------------------------
	if(!File_Exists($Path))
		continue;
	#-------------------------------------------------------------------------------
	$Files = IO_Scan($Path);
	if(Is_Error($Files))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	foreach($Files as $File){
		#-------------------------------------------------------------------------------
		$StatisticID = SubStr($File,0,StriPos($File,'.'));
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load(SPrintF('Statistics/%s',$StatisticID),$IsCreate);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Input = Comp_Load('Form/Input',Array('type'=>'checkbox','name'=>'StatisticsIDs[]','value'=>$StatisticID,'id'=>$StatisticID));
		if(Is_Error($Input))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Table[] = Array(new Tag('LABEL',Array('for'=>$StatisticID),$Comp['Title']),$Input);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>'form.submit();','value'=>'Сформировать'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = $Comp;
#-------------------------------------------------------------------------------
$Table[] =new Tag('IFRAME',Array('height'=>120,'width'=>'100%','name'=>'Statistics'),'Загрузка...');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form = new Tag('FORM',Array('target'=>'Statistics','method'=>'POST','onsubmit'=>'return false;'),$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'hidden','name'=>'IsCreate','value'=>'yes'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Form->AddChild($Comp);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Form);
#-------------------------------------------------------------------------------
$Out = $DOM->Build();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
