<?php

class Singleton {
    static $container;

    public function __construct() {
        $this->container = [];
    }
}

?>