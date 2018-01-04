<?php
/**
 * Copyright (c) 2018 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

/**
 * Class Event
 * @package CommerceExchange
 */
class Event {

    /**
     * @var array
     */
    protected $data;

    public function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

}