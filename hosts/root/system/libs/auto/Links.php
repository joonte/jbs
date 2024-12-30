<?php
#-------------------------------------------------------------------------------
/** @author Бреславский А.В. (Joonte Ltd.) */
#-------------------------------------------------------------------------------
function &Links(){
	#-------------------------------------------------------------------------------
	$Name = Md5('Links');
	#-------------------------------------------------------------------------------
	if(!IsSet($GLOBALS[$Name]))
		$GLOBALS[$Name] = Array();
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $GLOBALS[$Name];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}


#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function &Link_Get($LinkID,$TypeID = NULL){
	#-------------------------------------------------------------------------------
	$Links = &Links();
	#-------------------------------------------------------------------------------
	if(!IsSet($Links[$LinkID])){
		#-------------------------------------------------------------------------------
		$Links[$LinkID] = NULL;
		#-------------------------------------------------------------------------------
		if($TypeID){
			#-------------------------------------------------------------------------------
			if(!In_Array($TypeID,Array('boolean','bool','integer','int','float','double','string','array','object','null')))
				$TypeID = 'string';
			#-------------------------------------------------------------------------------
			SetType($Links[$LinkID],$TypeID);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return $Links[$LinkID];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
?>
