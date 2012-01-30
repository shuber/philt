<?php

namespace Philt;

class Template {

    public $binding;
    public $file;
    public $options;
    public $source;

    function __construct($file, &$options = array()) {
        $this->binding = new Binding;
        $this->file = $file;
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