<?php
namespace App\Shared\AdminPermission\Models;

use App\Models\DBHelper;
use App\Shared\SystemInfo;
use Exception;

class PermissionGroup {

    public static function getById(string $id): array|bool {
        try {
            $db = DBHelper::getConnection();
            $sqlGet = $db->query("SELECT * FROM admin_module_group WHERE MD5(MD5(id)) = '{$id}' LIMIT 1");
            return $sqlGet->fetch_assoc() ?? false;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

    public static function maxGroupId() {
        try {
            global $db;
            if(empty($db)) {
                $db = DBHelper::getConnection();
            }
            
            $sqlGet = $db->query("SELECT MAX(id) as id FROM admin_module_group LIMIT 1");
            
            return $sqlGet->fetch_assoc()['id'] ?? 0;

        } catch (Exception $e) {
            return 0;
        }
    }
}