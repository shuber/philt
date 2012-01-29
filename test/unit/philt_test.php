<?php

class PhiltTest extends ztest\UnitTestCase {

    function setup() {
        $this->philt = new Philt;
    }

    // #clear

        function test_clear() {
            assert_not_empty($this->philt->mappings);
            $this->philt->clear();
            assert_empty($this->philt->mappings);
        }

        function test_clear_with_extension() {
            $this->philt->register('TestHandler', 'test');
            assert_not_empty($this->philt->mappings['test']);
            $this->philt->clear('test');
            ensure(!isset($this->philt->mappings['test']));
            assert_not_empty($this->philt->mappings);
        }

        function test_clear_with_multiple_extensions() {
            $this->philt->register('TestHandler', 'test', 'testing');
            assert_not_empty($this->philt->mappings['test']);
            assert_not_empty($this->philt->mappings['testing']);
            $this->philt->clear('test', 'testing');
            ensure(!isset($this->philt->mappings['test']));
            ensure(!isset($this->philt->mappings['testing']));
            assert_not_empty($this->philt->mappings);
        }

    // #register

        function test_register() {
            ensure(!isset($this->philt->mappings['test']));
            $this->philt->register('TestHandler', 'test');
            assert_equal(array('TestHandler'), $this->philt->mappings['test']);
        }

        function test_register_with_multiple_extensions() {
            $this->philt->register('TestHandler', 'test', 'testing');
            assert_equal(array('TestHandler'), $this->philt->mappings['test']);
            assert_equal(array('TestHandler'), $this->philt->mappings['testing']);
        }

    // #registered

        function test_registered() {
            ensure(!$this->philt->registered('test'));
            $this->philt->register('TestHandler', 'test');
            ensure($this->philt->registered('test'));
        }

}