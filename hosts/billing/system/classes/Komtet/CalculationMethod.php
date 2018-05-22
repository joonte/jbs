<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Komtet\KassaSdk;

/**
* Cпособ рассчета
*/
class CalculationMethod
{
    /**
     * Полная предварительная оплата до момента передачи предмета расчета «ПРЕДОПЛАТА 100 %»
     */
    const PRE_PAYMENT_FULL = 'pre_payment_full';

    /**
     *  Частичная предварительная оплата до момента передачи предмета расчета - «ПРЕДОПЛАТА»
     */
    const PRE_PAYMENT_PART = 'pre_payment_part';

    /**
     * Полная оплата, в том числе с учетом аванса (предварительной оплаты) в момент передачи
     * предмета расчета - «ПОЛНЫЙ РАСЧЕТ»
     */
    const FULL_PAYMENT = 'full_payment';

    /**
     * Аванс
     */
    const ADVANCE = 'advance';

    /**
     * Частичная оплата предмета расчета в момент его передачи с последующей оплатой в кредит -
     * «ЧАСТИЧНЫЙ РАСЧЕТ И КРЕДИТ»
     */
    const CREDIT_PART = 'credit_part';

    /**
     * Оплата предмета расчета после его передачи с оплатой в кредит (оплата кредита) -
     * «ОПЛАТА КРЕДИТА»
     */
    const CREDIT_PAY = 'credit_pay';

    /**
     * Передача предмета расчета без его оплаты в момент его передачи с последующей оплатой в
     * кредит - «ПЕРЕДАЧА В КРЕДИТ»
     */
    const CREDIT = 'credit';
}
