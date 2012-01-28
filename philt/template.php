<?php

namespace Philt;

class Template {

    public $file;
    public $options;
    public $source;

    function __construct($file, $options = array()) {
        $this->file = $file;
        $this->options = $options;
        $this->source = new Source($file);
    }

    function render($locals = array()) {
        throw new \BadMethodCallException('Template handler must implement a render method');
    }

}