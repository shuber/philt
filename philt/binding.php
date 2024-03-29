<?php

namespace Philt;

class Binding {
    const CALLABLE_METHOD = 'invoke';
    const MISSING_METHOD = 'method_missing';
    const EXTENDED_METHOD = 'extended';
    const NON_STATIC_METHOD_CALL_ERROR = 2048;

    static $initialized = false;
    static $original_error_handler;

    public $ancestors = array();
    public $locals;
    public $methods = array();

    function __construct($locals = array()) {
        if (!self::$initialized) self::initialize();
        $this->locals = $locals;
        $this->extend_method(__CLASS__, self::MISSING_METHOD);
    }

    function __call($method, $arguments) {
        return $this->send($method, $arguments);
    }

    function __get($local) {
        return $this->locals[$local];
    }

    function __invoke() {
        return $this->send(self::CALLABLE_METHOD, func_get_args());
    }

    function __isset($local) {
        return isset($this->locals[$local]);
    }

    function __set($local, $value) {
        $this->locals[$local] = $value;
    }

    function __unset($local) {
        unset($this->locals[$local]);
    }

    function extend($classes) {
        $classes = func_get_args();
        foreach ($classes as $class) {
            if (!in_array($class, $this->ancestors)) {
                $methods = get_class_methods($class);
                foreach ($methods as $method) $this->extend_method($class, $method);
                array_unshift($this->ancestors, $class);
                if (method_exists($class, self::EXTENDED_METHOD)) call_user_func(array($class, self::EXTENDED_METHOD), $this);
            }
        }
        return $this;
    }

    function method($method, $caller = null) {
        if (isset($this->methods[$method])) {
            $callees = $this->methods[$method];
            $index = in_array($caller, $callees) ? array_search($caller, $callees) + 1 : 0;
            if (isset($callees[$index])) return array($callees[$index], $method);
        }
    }

    function method_missing($method, $arguments = array()) {
        throw new \BadMethodCallException('Undefined method '.__CLASS__.'::'.$method.'() called with arguments '.print_r($arguments, true));
    }

    function send($method, $arguments = array()) {
        if (!$callee = $this->method($method)) {
            $callee = $this->method(self::MISSING_METHOD);
            $arguments = array($method, $arguments);
        }
        return $this->call($callee, $arguments);
    }

    function super() {
        $arguments = func_get_args();
        $backtrace = debug_backtrace();
        if (isset($backtrace[1]) && isset($backtrace[1]['object']) && is_a($backtrace[1]['object'], __CLASS__)) {
            $class = $backtrace[1]['class'];
            $method = $backtrace[1]['function'];
            if ($callee = $this->method($method, $class)) return $this->call($callee, $arguments);
        }
        return $this->send('super', $arguments);
    }

    protected function call($callee, $arguments) {
        $variables = array();
        foreach ($arguments as $key => $value) $variables[] = '$arguments['.$key.']';
        eval('$value = '.implode('::', $callee).'('.implode(',', $variables).');');
        return $value;
    }

    protected function extend_method($class, $method) {
        if (!isset($this->methods[$method])) $this->methods[$method] = array();
        array_unshift($this->methods[$method], $class);
    }

    static function error_handler($number, $message, $file, $line, $context) {
        if ($number != self::NON_STATIC_METHOD_CALL_ERROR || !preg_match('#^'.__FILE__.'.+eval#', $file)) {
            return self::$original_error_handler ? call_user_func_array(self::$original_error_handler, func_get_args()) : false;
        }
    }

    static function register_error_handler() {
        return self::$original_error_handler = set_error_handler(array(__CLASS__, 'error_handler'));
    }

    static protected function initialize() {
        self::register_error_handler();
        self::$initialized = true;
    }
}