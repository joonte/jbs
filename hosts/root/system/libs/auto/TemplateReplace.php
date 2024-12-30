<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/

function TemplateReplace($Text,$Params = Array(),$NoBody = TRUE){
	#-------------------------------------------------------------------------------
	$Text = Trim($Text);
	#-------------------------------------------------------------------------------
	# проверяем что нам сунули - текст или файл
	if(!Preg_Match('/\s/',$Text)){
		#-------------------------------------------------------------------------------
		# достаём текст из файла
		$Path = System_Element(SPrintF('templates/modules/%s.html',$Text));
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[system/libs/auto/TemplateReplace.php]: загрузка: %s',$Path));
		#-------------------------------------------------------------------------------
		if(Is_Error($Path)){
			#-------------------------------------------------------------------------------
			// а ещё у нас тут почта будет попадаться
			$Path = System_Element(SPrintF('templates/modules/%s.eml',$Text));
			#-------------------------------------------------------------------------------
			if(Is_Error($Path)){
				#-------------------------------------------------------------------------------
				$Text = SprintF('Отсутствует шаблон сообщения (templates/modules/%s.(html|eml))',$Text);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		if(!Is_Error($Path)){
			#-------------------------------------------------------------------------------
			$Text = Trim(IO_Read($Path));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if($NoBody)
		$Text = SPrintF('<NOBODY><SPAN>%s</SPAN></NOBODY>',$Text);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Replace = Array_ToLine($Params,'%');
	#-------------------------------------------------------------------------------
	foreach(Array_Keys($Replace) as $Key)
		if(!Is_Null($Replace[$Key]))
			$Text = Str_Replace($Key,$Replace[$Key],$Text);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $Text;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
