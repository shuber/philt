<?php

namespace Philt;

class NestedTemplate extends Template {

    public $handlers;

    function __construct($handlers, $file, &$options = array()) {
        parent::__construct($file, $options);
        $this->handlers = $handlers;
    }

    function render($locals = array()) {
        $contents = $this->file;
        foreach ($this->handlers as $handler) $contents = \Philt::initialize_handler($handler, $contents, $this->options)->render($locals);
        return $contents;
    }

}