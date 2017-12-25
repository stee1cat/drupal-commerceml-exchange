<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

use stee1cat\CommerceMLExchange\Model\Offer;

/**
 * Class OfferImporter
 * @package CommerceExchange
 */
class OfferImporter extends AbstractImporter {

    /**
     * @param Offer[] $offers
     *
     * @throws NotUniqueProductException
     */
    public function import($offers) {
        $this->walk($offers);
    }

    /**
     * @param Offer[] $offers
     *
     * @throws NotUniqueProductException
     */
    protected function walk($offers) {
        foreach ($offers as $offer) {
            $product = $this->findProductByXmlId($offer->getProductId());

            if ($product && $offer->getPrices()) {
                $price = new PriceDecorator($offer->getPrices()[0]);

                $product->commerce_price = [
                    LANGUAGE_NONE => [[
                        'amount' => $price->getValue(),
                        'currency_code' => $price->getCurrency(),
                    ]]
                ];

                commerce_product_save($product);
            }
        }
    }

}