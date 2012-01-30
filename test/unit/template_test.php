<?php

namespace TemplateTest {
    class TestTemplate extends \Philt\Template { }
}

namespace {
    class TemplateTest extends ztest\UnitTestCase {

        function setup() {
            $this->file = PHILT_TEMPLATES_ROOT.DIRECTORY_SEPARATOR.'hello_world.php';
            $this->contents = file_get_contents($this->file);
            $this->options = array('test' => 'value');
            $this->template = new TemplateTest\TestTemplate($this->file, $this->options);
        }

        // #__construct

            function test_constructor() {
                ensure(is_a($this->template->binding, 'Philt\Binding'));
                ensure(is_a($this->template->source, 'Philt\Source'));
                assert_identical($this->options, $this->template->options);
            }

        // #binding

            function test_binding() {
                $this->template->binding->image = 'test.png';
                $binding = $this->template->binding(array('file' => 'test.txt'));
                assert_equal($this->template->binding, $binding['binding']);
                assert_equal('test.txt', $binding['file']);
                assert_equal('test.png', $binding['image']);
                assert_equal(array('file'), $binding['locals']);
            }

        // #render

            function test_render() {
                $template = $this->template;
                assert_throws('BadMethodCallException', function() use ($template) { $template->render(); });
            }

    }
}