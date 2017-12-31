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

    public static function notice($message) {
        watchdog(WATCHDOG_NOTICE, $message);
    }

    public static function exception(\Exception $exception) {
        watchdog_exception(WATCHDOG_CRITICAL, $exception);
    }

}