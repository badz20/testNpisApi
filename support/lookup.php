<?php

use App\Models\Decision;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

if (! function_exists('lookup')) {
    function lookup(string $key)
    {
        return Cache::remember('lookup-'.Str::kebab($key), config('cache_duration.lookup', 60), function () use ($key) {
            return \App\Models\Lookup::where('key', $key)->orderBy('order_by')->get();
        });
    }
}

if (! function_exists('lookupJson')) {
    function lookupJson(string $key, string $jsonKey, $value)
    {
        return \App\Models\Lookup::where('key', $key)->orderBy('order_by')->whereJsonContains('json_value->'.$jsonKey, $value);
    }
}

if (! function_exists('lookupOption')) {
    function lookupOption(string $key)
    {
        return Cache::remember('lookup-option-'.Str::kebab($key), config('cache_duration.lookup', 60), function () use ($key) {
            return \App\Models\LookupOption::where('key', $key)->orderBy('order_by')->get();
        });
    }
}

if (! function_exists('lookupOptionJson')) {
    function lookupOptionJson(string $key, string $jsonKey, $value)
    {
        return \App\Models\LookupOption::where('key', $key)->orderBy('order_by')->whereJsonContains('json_value->'.$jsonKey, $value);
    }
}

if (! function_exists('lookupOptionSingle')) {
    function lookupOptionSingle(string $key, string $value)
    {
        // return Cache::remember('lookup-option-'.Str::kebab($key). '-'.Str::kebab($value), config('cache_duration.lookup', 60), function () use ($key,$value) {
            return \App\Models\LookupOption::where('key', $key)->where('code',$value)->first();
        // });
    }
}