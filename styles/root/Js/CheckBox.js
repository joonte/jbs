//------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
function ChangeCheckBox($CheckBox){
	//----------------------------------------------------------------------------
	var CheckById   = document.getElementById($CheckBox);
	//----------------------------------------------------------------------------
	if(CheckById == null){	// работаем по имени чекбокса
		if(document.getElementsByName($CheckBox)[0].checked){
			document.getElementsByName($CheckBox)[0].checked = false;
		}else{
			document.getElementsByName($CheckBox)[0].checked = true;
		}
	}else{	// работаем по идентификатору чекбоеса
		//--------------------------------------------------------------------------
		if(CheckById != null){	// если он, конечно, задан. иначе вообще ничё не делаем
			if(document.getElementById($CheckBox).checked){
				document.getElementById($CheckBox).checked = false;
			}else{
				document.getElementById($CheckBox).checked = true;
			}
		}
	}
}
//-------------------------------------------------------------------------------

