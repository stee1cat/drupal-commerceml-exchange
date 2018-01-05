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
     * @throws NotUniqueGroupException
     * @throws \Exception
     */
    public function import($products) {
        $settings = $this->settings;

        foreach ($products as $product) {
            $parents = $this->findParentGroups($product);

            $fields = [
                'title' => $product->getName(),
                'language' => LANGUAGE_NONE,
                'uid' => 1,
                'status' => $product->isMarkAsDelete() ? 0 : 1,
                'commerce_price' => [
                    LANGUAGE_NONE => [[
                        'amount' => 0,
                        'currency_code' => 'RUB',
                    ]]
                ],
                self::XML_ID_FIELD_NAME => [
                    LANGUAGE_NONE => [[
                        'bundle' => $settings->getProductEntityType(),
                        'value' => $product->getId(),
                    ]]
                ],
                $settings->getCategoryReferenceField() => [
                    LANGUAGE_NONE => [[
                        'bundle' => $settings->getProductEntityType(),
                        'tid' => $parents[0]->tid
                    ]]
                ],
                'body' => [
                    LANGUAGE_NONE => [[
                        'bundle' => $settings->getProductEntityType(),
                        'value' => $product->getDescription(),
                    ]]
                ],
            ];

            $this->invoke(Hooks::BEFORE_IMPORT_PRODUCT, new Event([
                'fields' => &$fields,
                'product' => $product,
            ]));

            if ($record = $this->findProductByXmlId($product->getId())) {
                $this->update($record, $fields);
            }
            else {
                $fields['sku'] = (new SkuGenerator($product))->generate();

                $this->create($fields);
            }
        }
    }

    /**
     * @param array $fields
     *
     * @return integer
     * @throws \Exception
     */
    protected function create($fields) {
        $settings = $this->settings;
        $product = commerce_product_new($settings->getProductEntityType());
        $node = $this->createNode();

        foreach ($fields as $field => $value) {
            if (!in_array($field, ['type', 'body'])) {
                $product->{$field} = $value;
            }

            if (in_array($field, ['title', 'language', 'uid', $settings->getCategoryReferenceField(), 'body'])) {
                $node->{$field} = $value;
            }
        }

        commerce_product_save($product);
        $node->{$settings->getProductReferenceField()}[LANGUAGE_NONE][0]['product_id'] = $product->product_id;
        node_save($node);

        return $product->product_id;
    }

    /**
     * @param \stdClass $product
     * @param array $fields
     *
     * @throws NotUniqueNodeException
     * @throws \Exception
     */
    protected function update($product, $fields) {
        $nodeFields = ['title', 'language', 'uid', $this->settings->getCategoryReferenceField(), 'body', 'status'];
        $node = $this->findNodeByProductId($product->product_id);

        foreach ($fields as $field => $value) {
            if (!in_array($field, ['type', 'sku', 'body'])) {
                $product->{$field} = $value;
            }

            if (in_array($field, $nodeFields)) {
                $node->{$field} = $value;
            }
        }

        node_save($node);
        commerce_product_save($product);
    }

    /**
     * @param Product $product
     *
     * @return array
     * @throws NotUniqueGroupException
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

    /**
     * @return object
     */
    protected function createNode() {
        $node = (object) [
            'type' => $this->settings->getProductNodeType(),
        ];
        node_object_prepare($node);

        return $node;
    }

}