<?php

require_once 'philt/source.php';
require_once 'philt/template.php';
require_once 'philt/php_template.php';
require_once 'philt/string_template.php';

class Philt {

    static $mappings = array();

    static function clear($extension = null) {
        $extensions = func_get_args();
        if (empty($extensions)) {
            Philt::$mappings = array();
        } else {
            foreach ($extensions as $extension) unset(Philt::$mappings[$extension]);
        }
    }

    static function handler($file) {
        if ($handlers = self::handlers($file)) return $handlers[0];
    }

    static function handlers($file) {
        $pattern = basename($file);
        while (!empty($pattern) && !self::registered($pattern)) $pattern = preg_replace('/^[^\.]+\.?/', '', $pattern);
        if (isset(self::$mappings[$pattern])) return self::$mappings[$pattern];
    }

    static function register($handler, $extensions) {
        $extensions = func_get_args();
        $handler = array_shift($extensions);
        if (is_array($extensions[0])) $extensions = $extensions[0];
        foreach ($extensions as $extension) self::register_extension($handler, $extension);
    }

    static function register_default_extensions() {
        self::register('Philt\PhpTemplate', 'php');
        self::register('Philt\StringTemplate', 'str');
    }

    static function registered($extension) {
        return !empty(self::$mappings[$extension]);
    }

    static function template($file, $options = array()) {
        if ($handler = self::handler($file)) {
            return new $handler($file, $options);
        } else {
            throw new RuntimeException('No template handler registered for '.basename($file));
        }
    }

    static protected function register_extension($handler, $extension) {
        if (!isset(self::$mappings[$extension])) self::$mappings[$extension] = array();
        if (!in_array($handler, self::$mappings[$extension])) array_unshift(self::$mappings[$extension], $handler);
    }
}

Philt::register_default_extensions();