<?php

class PhiltTest extends ztest\UnitTestCase {

    function teardown() {
        Philt::clear();
        Philt::register_default_extensions();
    }

    // Philt::clear()

        function test_clear() {
            // 
        }

    // Philt::register()

        function test_register() {
            ensure(!isset(Philt::$mappings['test']));
            Philt::register('TestHandler', 'test');
            assert_equal(array('TestHandler'), Philt::$mappings['test']);
        }

        function test_register_with_array_of_extensions() {
            Philt::register('TestHandler', array('test', 'testing'));
            assert_equal(array('TestHandler'), Philt::$mappings['test']);
            assert_equal(array('TestHandler'), Philt::$mappings['testing']);
        }

        function test_register_with_multiple_extensions() {
            Philt::register('TestHandler', 'test', 'testing');
            assert_equal(array('TestHandler'), Philt::$mappings['test']);
            assert_equal(array('TestHandler'), Philt::$mappings['testing']);
        }

    // Philt::registered()

        function test_registered() {
            ensure(!Philt::registered('test'));
            Philt::register('TestHandler', 'test');
            ensure(Philt::registered('test'));
        }

}