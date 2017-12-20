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

    protected function beforeUpdate() {
        $this->createXmlIdField();
    }

    /**
     * @param string $xmlId
     *
     * @return boolean|\stdClass
     * @throws NotUniqueCategoryException
     */
    protected function findGroupByXmlId($xmlId) {
        $query = new \EntityFieldQuery();
        $result = $query->entityCondition('entity_type', GroupImporter::GROUP_ENTITY_TYPE)
            ->propertyCondition('vid', $this->vocabulary->getId())
            ->fieldCondition(AbstractImporter::XML_ID_FIELD_NAME, 'value', $xmlId)
            ->execute();

        if (!empty($result[GroupImporter::GROUP_ENTITY_TYPE])) {
            if (count($result[GroupImporter::GROUP_ENTITY_TYPE]) > 1) {
                throw new NotUniqueCategoryException();
            }

            return taxonomy_term_load(reset($result[self::GROUP_ENTITY_TYPE])->tid);
        }

        return false;
    }

    protected function createVocabulary() {
        $fields = taxonomy_vocabulary_machine_name_load($this->settings->getCategoryTaxonomyType());

        $this->vocabulary = Vocabulary::create($fields);
    }

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

    protected function attachXmlIdField() {
        field_create_instance([
            'field_name' => self::XML_ID_FIELD_NAME,
            'entity_type' => self::GROUP_ENTITY_TYPE,
            'bundle' => $this->vocabulary->getMachineName(),
            'label' => 'XML ID',
            'description' => '',
            'required' => true,
            'widget' => [
                'type' => 'text_textfield',
                'weight' => 0,
            ]
        ]);

        field_create_instance([
            'field_name' => self::XML_ID_FIELD_NAME,
            'entity_type' => self::PRODUCT_ENTITY_TYPE,
            'bundle' => $this->settings->getProductEntityType(),
            'label' => 'XML ID',
            'description' => '',
            'required' => true,
            'widget' => [
                'type' => 'text_textfield',
                'weight' => 0,
            ]
        ]);
    }

}