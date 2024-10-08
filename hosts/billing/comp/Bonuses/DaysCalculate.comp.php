<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array(
			'DaysFromBallance',	// стартовое число дней с балланса. чтоб не с нуля начинать
			'Scheme',		// тариф. используется ID и CostDay
			'Order'			// заказ. используется ContractBalance, UserID, GroupID, ServiceID
			);
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
# начальная сумма оплаты - грубое число дней на цену дня
//$CostPay = $DaysFromBallance * $Scheme['CostDay'];
// непонятно зачем я сделал ненулевую начальную сумму оплаты.
// убрал для JBS-1437, но стрёмно как-то - оно лет 7-8 нормально же работало. или не замечал
$CostPay = 0;
#-------------------------------------------------------------------------------
$DaysPay = $DaysFromBallance;
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Bonuses/DaysCalculate]: ContractBalance = %s;  DaysFromBallance = %s; $CostPay = %s',$Order['ContractBalance'],$DaysPay,$CostPay));
#-------------------------------------------------------------------------------
# счётчик итераций
$Iteration  = 0;
#-------------------------------------------------------------------------------
# начальный шаг прибавления дней - разный, в зависимости от числа дней с балланса
# (а то гоняет сто дней вперёд, потом по одному назад для тех у кого копейка на баллансе)
$Step = ($DaysFromBallance > 50)?200:50;
# знак для операций с днями TRUE = +, FALSE = -
$Sign = TRUE;
#-------------------------------------------------------------------------------
# перебираем дни, добавляя по одному, пока сумма оплаты не превысит балланс
while($CostPay <= $Order['ContractBalance']){
	#-------------------------------------------------------------------------------
	# прибавляем 1 к счётчику итераций
	$Iteration++;
	#-------------------------------------------------------------------------------
	# прибавляем/вычитаем шаг к счётчику дней
	$DaysPay = ($Sign)?($DaysPay + $Step):($DaysPay - $Step);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('DaysCalculate'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Services/Politics',$Order['UserID'],$Order['GroupID'],$Order['ServiceID'],$Scheme['ID'],$DaysPay,'calculate DaysFromBallance');
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$CostPay = 0.00;
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Services/Bonuses',$DaysPay,$Order['ServiceID'],$Scheme['ID'],$Order['UserID'],$CostPay,$Scheme['CostDay'],FALSE);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$CostPay = $Comp['CostPay'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Roll($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Bonuses/DaysCalculate]: Iteration = %s; Sign = %s; ContractBalance = %s;  DaysFromBallance = %s; CostPay = %s',$Iteration,(($Sign)?'+':'-'),$Order['ContractBalance'],$DaysPay,$CostPay));
	#-------------------------------------------------------------------------------
	$CostPaySaved = $CostPay;       # сохраняем
	#-------------------------------------------------------------------------------
	# если шаг больше 1 и цена оплаты больше чем балланс, начинаем считать дни в обратную сторону, по одному
	if($CostPay > $Order['ContractBalance']){
		#-------------------------------------------------------------------------------
		$Step = 1;
		#-------------------------------------------------------------------------------
		$CostPay = 0;			# обнуляем
		#-------------------------------------------------------------------------------
		$Sign = FALSE;
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Bonuses/DaysCalculate]: CostPaySaved = %s',$CostPaySaved));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# в зависимости от знака делаем разные действия при/для выхода из цикла
	if($Sign){
		#-------------------------------------------------------------------------------
		# если шаг больше 1 и цена оплаты больше чем балланс, вычитаем шаг и выставляем его = 1
		if($Step > 1 && $CostPaySaved > $Order['ContractBalance']){
			#-------------------------------------------------------------------------------
			$DaysPay = $DaysPay - $Step;
			#-------------------------------------------------------------------------------
			$Step = 1;
			#-------------------------------------------------------------------------------
			$CostPay = 0;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		# оплата докатилась до суммы меньше или равно баллансу
		if($CostPaySaved <= $Order['ContractBalance']){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Bonuses/DaysCalculate]: выходим из цикла, DaysPay = %s',$DaysPay));
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	# некое разумное число итераций, после которого расчёты прекращаем и возвращаем явно странное число дней, чтоб юзер обратился и удалось воспроизвести
	# выбрал 200, из расчёта что разумное число дней продления не больше 10k - 100 итераций по 100 вперёд и вернуться назад столько же
	if($Iteration > 200)
		return 1234567890;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Bonuses/DaysCalculate]: подбор дней по сумме, число итераций = %s; DaysPay = %s',$Iteration,$DaysPay));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $DaysPay;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
