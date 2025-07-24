<?php
namespace Allmedia\Shared\Regol\Steps;

class CreateSteps {

    public static function create(array $array): array {
        $steps = [];
        foreach($array as $ar) {
            $steps = self::validate($ar);
        }

        return $steps;
    }

    public static function validate(array $data) {
        /** Required Data */
        $data['handler'] ??= "Invalid Handler";
        $data['title'] ??= $data['handler'];
        $data['data'] ??= [];

        return $data;
    }
}