<?php
namespace Allmedia\Shared\AdminPermission\Core;

class UrlParser {

    public static function urlToPath(array $data = [], string $default = "index") {
        if(empty($data)) {
            return $default;
        }

        $result = [];
        foreach($data as $key => $val) {
            if(!empty($val)) {
                $result[] = filter_var(strtolower($val), FILTER_SANITIZE_URL);
            }
        }

        if(count($result) < 2) {
            $result[] = $default;
        }

        return implode("/", $result);
    }
}