<?php
namespace Allmedia\Shared\AdminPermission\Models;

use Allmedia\Shared\Contracts\DatabaseInterface;
use Exception;

class PermissionModule {

    private $db;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db::connect();
    }
    
    public function findModuleById(string $id) {
        try {
            $sqlGet = $this->db->query("
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
            throw $e;
        }
    }

    public function findPermissionByModuleId(int $id) {
        try {
            $sqlGet = $this->db->query("SELECT * FROM admin_permissions WHERE module_id = {$id}");
            return $sqlGet->fetch_all(MYSQLI_ASSOC) ?? [];

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function findPermissionById(int $id) {
        try {
            $sqlGet = $this->db->query("SELECT * FROM admin_permissions WHERE id = {$id} LIMIT 1");
            if($sqlGet->num_rows != 1) {
                return false;
            }

            return $sqlGet->fetch_assoc() ?? false;

        } catch (Exception $e) {
            throw $e;
        }
    }
}