<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

/**
 * Class Logger
 * @package CommerceExchange
 */
class Logger {

    const TAG = 'commerce_exchange';

    public static function info($message, $variables = []) {
        watchdog(self::TAG, $message, $variables, WATCHDOG_INFO);
    }

    public static function notice($message, $variables = []) {
        watchdog(self::TAG, $message, $variables, WATCHDOG_NOTICE);
    }

    public static function exception(\Exception $exception) {
        watchdog_exception(self::TAG, $exception);
    }

}