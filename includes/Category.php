<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

use stee1cat\CommerceMLExchange\Model\Group;

/**
 * Class Category
 */
class Category {

    const XML_ID_FIELD_NAME = 'xml_id';
    const ENTITY_TYPE = 'taxonomy_term';

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
     * @param $categories
     *
     * @throws NotUniqueCategoryException
     */
    public function import($categories) {
        $this->walk($categories);
    }

    protected function beforeUpdate() {
        $this->createXmlIdField();
    }

    /**
     * @param Group[] $categories
     * @param null|integer $parentId
     *
     * @throws NotUniqueCategoryException
     */
    protected function walk($categories, $parentId = null) {
        foreach ($categories as $category) {
            if ($record = $this->findByXmlId($category->getId())) {
                $newCategoryId = $record->tid;

                $this->update([
                    'tid' => $record->tid,
                    'name' => $category->getName(),
                    'path' => [
                        'alias' => $this->generateSlug($category, $record)
                    ],
                    'xml_id' => [
                        LANGUAGE_NONE => [[
                            'value' => $category->getId(),
                        ]]
                    ],
                    'parent' => $parentId,
                ]);
            }
            else {
                $newCategoryId = $this->update([
                    'name' => $category->getName(),
                    'path' => [
                        'alias' => $this->generateSlug($category)
                    ],
                    'xml_id' => [
                        LANGUAGE_NONE => [[
                            'value' => $category->getId(),
                        ]]
                    ],
                    'parent' => $parentId,
                ]);
            }

            echo '<pre>', print_r($category->getName(), 1), '</pre>';

            $children = $category->getGroups();
            if (count($children) > 0) {
                $this->walk($children, $newCategoryId);
            }
        }
    }

    protected function update($fields) {
        if (is_array($fields)) {
            $term = new \stdClass();
            $term->vid = $this->vocabulary->getId();
            $term->language = 'ru';
            $term->weight = 0;

            foreach ($fields as $field => $value) {
                if (in_array($field, ['tid', 'name', 'description', 'xml_id', 'parent', 'path'])) {
                    $term->{$field} = $value;
                }
            }

            taxonomy_term_save($term);

            return $term->tid;
        }

        return false;
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

    /**
     * @param string $xmlId
     *
     * @return boolean|\stdClass
     * @throws NotUniqueCategoryException
     */
    protected function findByXmlId($xmlId) {
        $query = new \EntityFieldQuery();
        $result = $query->entityCondition('entity_type', self::ENTITY_TYPE)
            ->propertyCondition('vid', $this->vocabulary->getId())
            ->fieldCondition(self::XML_ID_FIELD_NAME, 'value', $xmlId)
            ->execute();

        if (!empty($result[self::ENTITY_TYPE])) {
            if (count($result[self::ENTITY_TYPE]) > 1) {
                throw new NotUniqueCategoryException();
            }

            return taxonomy_term_load(reset($result[self::ENTITY_TYPE])->tid);
        }

        return false;
    }

    protected function attachXmlIdField() {
        $instance = [
            'field_name' => 'xml_id',
            'entity_type' => self::ENTITY_TYPE,
            'bundle' => $this->vocabulary->getMachineName(),
            'label' => 'XML ID',
            'description' => '',
            'required' => true,
            'widget' => [
                'type' => 'text_textfield',
                'weight' => 0,
            ]
        ];

        field_create_instance($instance);
    }

    /**
     * @param $previous
     * @param Group $category
     *
     * @return null|string|string[]
     */
    protected function generateSlug($category, $previous = null) {
        if ($previous && isset($previous->path) && $previous->path->alias) {
            $slug = $previous->path->alias;
        }
        else {
            $slug = Util::translit($category->getName());
        }

        return $slug;
    }

}