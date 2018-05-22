<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Komtet\KassaSdk;

class Payment
{
    /**
     * Электронными
     */
    const TYPE_CARD = 'card';

    /**
     * Наличными
     */
    const TYPE_CASH = 'cash';

    /**
     * Cумма предоплатой (зачет аванса и/или предыдущих платежей)
     */
    const TYPE_PREPAYMENT = 'prepayment';

    /**
     * Cумма постоплатой (кредит)
     */
    const TYPE_CREDIT = 'credit';

    /**
     * Cумма встречным предлжением
     */
    const TYPE_COUNTER_PROVISIONING = 'counter_provisioning';

    /**
     * @var string
     */
    private $type;

    /**
     * @var int|float
     */
    private $sum;

    /**
     * @param string $type Form of payment
     * @param int|float $sum Amount
     *
     * @return Payment
     */
    public function __construct($type, $sum)
    {
        $this->type = $type;
        $this->sum = $sum;
    }

    /**
     * @return int|float
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            'type' => $this->type,
            'sum' => $this->sum
        ];
    }
}
