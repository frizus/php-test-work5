<?php

use Carbon\Carbon;

function validate_int($value): bool
{
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

function validate_float($value): bool
{
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
}

function api_timezone(): ?string
{
    static $timezone;
    static $called = false;

    if (!$called) {
        $called = true;
        $timezone = config('app.api_timezone');
    }

    return $timezone;
}

function api_date_format(): ?string
{
    static $format;
    static $called = false;

    if (!$called) {
        $called = true;
        $format = config('app.api_date_format');
    }

    return $format;
}

function api_formatted_date(mixed $datetime): ?string
{
    if (!$datetime instanceof Carbon) {
        return null;
    }

    return $datetime->clone()
        ->setTimezone(api_timezone())
        ->format(api_date_format());
}
