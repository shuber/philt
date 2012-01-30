<?php

namespace Philt;

abstract class Template {

    public $binding;
    public $options;
    public $source;

    function __construct($file, &$options = array()) {
        $this->binding = new Binding;
        $this->options = $options;
        $this->source = new Source($file);
    }

    function binding($locals = array()) {
        return array_merge($this->binding->locals, $locals, array(
            'binding' => $this->binding,
            'locals' => array_keys($locals)
        ));
    }

    function render($locals = array()) {
        throw new \BadMethodCallException('Undefined method '.get_called_class().'::render()');
    }

}