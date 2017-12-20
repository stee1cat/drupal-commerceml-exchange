<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

use CommerceExchange\Settings;

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

    $form[commerce_exchange_module('_catalog_settings')] = [
        '#type' => 'fieldset',
        '#title' => t('Catalog settings'),
    ];

    $form[commerce_exchange_module('_catalog_settings')][commerce_exchange_module('_product_entity_type')] = array(
        '#type' => 'select',
        '#title' => t('Product entity type'),
        '#default_value' => $settings->getProductEntityType(),
        '#options' => commerce_exchange_node_get_types(),
    );

    $form[commerce_exchange_module('_catalog_settings')][commerce_exchange_module('_product_node_type')] = array(
        '#type' => 'select',
        '#title' => t('Product node reference type'),
        '#default_value' => $settings->getProductNodeType(),
        '#options' => commerce_exchange_node_get_types(),
    );

    $form[commerce_exchange_module('_catalog_settings')][commerce_exchange_module('_category_taxonomy_type')] = array(
        '#type' => 'select',
        '#title' => t('Catalog taxonomy type'),
        '#default_value' => $settings->getCategoryTaxonomyType(),
        '#options' => commerce_exchange_vocabulary_get_names(),
    );

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
function commerce_exchange_vocabulary_get_names() {
    $vocabularyNames = [];
    foreach (taxonomy_vocabulary_get_names() as $vocabulary) {
        $vocabularyNames[$vocabulary->machine_name] = sprintf('%s (%s)', $vocabulary->name, $vocabulary->machine_name);
    }

    return $vocabularyNames;
}