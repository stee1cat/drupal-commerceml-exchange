<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

use stee1cat\CommerceMLExchange\Model\Product;

/**
 * Class ProductImporter
 * @package CommerceExchange
 */
class ProductImporter extends AbstractImporter {

    /**
     * @param Product[] $products
     *
     * @throws NotUniqueCategoryException
     * @throws \Exception
     */
    public function import($products) {
        foreach ($products as $product) {
            $parents = $this->findParentGroups($product);

            if ($product->getVendorCode()) {
                $this->update([
                    'sku' => $product->getVendorCode(),
                    'title' => $product->getName(),
                    'language' => LANGUAGE_NONE,
                    'uid' => 1,
                    'commerce_price' => [
                        LANGUAGE_NONE => [[
                            'amount' => 0,
                            'currency_code' => 'RUB',
                        ]]
                    ],
                    self::XML_ID_FIELD_NAME => [
                        LANGUAGE_NONE => [[
                            'bundle' => $this->settings->getProductEntityType(),
                            'value' => $product->getId(),
                        ]]
                    ],
                    'field_product_category' => [
                        LANGUAGE_NONE => [[
                            'bundle' => $this->settings->getProductEntityType(),
                            'tid' => $parents[0]->tid
                        ]]
                    ],
                ]);
            }
        }
    }

    /**
     * @param $fields
     *
     * @return mixed
     * @throws \Exception
     */
    protected function update($fields) {
        $product = commerce_product_new($this->settings->getProductEntityType());
        $node = (object) ['type' => $this->settings->getProductNodeType()];
        node_object_prepare($node);

        foreach ($fields as $field => $value) {
            if (!in_array($field, ['type'])) {
                $product->{$field} = $value;
            }

            if (in_array($field, ['title', 'language', 'uid', 'field_product_category'])) {
                $node->{$field} = $value;
            }
        }

        commerce_product_save($product);
        $node->field_product[LANGUAGE_NONE][0]['product_id'] = $product->product_id;
        node_save($node);

        return $product->product_id;
    }

    /**
     * @param Product $product
     *
     * @return array
     * @throws NotUniqueCategoryException
     */
    protected function findParentGroups(Product $product) {
        $result = [];
        $groups = $product->getGroups();

        foreach ($groups as $groupId) {
            if ($group = $this->findGroupByXmlId($groupId)) {
                $result[] = $group;
            }
        }

        return $result;
    }

}