<?php

namespace Philt;

class HamlTemplate extends Template {

    function render($locals = array()) {
        $parser = new \phphaml\haml\Parser($this->source, $this->binding($locals), $this->options);
        return $parser->render();
    }

}