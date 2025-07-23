<?php
namespace Allmedia\Shared\AdminPermission\Models;

use Allmedia\Shared\Contracts\DatabaseInterface;
use Exception;

class PermissionGroup {

    private $db;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db::connect();
    }

    public function getById(string $id): array|bool {
        try {
            $sqlGet = $this->db->query("SELECT * FROM admin_module_group WHERE MD5(MD5(id)) = '{$id}' LIMIT 1");
            return $sqlGet->fetch_assoc() ?? false;

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function maxGroupId() {
        try {
            $sqlGet = $this->db->query("SELECT MAX(id) as id FROM admin_module_group LIMIT 1");
            return $sqlGet->fetch_assoc()['id'] ?? 0;

        } catch (Exception $e) {
            throw $e;
        }
    }
}