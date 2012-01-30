<?php

require_once 'philt/binding.php';
require_once 'philt/source.php';
require_once 'philt/template.php';
require_once 'philt/nested_template.php';
require_once 'philt/php_template.php';
require_once 'philt/string_template.php';

class Philt {
    static function initialize_handler($potential_handlers, $file, &$options) {
        foreach ($potential_handlers as $handler) {
            try {
                return new $handler($file, $options);
            } catch (Exception $exception) {
                if (!isset($first_exception)) $first_exception = $exception;
            }
        }
        throw $first_exception;
    }

    public $handlers = array();

    function __construct() {
        $this->register_default_handlers();
    }

    function clear($extensions = null) {
        $extensions = func_get_args();
        if (empty($extensions)) {
            $this->handlers = array();
        } else {
            foreach ($extensions as $extension) unset($this->handlers[$extension]);
        }
    }

    function handler($file, $options) {
        if ($handlers = $this->handlers($file)) {
            if (count($handlers) > 1) {
                return new Philt\NestedTemplate($handlers, $file, $options);
            } else {
                return self::initialize_handler(array_shift($handlers), $file, $options);
            }
        }
    }

    function handlers($file) {
        $file = basename($file);
        $handlers = array();
        while ($extension = $this->next_registered_extension($file)) {
            $handlers[$extension] = $this->handlers[$extension];
            $file = preg_replace('#\.?'.$extension.'$#', '', $file);
        }
        if (!empty($handlers)) return $handlers;
    }

    function register($handler, $extensions) {
        $extensions = func_get_args();
        $handler = array_shift($extensions);
        foreach ($extensions as $extension) $this->register_handler($handler, $extension);
    }

    function register_default_handlers() {
        $this->register('Philt\PhpTemplate', 'php');
        $this->register('Philt\StringTemplate', 'str');
    }

    function registered($extension) {
        return isset($this->handlers[$extension]);
    }

    function template($file, $options = array()) {
        if ($handler = $this->handler($file, $options)) {
            return $handler;
        } else {
            throw new RuntimeException('No template handlers registered for '.basename($file));
        }
    }

    protected function next_registered_extension($extension) {
        while (!empty($extension)) {
            if ($this->registered($extension)) {
                return $extension;
            } else {
                $extension = preg_replace('/^[^\.]*\.?/', '', $extension);
            }
        }
    }

    protected function register_handler($handler, $extension) {
        if (!isset($this->handlers[$extension])) $this->handlers[$extension] = array();
        if (!in_array($handler, $this->handlers[$extension])) array_unshift($this->handlers[$extension], $handler);
    }
}