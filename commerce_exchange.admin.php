<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

function commerce_exchange_settings_form($form) {
    $form[commerce_exchange_module('_auth')] = [
        '#type' => 'fieldset',
        '#title' => t('Authentication'),
    ];

    $form[commerce_exchange_module('_auth')][commerce_exchange_module('_username')] = [
        '#type' => 'textfield',
        '#title' => t('Username'),
        '#default_value' => variable_get(commerce_exchange_module('_username'), ''),
    ];

    $form[commerce_exchange_module('_auth')][commerce_exchange_module('_password')] = [
        '#type' => 'password',
        '#title' => t('Password'),
        '#default_value' => variable_get(commerce_exchange_module('_password'), ''),
    ];

    $form[commerce_exchange_module('_import_settings')] = [
        '#type' => 'fieldset',
        '#title' => t('Import settings'),
    ];

    $form[commerce_exchange_module('_import_settings')][commerce_exchange_module('_file_path')] = [
        '#type' => 'textfield',
        '#title' => t('Path to temporary store imported files'),
        '#default_value' => variable_get(commerce_exchange_module('_file_path'), 'public://commerceml-exchange/'),
    ];

    $form[commerce_exchange_module('_catalog_settings')] = [
        '#type' => 'fieldset',
        '#title' => t('Catalog settings'),
    ];

    $options = [];
    foreach (node_type_get_types() as $type) {
        $options[$type->type] = $type->name;
    }

    $form[commerce_exchange_module('_catalog_settings')][commerce_exchange_module('_product_node_type')] = array(
        '#type' => 'select',
        '#title' => t('Product node type'),
        '#default_value' => variable_get(commerce_exchange_module('_product_node_type'), 'product_display'),
        '#options' => $options,
    );

    return system_settings_form($form);
}