<?php

namespace Philt;

class Source {

    static $cache = array();

    public $file;

    function __construct($file) {
        $this->file = $file;
    }

    function __toString() {
        if (!isset(self::$cache[$this->file])) self::$cache[$this->file] = file_get_contents($this->file);
        return self::$cache[$this->file];
    }

}