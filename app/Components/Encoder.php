<?php
namespace App\Components;

use Illuminate\Support\Collection;

class Encoder {    
    public static function encode($decoded_data) {
        return gzencode(json_encode($decoded_data), 5);
    }
    
    public static function decode($encoded_data) {
        return json_decode(gzdecode($encoded_data), true);
    }
    
    public static function jsonDecodeProperties(Collection $records, array $properties_to_decode): void {
        if(!empty($records)) {
            foreach($records as $record) {
                foreach($properties_to_decode as $property_name) {
                    $record->$property_name = json_decode($record->$property_name, true);
                }
            }
        }
    }
}
