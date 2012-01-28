<?php

namespace Philt;

class StringTemplate extends Template {

    function render($locals = array()) {
        extract($locals);
        eval('$rendered = "'.$this->source.'";');
        return $rendered;
    }

}