<?php

class SourceTest extends ztest\UnitTestCase {

    function setup() {
        $this->file = PHILT_TEMPLATES_ROOT.DIRECTORY_SEPARATOR.'hello_world.php';
        $this->source = new Philt\Source($this->file);
        $this->contents = file_get_contents($this->file);
        $this->compiled_source = new Philt\Source($this->contents);
    }

    // #__construct

        function test_constructor() {
            assert_equal($this->file, $this->source->file);
            ensure(!isset(Philt\Source::$cache[$this->source->file]));
            assert_equal(Philt\Source::$compile_prefix.urlencode($this->contents), $this->compiled_source->file);
            ensure(isset(Philt\Source::$cache[$this->compiled_source->file]));
        }

    // #__toString

        function test_to_string() {
            assert_equal($this->source->contents(), (string) $this->source);
        }

    // #contents

        function test_contents() {
            unset(Philt\Source::$cache[$this->source->file]);
            assert_all_equal($this->contents, $this->source->contents(), Philt\Source::$cache[$this->source->file]);
            assert_equal($this->contents, $this->compiled_source->contents());
        }

}