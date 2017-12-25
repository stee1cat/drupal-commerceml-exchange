<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

use stee1cat\CommerceMLExchange\Model\Price;

/**
 * Class PriceDecorator
 * @package CommerceExchange
 */
class PriceDecorator {

    /**
     * @var Price
     */
    protected $price;

    public function __construct(Price $price) {
        $this->price = $price;
    }

    /**
     * @return float|integer
     */
    public function getValue() {
        return $this->price->getValue() * 100;
    }

    /**
     * @return string
     */
    public function getCurrency() {
        $currency = mb_strtoupper($this->price->getCurrency(), 'UTF-8');

        switch ($currency) {
            case 'РУБ':
                $result = 'RUB';
                break;
            default:
                $result = $currency;
        }

        return $result;
    }

}