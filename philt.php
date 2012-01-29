<?php

require_once 'philt/source.php';
require_once 'philt/template.php';
require_once 'philt/php_template.php';
require_once 'philt/string_template.php';

class Philt {
    public $mappings = array();

    function __construct() {
        $this->register_default_extensions();
    }

    function clear($extensions = null) {
        $extensions = func_get_args();
        if (empty($extensions)) {
            $this->mappings = array();
        } else {
            foreach ($extensions as $extension) unset($this->mappings[$extension]);
        }
    }

    function handler($file) {
        if ($handlers = $this->handlers($file)) return $handlers[0];
    }

    function handlers($file) {
        $pattern = basename($file);
        while (!empty($pattern) && !$this->registered($pattern)) $pattern = preg_replace('/^[^\.]+\.?/', '', $pattern);
        if (isset($this->mappings[$pattern])) return $this->mappings[$pattern];
    }

    function register($handler, $extensions) {
        $extensions = func_get_args();
        $handler = array_shift($extensions);
        foreach ($extensions as $extension) $this->register_extension($handler, $extension);
    }

    function register_default_extensions() {
        $this->register('Philt\PhpTemplate', 'php');
        $this->register('Philt\StringTemplate', 'str');
    }

    function registered($extension) {
        return isset($this->mappings[$extension]);
    }

    function template($file, $options = array()) {
        if ($handler = $this->handler($file)) {
            return new $handler($file, $options);
        } else {
            throw new RuntimeException('No template handler registered for '.basename($file));
        }
    }

    protected function register_extension($handler, $extension) {
        if (!isset($this->mappings[$extension])) $this->mappings[$extension] = array();
        if (!in_array($handler, $this->mappings[$extension])) array_unshift($this->mappings[$extension], $handler);
    }
}