<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Komtet\KassaSdk;

function TaxSystem($TaxationSystem)
{
	/**
	* Common tax system
	*/
	if($TaxationSystem == 'COMMON')
		return 0;

	/**
	* Simplified tax system: Income
	*/
	if($TaxationSystem == 'SIMPLIFIED_IN')
		return 1;

	/**
	* Simplified tax system: Income - Outgo
	*/
	if($TaxationSystem == 'SIMPLIFIED_IN_OUT')
		return 2;

	/**
	* An unified tax on imputed income
	*/
	if($TaxationSystem == 'UTOII')
		return 3;

	/**
	* Unified social tax
	*/
	if($TaxationSystem == 'UST')
		return 4;


	/**
	* Patent
	*/
	if($TaxationSystem == 'PATENT')
		return 5;
	

	/* ничего не подошло? какая-то херня значит и получим Unexpected status (422) */
	return 'КАКАЯ-ТО ХРЕНЬ';
}
