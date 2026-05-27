<?php

namespace ClassLeak202605\Illuminate\Support;

if (!\function_exists('ClassLeak202605\\Illuminate\\Support\\enum_value')) {
    /**
     * Return a scalar value for the given value that might be an enum.
     *
     * @internal
     *
     * @template TValue
     * @template TDefault
     *
     * @param  TValue  $value
     * @param  TDefault|callable(TValue): TDefault  $default
     * @return ($value is empty ? TDefault : mixed)
     */
    function enum_value($value, $default = null)
    {
        switch (\true) {
            case $value instanceof \BackedEnum:
                return $value->value;
            case $value instanceof \UnitEnum:
                return $value->name;
            default:
                return $value ?? value($default);
        }
    }
}
