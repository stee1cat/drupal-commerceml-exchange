<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

use stee1cat\CommerceMLExchange\Model\Product;

/**
 * Class SkuGenerator
 * @package CommerceExchange
 */
class SkuGenerator {

    const TEMPLATE = '{sku}/{index}';

    /**
     * @var Product
     */
    protected $product;

    public function __construct(Product $product) {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function generate() {
        $originSku = $this->product->getVendorCode();

        if ($originSku) {
            $format = function ($i) use ($originSku) {
                return $this->format($originSku, $i);
            };

            $result = $this->findUnique($format, 100);
        }
        else {
            $format = function () {
                return $this->format($this->random(), 1);
            };

            $result = $this->findUnique($format, 3);
        }

        return $result;
    }

    /**
     * @param string $sku
     * @param integer $index
     *
     * @return string
     */
    protected function format($sku, $index) {
        return str_replace(['{sku}', '{index}'], [$sku, $index], self::TEMPLATE);
    }

    /**
     * @param integer $length
     *
     * @return string
     */
    protected function random($length = 6) {
        $abc = 'abcdefghijklmopqrstuvxwyz0123456789';
        $abcLength = strlen($abc) - 1;

        $result = '';

        for ($i = 1; $i <= $length; $i++) {
            $index = rand(0, $abcLength);
            $result .= substr($abc, $index, 1);
        }

        return $result;
    }

    /**
     * @param callable $formatFunction
     * @param integer $maxIterations
     *
     * @return string
     */
    protected function findUnique($formatFunction, $maxIterations = 10) {
        $i = 1;
        $result = $formatFunction($i);

        do {
            $i++;

            if (!$this->isUnique($result)) {
                $result = $formatFunction($i);
            }
            else {
                break;
            }
        }
        while ($i < $maxIterations);

        return $result;
    }

    /**
     * @param string $sku
     *
     * @return boolean
     */
    protected function isUnique($sku) {
        $query = '
            SELECT *
            FROM {commerce_product}
            WHERE sku = :sku
        ';

        return !db_query($query, [':sku' => $sku])->fetchField();
    }

}