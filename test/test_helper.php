<?php

define('PHILT_TEMPLATES_ROOT', __DIR__.DIRECTORY_SEPARATOR.'templates');

function assert_all_equal($value, $other) {
    $arguments = func_get_args();
    $value = array_shift($arguments);
    foreach ($arguments as $argument) {
        assert_equal($value, $argument);
    }
}

function assert_difference($expression, $lambda) {
    $expression = 'return '.$expression.';';
    $value = eval($expression);
    $lambda();
    assert_not_equal($value, eval($expression));
}

function assert_no_difference($expression, $lambda) {
    $expression = 'return '.$expression.';';
    $value = eval($expression);
    $lambda();
    assert_equal($value, eval($expression));
}

function assert_empty($object, $message = 'was not expected to be empty') {
    ensure(empty($object), $message);
}

function assert_not_empty($object, $message = 'was expected to be empty') {
    ensure(!empty($object), $message);
}

function assert_not_identical($value, $other, $message = null) {
    if (is_null($message)) $message = 'was expected not to be identical to '.print_r($other, true);
    ensure($value !== $other, $message);
}

function assert_in_array($needle, $haystack) {
    ensure(in_array($needle, $haystack));
}

function assert_not_in_array($needle, $haystack) {
    ensure(!in_array($needle, $haystack));
}

function assert_matches($pattern, $subject) {
    ensure(preg_match($pattern, $subject));
}

function assert_not_matches($pattern, $subject) {
    ensure(!preg_match($pattern, $subject));
}