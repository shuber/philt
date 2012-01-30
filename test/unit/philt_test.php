<?php

class PhiltTest extends ztest\UnitTestCase {

    function setup() {
        $this->philt = new Philt;
    }

    // #clear

        function test_clear() {
            assert_not_empty($this->philt->handlers);
            $this->philt->clear();
            assert_empty($this->philt->handlers);
        }

        function test_clear_with_extension() {
            $this->philt->register('TestHandler', 'test');
            assert_not_empty($this->philt->handlers['test']);
            $this->philt->clear('test');
            ensure(!isset($this->philt->handlers['test']));
            assert_not_empty($this->philt->handlers);
        }

        function test_clear_with_multiple_extensions() {
            $this->philt->register('TestHandler', 'test', 'testing');
            assert_not_empty($this->philt->handlers['test']);
            assert_not_empty($this->philt->handlers['testing']);
            $this->philt->clear('test', 'testing');
            ensure(!isset($this->philt->handlers['test']));
            ensure(!isset($this->philt->handlers['testing']));
            assert_not_empty($this->philt->handlers);
        }

    // #register

        function test_register() {
            ensure(!isset($this->philt->handlers['test']));
            $this->philt->register('TestHandler', 'test');
            assert_equal(array('TestHandler'), $this->philt->handlers['test']);
        }

        function test_register_with_multiple_extensions() {
            $this->philt->register('TestHandler', 'test', 'testing');
            assert_equal(array('TestHandler'), $this->philt->handlers['test']);
            assert_equal(array('TestHandler'), $this->philt->handlers['testing']);
        }

    // #registered

        function test_registered() {
            ensure(!$this->philt->registered('test'));
            $this->philt->register('TestHandler', 'test');
            ensure($this->philt->registered('test'));
        }

}