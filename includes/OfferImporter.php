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
        $priceType = $this->settings->getPriceType();

        foreach ($offers as $offer) {
            $product = $this->findProductByXmlId($offer->getProductId());
            $prices = $offer->getPrices();

            if ($product && $prices && isset($prices[$priceType])) {
                $price = new PriceDecorator($prices[$priceType]);

                $product->commerce_price = [
                    LANGUAGE_NONE => [[
                        'amount' => $price->getValue(),
                        'currency_code' => $price->getCurrency(),
                    ]],
                ];

                commerce_product_save($product);
            }
            else if ($product) {
                Logger::notice('Price not found for product "%title"', [
                    '%title' => $product->title,
                ]);
            }
        }
    }

}