<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

use CommerceExchange\Settings;

/**
 * @param array $form
 *
 * @return array
 */
function commerce_exchange_settings_form($form) {
    commerce_exchange_load_classes();

    $settings = new Settings();

    $form[commerce_exchange_module('_auth')] = [
        '#type' => 'fieldset',
        '#title' => t('Authentication'),
    ];

    $form[commerce_exchange_module('_auth')][commerce_exchange_module('_username')] = [
        '#type' => 'textfield',
        '#title' => t('Username'),
        '#default_value' => $settings->getUsername(),
    ];

    $form[commerce_exchange_module('_auth')][commerce_exchange_module('_password')] = [
        '#type' => 'password',
        '#title' => t('Password'),
        '#default_value' => $settings->getPassword(),
    ];

    $form[commerce_exchange_module('_import_settings')] = [
        '#type' => 'fieldset',
        '#title' => t('Import settings'),
    ];

    $form[commerce_exchange_module('_import_settings')][commerce_exchange_module('_file_path')] = [
        '#type' => 'textfield',
        '#title' => t('Path to temporary store imported files'),
        '#default_value' => $settings->getFilePath(),
    ];

    $nodeTypes = commerce_exchange_node_get_types();
    $catalogGroup = commerce_exchange_module('_catalog_settings');

    $form[$catalogGroup] = [
        '#type' => 'fieldset',
        '#title' => t('Catalog settings'),
    ];

    $form[$catalogGroup][commerce_exchange_module('_product_entity_type')] = [
        '#type' => 'select',
        '#title' => t('Product entity type'),
        '#default_value' => $settings->getProductEntityType(),
        '#options' => $nodeTypes,
    ];

    $form[$catalogGroup][commerce_exchange_module('_product_node_type')] = [
        '#type' => 'select',
        '#title' => t('Product node reference type'),
        '#default_value' => $settings->getProductNodeType(),
        '#options' => $nodeTypes,
    ];

    $form[$catalogGroup][commerce_exchange_module('_product_reference_field')] = [
        '#type' => 'select',
        '#title' => t('Product node reference field'),
        '#default_value' => $settings->getProductReferenceField(),
        '#options' => commerce_exchange_product_reference_fields(),
    ];

    $form[$catalogGroup][commerce_exchange_module('_generate_sku')] = [
        '#type' => 'checkbox',
        '#title' => t('Generate SKU'),
        '#default_value' => $settings->isGenerateSku(),
    ];

    $form[$catalogGroup][commerce_exchange_module('_category_taxonomy_type')] = [
        '#type' => 'select',
        '#title' => t('Catalog category type'),
        '#default_value' => $settings->getCategoryTaxonomyType(),
        '#options' => commerce_exchange_vocabulary_get_names(),
    ];

    $form[$catalogGroup][commerce_exchange_module('_category_reference_field')] = [
        '#type' => 'select',
        '#title' => t('Category reference field'),
        '#default_value' => $settings->getCategoryReferenceField(),
        '#options' => commerce_exchange_category_reference_fields(),
    ];

    $form[$catalogGroup][commerce_exchange_module('_price_type')] = [
        '#type' => 'textfield',
        '#title' => t('Price type'),
        '#default_value' => $settings->getPriceType(),
    ];

    return system_settings_form($form);
}

/**
 * @return array
 */
function commerce_exchange_node_get_types() {
    $nodeTypes = [];
    foreach (node_type_get_types() as $type) {
        $nodeTypes[$type->type] = sprintf('%s (%s)', $type->name, $type->type);
    }

    return $nodeTypes;
}

/**
 * @return string[]
 */
function commerce_exchange_category_reference_fields() {
    return commerce_exchange_get_fields_by_type('taxonomy_term_reference');
}

/**
 * @return string[]
 */
function commerce_exchange_product_reference_fields() {
    return commerce_exchange_get_fields_by_type('commerce_product_reference');
}

/**
 * @param string $type
 *
 * @return string[]
 */
function commerce_exchange_get_fields_by_type($type) {
    $result = [];
    $fields = field_info_fields();

    foreach ($fields as $name => $field) {
        if ($type && $field['type'] === $type && $field['active'] == 1) {
            $result[$name] = $name;
        }
    }

    return $result;
}

/**
 * @return string[]
 */
function commerce_exchange_vocabulary_get_names() {
    $vocabularyNames = [];
    foreach (taxonomy_vocabulary_get_names() as $vocabulary) {
        $vocabularyNames[$vocabulary->machine_name] = sprintf('%s (%s)', $vocabulary->name, $vocabulary->machine_name);
    }

    return $vocabularyNames;
}