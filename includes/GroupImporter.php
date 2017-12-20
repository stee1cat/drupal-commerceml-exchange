<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

use stee1cat\CommerceMLExchange\Model\Group;

/**
 * Class GroupImporter
 */
class GroupImporter extends AbstractImporter {

    /**
     * @param $categories
     *
     * @throws NotUniqueCategoryException
     */
    public function import($categories) {
        $this->beforeUpdate();
        $this->walk($categories);
    }


    /**
     * @param Group[] $categories
     * @param null|integer $parentId
     *
     * @throws NotUniqueCategoryException
     */
    protected function walk($categories, $parentId = null) {
        foreach ($categories as $category) {
            if ($record = $this->findGroupByXmlId($category->getId())) {
                $newCategoryId = $record->tid;

                $this->update([
                    'tid' => $record->tid,
                    'name' => $category->getName(),
                    'path' => [
                        'alias' => $this->generateSlug($category, $record)
                    ],
                    self::XML_ID_FIELD_NAME => [
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
                    self::XML_ID_FIELD_NAME => [
                        LANGUAGE_NONE => [[
                            'value' => $category->getId(),
                        ]]
                    ],
                    'parent' => $parentId,
                ]);
            }

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
                if (in_array($field, ['tid', 'name', 'description', self::XML_ID_FIELD_NAME, 'parent', 'path'])) {
                    $term->{$field} = $value;
                }
            }

            taxonomy_term_save($term);

            return $term->tid;
        }

        return false;
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