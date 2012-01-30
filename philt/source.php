<?php

namespace Philt;

class Source {

    static $cache = array();
    static $compile_prefix = 'data://text/plain,';

    public $file;

    function __construct($file) {
        if (is_file($file)) {
            $this->file = $file;
        } else {
            $this->file = self::$compile_prefix.urlencode($file);
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