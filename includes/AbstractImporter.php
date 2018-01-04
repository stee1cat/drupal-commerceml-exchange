<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

/**
 * Class AbstractImporter
 * @package CommerceExchange
 */
abstract class AbstractImporter {

    const XML_ID_FIELD_NAME = 'xml_id';
    const GROUP_ENTITY_TYPE = 'taxonomy_term';
    const PRODUCT_ENTITY_TYPE = 'commerce_product';
    const NODE_ENTITY_TYPE = 'node';

    /**
     * @var Vocabulary
     */
    protected $vocabulary;

    /**
     * @var Settings
     */
    protected $settings;

    public function __construct(Settings $settings) {
        $this->settings = $settings;
        $this->createVocabulary();
    }

    /**
     * @throws \FieldException
     */
    protected function beforeUpdate() {
        $this->createXmlIdField();
    }

    /**
     * @param string $xmlId
     *
     * @return boolean|\stdClass
     * @throws NotUniqueGroupException
     */
    protected function findGroupByXmlId($xmlId) {
        $query = new \EntityFieldQuery();
        $result = $query->entityCondition('entity_type', self::GROUP_ENTITY_TYPE)
            ->propertyCondition('vid', $this->vocabulary->getId())
            ->fieldCondition(self::XML_ID_FIELD_NAME, 'value', $xmlId)
            ->execute();

        if (!empty($result[self::GROUP_ENTITY_TYPE])) {
            if (count($result[self::GROUP_ENTITY_TYPE]) > 1) {
                throw new NotUniqueGroupException();
            }

            return taxonomy_term_load(reset($result[self::GROUP_ENTITY_TYPE])->tid);
        }

        return false;
    }

    /**
     * @param string $xmlId
     *
     * @return boolean|mixed
     * @throws NotUniqueProductException
     */
    protected function findProductByXmlId($xmlId) {
        $query = new \EntityFieldQuery();
        $result = $query->entityCondition('entity_type', self::PRODUCT_ENTITY_TYPE)
            ->propertyCondition('type', $this->settings->getProductEntityType())
            ->fieldCondition(self::XML_ID_FIELD_NAME, 'value', $xmlId)
            ->execute();

        $entities = $result[self::PRODUCT_ENTITY_TYPE];

        if (!empty($entities)) {
            if (count($entities) > 1) {
                throw new NotUniqueProductException();
            }

            $productId = (integer) reset($entities)->product_id;

            return commerce_product_load($productId);
        }

        return false;
    }

    /**
     * @param integer $productId
     *
     * @return boolean|mixed
     * @throws NotUniqueNodeException
     */
    protected function findNodeByProductId($productId) {
        $query = new \EntityFieldQuery();
        $result = $query->entityCondition('entity_type', self::NODE_ENTITY_TYPE)
            ->propertyCondition('type', $this->settings->getProductEntityType())
            ->fieldCondition('field_product', 'product_id', $productId)
            ->execute();

        $entities = $result[self::NODE_ENTITY_TYPE];

        if (!empty($entities)) {
            if (count($entities) > 1) {
                throw new NotUniqueNodeException();
            }

            $entity = reset($entities);

            return node_load($entity->nid, $entity->vid);
        }

        return false;
    }

    protected function createVocabulary() {
        $fields = taxonomy_vocabulary_machine_name_load($this->settings->getCategoryTaxonomyType());

        $this->vocabulary = Vocabulary::create($fields);
    }

    /**
     * @throws \FieldException
     */
    protected function createXmlIdField() {
        $field = field_info_field(self::XML_ID_FIELD_NAME);
        if (!$field) {
            $data = [
                'label' => 'XML ID',
                'field_name' => self::XML_ID_FIELD_NAME,
                'type' => 'text',
                'active' => 1,
            ];

            field_create_field($data);
            $this->attachXmlIdField();
        }
    }

    /**
     * @throws \FieldException
     */
    protected function attachXmlIdField() {
        $defaultOptions = [
            'field_name' => self::XML_ID_FIELD_NAME,
            'label' => 'XML ID',
            'description' => '',
            'required' => true,
            'display' => [
                'default' => [
                    'label' => 'hidden',
                    'type' => 'hidden',
                ],
            ],
            'widget' => [
                'type' => 'text_textfield',
                'weight' => 0,
            ]
        ];

        field_create_instance(array_merge($defaultOptions, [
            'entity_type' => self::GROUP_ENTITY_TYPE,
            'bundle' => $this->vocabulary->getMachineName(),

        ]));

        field_create_instance(array_merge($defaultOptions, [
            'entity_type' => self::PRODUCT_ENTITY_TYPE,
            'bundle' => $this->settings->getProductEntityType(),
        ]));
    }

    /**
     * @param string $hook
     * @param Event $event
     */
    protected function invoke($hook, &$event) {
        if (!$event) {
            $event = new Event();
        }

        foreach (module_implements($hook) as $module) {
            $function = $module . '_' . $hook;

            call_user_func($function, $event);
        }
    }

}