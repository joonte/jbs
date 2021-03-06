<?php

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// должности
$Posts	= Array(
		'Director'		=> 'Директор',
		'HeadMan'		=> 'Глава',
		'General'		=> 'Генеральный директор',
		'Executive'		=> 'Исполнительный директор',
		'Commerce'		=> 'Коммерческий директор',
		'DeputyDirector'	=> 'Заместитель директора',
		'Chairperson'		=> 'Председатель',
		'ChairmanOfTheBoard'	=> 'Председатель Правления',
		'CouncilChairman'	=> 'Председатель совета',
		'FirstSecretary'	=> 'Первый секретарь',
		'Chairman'		=> 'Председатель',
		'Rector'		=> 'Ректор',
		'President'		=> 'Президент',
		);
#-------------------------------------------------------------------------------
// ключи для проверки
$PostsKeys = Array_Keys($Posts);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Profiles = DB_Select('Profiles','*',Array('Where'=>"`TemplateID` IN ('Juridical')"));
#-------------------------------------------------------------------------------
switch(ValueOf($Profiles)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Profiles as $Profile){
		#-------------------------------------------------------------------------------
		$Attribs = $Profile['Attribs'];
		#-------------------------------------------------------------------------------
		if(In_Array($Attribs['dPost'],$PostsKeys)){
			#-------------------------------------------------------------------------------
			$Attribs['dPost'] = $Posts[$Attribs['dPost']];
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$IsUpdate = DB_Update('Profiles',Array('Attribs'=>$Attribs),Array('ID'=>$Profile['ID']));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
