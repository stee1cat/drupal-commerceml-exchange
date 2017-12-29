<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

/**
 * Class Settings
 * @package CommerceExchange
 */
class Settings {

    public function getUsername() {
        return variable_get(commerce_exchange_module('_username'), '');
    }

    public function getPassword() {
        return variable_get(commerce_exchange_module('_password'), '');
    }

    public function getFilePath() {
        return variable_get(commerce_exchange_module('_file_path'), 'public://commerceml-exchange/');
    }

    public function getProductEntityType() {
        return variable_get(commerce_exchange_module('_product_entity_type'), 'product');
    }

    public function getProductNodeType() {
        return variable_get(commerce_exchange_module('_product_node_type'), 'product_display');
    }

    public function getCategoryTaxonomyType() {
        return variable_get(commerce_exchange_module('_category_taxonomy_type'), 'category');
    }

    public function getPriceType() {
        return variable_get(commerce_exchange_module('_price_type'), '');
    }

}