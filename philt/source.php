<?php

namespace Philt;

class Source {

    static $cache = array();

    public $file;

    function __construct($file) {
        if (is_file($file)) {
            $this->file = $file;
        } else {
            $this->file = 'philt://'.md5($file);
            self::$cache[$this->file] = $file;
        }
    }

    function contents() {
        if (!isset(self::$cache[$this->file])) self::$cache[$this->file] = file_get_contents($this->file);
        return self::$cache[$this->file];
    }

    function __toString() {
        return $this->contents();
    }

}