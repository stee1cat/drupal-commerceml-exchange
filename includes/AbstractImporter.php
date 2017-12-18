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

    /**
     * @var Vocabulary
     */
    protected $vocabulary;

    /**
     * @var array
     */
    protected $options = [];

    public function __construct($options) {
        $this->options = $options;
        $this->vocabulary = Vocabulary::create(taxonomy_vocabulary_machine_name_load($options['vocabulary_machine_name']));
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

}