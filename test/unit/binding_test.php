<?php

namespace BindingTest {
    class AssetHelpers {
        static $extended_binding;

        static function extended($binding) {
            self::$extended_binding = $binding;
        }

        function asset_path($path) {
            return '/assets/'.$path;
        }
    }

    class S3AssetHelpers {
        function asset_path($path) {
            return 'http://example.s3.amazonaws.com'.$this->super($path);
        }

        function bad_super() {
            return $this->super();
        }

        function invoke() {
            return true;
        }
    }

    class ErrorHandler {
        static function error_handler($number, $message, $file, $line, $context) {
            return 'test';
        }
    }
}

namespace {
    class BindingTest extends ztest\UnitTestCase {

        function setup() {
            $this->binding = new Philt\Binding;
            $this->assets = __CLASS__.'\\AssetHelpers';
            $this->s3 = __CLASS__.'\\S3AssetHelpers';
            $class = $this->assets;
            $class::$extended_binding = null;
            $this->get_error_handler = (function() {
                $handler = set_error_handler(function () {});
                restore_error_handler();
                return $handler;
            });
        }

        // #__call

            function test_magic_call() {
                $binding = $this->binding->extend($this->assets);
                assert_equal('/assets/test.png', $this->binding->asset_path('test.png'));
                assert_throws('BadMethodCallException', function() use ($binding) { $binding->missing(); });
            }

        // #__construct

            function test_constructor_shoud_accept_locals() {
                $locals = array('test' => 'value');
                $binding = new Philt\Binding(array('test' => 'value'));
                assert_equal($locals, $binding->locals);
            }

        // #__get/__set

            function test_get_and_set() {
                ensure(!isset($this->binding->locals['test']));
                $this->binding->test = 'testing';
                assert_all_equal('testing', $this->binding->locals['test'], $this->binding->test);
            }

        // #__invoke/invoke

            function test_invoke() {
                $binding = $this->binding;
                assert_throws('BadMethodCallException', function() use ($binding) { $binding(); });
                $binding->extend($this->s3);
                ensure($binding());
            }

        // #__isset/__unset

            function test_isset_and_unset() {
                ensure(!isset($this->binding->test));
                $this->binding->test = 'testing';
                ensure(isset($this->binding->test));
                unset($this->binding->test);
                ensure(!isset($this->binding->test));
            }

        // #extend

            function test_extend() {
                assert_empty($this->binding->ancestors);
                ensure(!isset($this->binding->methods['asset_path']));
                assert_equal($this->binding, $this->binding->extend($this->assets));
                assert_equal(array($this->assets), $this->binding->ancestors);
                assert_equal(array($this->assets), $this->binding->methods['asset_path']);
            }

            function test_extend_with_multiple_classes() {
                $this->binding->extend($this->assets, $this->s3);
                assert_equal(array($this->s3, $this->assets), $this->binding->ancestors);
            }

            function test_extend_with_duplicate_classes() {
                $this->binding->extend($this->assets, $this->s3, $this->assets);
                assert_equal(array($this->s3, $this->assets), $this->binding->ancestors);
            }

            function test_extend_should_call_extended_callback() {
                $class = $this->assets;
                assert_null($class::$extended_binding);
                $this->binding->extend($class);
                assert_equal($this->binding, $class::$extended_binding);
            }

        // #method

            function test_method() {
                $this->binding->extend($this->assets, $this->s3);
                assert_null($this->binding->method('missing'));
                assert_equal(array($this->s3, 'bad_super'), $this->binding->method('bad_super'));
                assert_equal(array($this->s3, 'asset_path'), $this->binding->method('asset_path'));
                assert_equal(array($this->assets, 'asset_path'), $this->binding->method('asset_path', $this->s3));
            }

        // #method_missing

            function test_method_missing() {
                $binding = $this->binding;
                assert_throws('BadMethodCallException', function() use ($binding) { $binding->method_missing('missing'); });
            }

        // #send

            function test_send() {
                $binding = $this->binding->extend($this->assets);
                assert_equal('/assets/test.png', $this->binding->send('asset_path', array('test.png')));
                assert_throws('BadMethodCallException', function() use ($binding) { $binding->send('missing'); });
            }

        // #super

            function test_super() {
                $binding = $this->binding;
                assert_throws('BadMethodCallException', function() use ($binding) { $binding->super(); });
                $binding->extend($this->assets, $this->s3);
                assert_equal('http://example.s3.amazonaws.com/assets/test.png', $binding->asset_path('test.png'));
                assert_throws('BadMethodCallException', function() use ($binding) { $binding->bad_super(); });
            }

        // ::error_handler

            function test_error_handler() {
                $file = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'philt'.DIRECTORY_SEPARATOR.'binding.php';
                ensure(!Philt\Binding::error_handler(1, 'error message', __FILE__, 1, array()));
                ensure(!Philt\Binding::error_handler(1, 'error message', $file, 1, array()));
                ensure(!Philt\Binding::error_handler(Philt\Binding::NON_STATIC_METHOD_CALL_ERROR, 'error message', __FILE__, 1, array()));
                ensure(!Philt\Binding::error_handler(Philt\Binding::NON_STATIC_METHOD_CALL_ERROR, 'error message', $file, 1, array()));
                assert_null(Philt\Binding::error_handler(Philt\Binding::NON_STATIC_METHOD_CALL_ERROR, 'error message', $file.'-eval', 1, array()));
            }

            function test_error_handler_with_original_error_handler() {
                $original = Philt\Binding::$original_error_handler;
                Philt\Binding::$original_error_handler = array(__CLASS__.'\\ErrorHandler', 'error_handler');
                assert_equal('test', Philt\Binding::error_handler(1, 'error message', $file, 1, array()));
                Philt\Binding::$original_error_handler = $original;
            }

        // ::initialize

            function test_initialize() {
                $get_error_handler = $this->get_error_handler;
                restore_error_handler();
                Philt\Binding::$initialized = false;
                $binding = new Philt\Binding;
                ensure(Philt\Binding::$initialized);
                assert_equal(array('Philt\Binding', 'error_handler'), $get_error_handler());
            }

        // ::register_error_handler

            function test_register_error_handler() {
                restore_error_handler();
                $get_error_handler = $this->get_error_handler;
                $handler = $get_error_handler();
                assert_equal($handler, Philt\Binding::register_error_handler());
                assert_equal(array('Philt\Binding', 'error_handler'), $get_error_handler());
                assert_not_equal($handler, $get_error_handler());
            }

    }
}