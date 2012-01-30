<?php

namespace Philt;

class PhpTemplate extends Template {

    function render($locals = array()) {
        ob_start();
        extract($this->binding($locals));
        include $this->source->file;
        return ob_get_clean();
    }

}