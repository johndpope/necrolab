<?php
namespace App\Components;

class Encoder {    
    public static function encode($decoded_data) {
        return gzencode(json_encode($decoded_data), 5);
    }
    
    public static function decode($encoded_data) {
        return json_decode(gzdecode($encoded_data));
    }
}