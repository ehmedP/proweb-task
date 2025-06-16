<?php

if (! function_exists('throw_if_null')) {
    function throw_if_null($value, $exception, ...$parameters) {
        throw_if(is_null($value), $exception, ...$parameters);

        return $value;
    }
}

if (! function_exists('throw_if_not_null')) {
    function throw_if_not_null($value, $exception, ...$parameters)
    {
        throw_if(!is_null($value), $exception, ...$parameters);
    }
}
