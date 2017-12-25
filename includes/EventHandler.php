<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

use stee1cat\CommerceMLExchange\Catalog\Result;
use stee1cat\CommerceMLExchange\Event\Event;

/**
 * Class EventHandler
 * @package CommerceExchange
 */
class EventHandler {

    /**
     * @var Settings
     */
    protected $settings;

    public function __construct(Settings $settings) {
        $this->settings = $settings;
    }

    /**
     * @param Event $event
     *
     * @throws NotUniqueGroupException
     * @throws \Exception
     * @throws \FieldException
     */
    public function onParse(Event $event) {
        /** @var Result $data */
        $data = $event->getData();

        $categories = $data->getGroups();
        $products = $data->getProducts();
        $offers = $data->getOffers();

        if (count($categories) > 0) {
            $groupImporter = new GroupImporter($this->settings);

            $groupImporter->import($categories);
        }

        if (count($products) > 0) {
            $productImporter = new ProductImporter($this->settings);

            $productImporter->import($products);
        }

        if (count($offers) > 0) {
            $offerImporter = new OfferImporter($this->settings);

            $offerImporter->import($offers);
        }
    }

}