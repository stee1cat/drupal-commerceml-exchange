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

    public function getCategoryReferenceField() {
        return variable_get(commerce_exchange_module('_category_reference_field'), 'field_product_category');
    }

    public function getProductReferenceField() {
        return variable_get(commerce_exchange_module('_product_reference_field'), 'field_product');
    }

    public function getImageReferenceField() {
        return variable_get(commerce_exchange_module('_image_reference_field'), 'field_images');
    }

    public function getPriceType() {
        return variable_get(commerce_exchange_module('_price_type'), '');
    }

    public function isGenerateSku() {
        return variable_get(commerce_exchange_module('_generate_sku'), false);
    }

}