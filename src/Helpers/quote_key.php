<?php

if(!function_exists('quote_key')) {
    function quote_key(string $key, ?string $startQuote = '{', ?string $endQuote = '}'): string
    {
        if($startQuote && !str_starts_with($key, $startQuote)) {
            $key = $startQuote.$key;
        }

        if($endQuote && !str_ends_with($key, $endQuote)) {
            $key .= $endQuote;
        }

        return $key;
    }
}
