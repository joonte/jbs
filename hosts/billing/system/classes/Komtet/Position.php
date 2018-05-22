<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

#namespace Komtet\KassaSdk;

class Position
{
    /**
     * @var string
     */
    private $id = null;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int|float
     */
    private $price;

    /**
     * @var int|float
     */
    private $quantity;

    /**
     * @var int|float
     */
    private $total;

    /**
     * @var int|float
     */
    private $discount;

    /**
     * @var Vat
     */
    private $vat;

    /**
     * @var string|null
     */
    private $measureName = null;

    /**
     * @var string
     */
    private $calcMethod = null;

    /**
     * @var string
     */
    private $calcSubject = null;

    /**
     * @var Agent
     */
    private $agent = null;

    /**
     * @param string $name Item name
     * @param int|float $price Item price
     * @param int|float $quantity Item quanitity
     * @param int|float $total Total cost
     * @param int|float $discount Discount size in RUB
     * @param Vat $vat VAT
     *
     * @return Position
     */
    public function __construct($name, $price, $quantity, $total, $discount, Vat $vat)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->total = $total;
        $this->discount = $discount;
        $this->vat = $vat;
    }

    /**
     * @param string|null $value
     *
     * @return Position
     */
    public function setId($value)
    {
        $this->id = $value;

        return $this;
    }

    /**
     * @param string|null $value
     *
     * @return Position
     */
    public function setMeasureName($value)
    {
        $this->measureName = $value;

        return $this;
    }

    /**
     * @param srting $calc_method
     *
     * @return Position
     */
    public function setCalculationMethod($calc_method)
    {
        $this->calcMethod = $calc_method;

        return $this;
    }

    /**
     * @param srting $calc_subject
     *
     * @return Position
     */
    public function setCalculationSubject($calc_subject)
    {
        $this->calcSubject = $calc_subject;

        return $this;
    }

    /**
     * @param Agent $agent
     *
     * @return Position
     */
    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        $result = [
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'discount' => $this->discount,
            'vat' => $this->vat->getRate(),
        ];

        if ($this->id !== null) {
            $result['id'] = $this->id;
        }

        if ($this->measureName !== null) {
            $result['measure_name'] = $this->measureName;
        }

        if ($this->calcMethod !== null) {
            $result['calculation_method'] = $this->calcMethod;
        }

        if ($this->calcSubject !== null) {
            $result['calculation_subject'] = $this->calcSubject;
        }

        if ($this->agent !== null) {
            $result['agent'] = $this->agent->asArray();
        }

        return $result;
    }
}
