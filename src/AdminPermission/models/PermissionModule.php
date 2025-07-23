<?php
namespace App\Shared\AdminPermission\Models;

use App\Models\DBHelper;
use App\Shared\SystemInfo;
use Exception;

class PermissionModule {

    public static function findModuleById(string $id) {
        try {
            $db = DBHelper::getConnection();
            $sqlGet = $db->query("
                SELECT 
                	am.*,
                    amg.`group`
                FROM admin_module am
                JOIN admin_module_group amg ON (amg.id = am.group_id)
                WHERE MD5(MD5(am.id)) = '{$id}' 
                LIMIT 1
            ");

            if($sqlGet->num_rows != 1) {
                return false;
            }

            return $sqlGet->fetch_assoc();

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function findPermissionByModuleId(int $id) {
        try {
            $db = DBHelper::getConnection();
            $sqlGet = $db->query("SELECT * FROM admin_permissions WHERE module_id = {$id}");
            return $sqlGet->fetch_all(MYSQLI_ASSOC) ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function findPermissionById(int $id) {
        try {
            $db = DBHelper::getConnection();
            $sqlGet = $db->query("SELECT * FROM admin_permissions WHERE id = {$id} LIMIT 1");
            if($sqlGet->num_rows != 1) {
                return false;
            }

            return $sqlGet->fetch_assoc() ?? false;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }
}