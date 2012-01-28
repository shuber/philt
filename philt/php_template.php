<?php

namespace Philt;

class PhpTemplate extends Template {

    function render($locals = array()) {
        ob_start();
        extract($locals);
        include $this->file;
        return ob_get_clean();
    }

}