<?php

if(!function_exists('quote_array_keys')) {
    function quote_array_keys(array $array, ?string $startQuote = '{', ?string $endQuote = '}'): array
    {
        $quoted = [];
        foreach($array as $key => $value) {
            $quoted[quote_key($key, $startQuote, $endQuote)] = $value;
        }
        return $quoted;
    }
}
