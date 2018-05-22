<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Komtet\KassaSdk;

class Check
{
    const INTENT_SELL = 'sell';
    const INTENT_SELL_RETURN = 'sellReturn';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $userContact;

    /**
     * @var string
     */
    private $intent;

    /**
     * @var int
     */
    private $taxSystem;

    /**
     * @var bool
     */
    private $shouldPrint = false;

    /**
     * @var Payment[]
     */
    private $payments = [];

    /**
     * @var Position[]
     */
    private $positions = [];

    /**
     * @var Cashier
     */
    private $cashier;

    /**
     * @param string $id An unique ID provided by an online store
     * @param string $userContact User E-Mail or phone
     * @param string $intent Check::INTENT_SELL or Check::INTENT_SELL_RETURN
     * @param int    $taxSystem See Check::TS_*
     *
     * @return Check
     */
    public function __construct($id, $userContact, $intent, $taxSystem)
    {
        $this->id = $id;
        $this->userContact = $userContact;
        $this->intent = $intent;
        $this->taxSystem = $taxSystem;
    }

    /**
     * @param string $id
     * @param string $userContact
     * @param int    $taxSystem
     *
     * @return Check
     */
    public static function createSell($id, $userContact, $taxSystem)
    {
        return new static($id, $userContact, static::INTENT_SELL, $taxSystem);
    }

    /**
     * @param string $id
     * @param string $userContact
     * @param int    $taxSystem
     *
     * @return Check
     */
    public static function createSellReturn($id, $userContact, $taxSystem)
    {
        return new static($id, $userContact, static::INTENT_SELL_RETURN, $taxSystem);
    }

    /**
     * @param bool $value
     *
     * @return Check
     */
    public function setShouldPrint($value)
    {
        $this->shouldPrint = (bool) $value;

        return $this;
    }

    /**
     * @param Payment $payment
     *
     * @return Check
     */
    public function addPayment(Payment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * @param Cashier $cashier
     *
     * @return Check
     */
    public function addCashier(Cashier $cashier)
    {
        $this->cashier = $cashier;

        return $this;
    }

    /**
     * @param Position $position
     *
     * @return Check
     */
    public function addPosition(Position $position)
    {
        $this->positions[] = $position;

        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'task_id' => $this->id,
            'user' => $this->userContact,
            'print' => $this->shouldPrint,
            'intent' => $this->intent,
            'sno' => $this->taxSystem,
            'payments' => array_map(
                function ($payment) {
                    return $payment->asArray();
                },
                $this->payments
            ),
            'positions' => array_map(
                function ($position) {
                    return $position->asArray();
                },
                $this->positions
            ),
        ];

        if ($this->cashier !== null) {
            $result['cashier'] = $this->cashier->asArray();
        }

        return $result;
    }
}
