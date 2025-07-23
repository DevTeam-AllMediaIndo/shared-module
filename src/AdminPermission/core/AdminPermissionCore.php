<?php
namespace App\Shared\AdminPermission\Core;

use App\Shared\AdminPermission\Contracts\AdminPermissionCoreInterface;
use App\Shared\Contracts\DatabaseInterface;
use App\Shared\Helper\UrlParser;
use Exception;

class AdminPermissionCore implements AdminPermissionCoreInterface {

    private $db;
    public string $sessionPermissionName = "authorization";
    public array $adminPermission = ['view', 'create', 'update', 'delete'];

    public function __construct(DatabaseInterface $db) {
        $this->db = $db::connect();
    }

    public function getAuthrorizedPermissions(int $adminid): array {
        try {
            global $_SESSION;
            $sqlGetModule = $this->db->query("
                SELECT 	
                    module.*,
                    ap.id as permission_id,
                    ap.`code`,
	                ap.`desc`,
                    ap.url,
                    aa.`status`,
                    aa.created_at,
                    aa.updated_at
                FROM admin_permissions ap
                JOIN admin_authorize aa ON (aa.permission_id = ap.id)
                JOIN (
                    SELECT 
                        amg.id as group_id,
                        amg.`group`,
                        amg.icon,
                        amg.min_level,
                        amg.type,
                        am.id as module_id,
                        am.module,
                        am.`status` as module_status,
                        am.visible
                    FROM admin_module am
                    JOIN admin_module_group amg ON (amg.id = am.group_id)
                ) as module ON (module.module_id = ap.module_id)
                WHERE aa.admin_id = {$adminid}
                ORDER BY module.group_id ASC, ap.module_id ASC
            ");

            $result = [];
            foreach($sqlGetModule->fetch_all(MYSQLI_ASSOC) as $module) {
                /** Module Index */
                $groupObject = [
                    'group_id' => $module['group_id'],
                    'group' => strtolower($module['group']),
                    'type'  => $module['type'],
                    'icon'  => $module['icon'],
                    'modules' => []
                ];

                $moduleObject = [
                    'id' => $module['module_id'],
                    'module' => strtolower($module['module']),
                    'status' => $module['status'],
                    'visible' => $module['visible'],
                    'alias' => ucwords(str_replace("-", " ", $module['module'])),
                    'permission' => []
                ];

                $permissionObject = [
                    'permission_id' => $module['permission_id'],
                    'code' => $module['code'],
                    'status' => $module['status'],
                    'desc' => $module['desc'],
                    'pattern' => $module['url'],
                    'link' => str_replace("/*", "", $module['url']),
                ];

                /** Search group index */
                $groupIndex = array_search($module['group_id'], array_column($result, "group_id"));
                if($groupIndex === FALSE) {
                    array_push($result, $groupObject);
                    $groupIndex = array_key_last($result);
                }


                $moduleIndex = array_search($module['module_id'], array_column($result[$groupIndex]['modules'], "id"));
                if($moduleIndex === FALSE) {
                    array_push($result[$groupIndex]['modules'], $moduleObject);
                    $moduleIndex = array_key_last($result[$groupIndex]['modules']);
                }
                
                $permissionIndex = array_search($module['permission_id'], array_column($result[$groupIndex]['modules'][$moduleIndex]['permission'], "permission_id"));
                if($permissionIndex === FALSE) {
                    array_push($result[$groupIndex]['modules'][$moduleIndex]['permission'], $permissionObject);
                    $permissionIndex = array_key_last($result[$groupIndex]['modules'][$moduleIndex]['permission']);
                }
            }
            
            /** set permission to session */
            $_SESSION[ self::$sessionPermissionName ] = $result;
            return $result;
            
        } catch (Exception $e) {
            return [];
        }
    }

    public function getModule_and_Permissions(int $adminid): array {
        try {
            $sqlGetModule = $this->db->query("
                SELECT 	
                    module.*,
                    ap.id as permission_id,
                    ap.`code`,
	                ap.`desc`,
                    ap.url,
                    aa.`status`,
                    aa.created_at,
                    aa.updated_at
                FROM admin_permissions ap
                LEFT JOIN admin_authorize aa ON (aa.permission_id = ap.id AND aa.admin_id = {$adminid})
                JOIN (
                    SELECT 
                        amg.id as group_id,
                        amg.`group`,
                        amg.icon,
                        amg.min_level,
                        amg.type,
                        am.id as module_id,
                        am.module,
                        am.`status` as module_status,
                        am.visible
                    FROM admin_module am
                    JOIN admin_module_group amg ON (amg.id = am.group_id)
                ) as module ON (module.module_id = ap.module_id)
                ORDER BY module.group_id ASC, ap.module_id ASC
            ");

            $result = [];
            foreach($sqlGetModule->fetch_all(MYSQLI_ASSOC) as $module) {
                /** Module Index */
                $groupObject = [
                    'group_id' => $module['group_id'],
                    'group' => strtolower($module['group']),
                    'type'  => $module['type'],
                    'icon'  => $module['icon'],
                    'modules' => []
                ];

                $moduleObject = [
                    'id' => $module['module_id'],
                    'module' => strtolower($module['module']),
                    'status' => $module['status'],
                    'visible' => $module['visible'],
                    'alias' => ucwords(str_replace("-", " ", $module['module'])),
                    'permission' => []
                ];

                $permissionObject = [
                    'permission_id' => $module['permission_id'],
                    'code' => $module['code'],
                    'status' => $module['status'],
                    'desc' => $module['desc'],
                    'pattern' => $module['url'],
                    'link' => str_replace("/*", "", $module['url']),
                ];

                /** Search group index */
                $groupIndex = array_search($module['group_id'], array_column($result, "group_id"));
                if($groupIndex === FALSE) {
                    array_push($result, $groupObject);
                    $groupIndex = array_key_last($result);
                }


                $moduleIndex = array_search($module['module_id'], array_column($result[$groupIndex]['modules'], "id"));
                if($moduleIndex === FALSE) {
                    array_push($result[$groupIndex]['modules'], $moduleObject);
                    $moduleIndex = array_key_last($result[$groupIndex]['modules']);
                }
                
                $permissionIndex = array_search($module['permission_id'], array_column($result[$groupIndex]['modules'][$moduleIndex]['permission'], "permission_id"));
                if($permissionIndex === FALSE) {
                    array_push($result[$groupIndex]['modules'][$moduleIndex]['permission'], $permissionObject);
                    $permissionIndex = array_key_last($result[$groupIndex]['modules'][$moduleIndex]['permission']);
                }
            }
            
            return $result;
            
        } catch (Exception $e) {
            if(ini_get("display_errors") == "1") {
                throw $e;
            }

            return [];
        }
    }

    public function hasPermission(array $modulePermission, string $url = ""): array|bool {
        try {
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if(!empty($url)) {
                $requestUri = $url;
            }

            if(empty($requestUri)) {
                return false;
            }

            $permission = [];
            foreach($modulePermission as $group) {
                if(!empty($permission)) {
                    break;
                }

                foreach($group['modules'] as $module) {
                    foreach($module['permission'] as $perm) {
                        if(!empty($permission)) {
                            break;
                        }

                        $pattern1 = preg_quote($perm['pattern'], "#");
                        $pattern2 = str_replace("\*", ".*", $pattern1);
                        $regex = "#^" . $pattern2 . "$#";

                        if(preg_match($regex, $requestUri)) {
                            $perm['module_id'] = $module['id'];
                            $perm['fileurl'] = str_replace("/^[a-zA-Z0-9\-]+$/", "", $pattern1);
                            $permission = $perm;
                            break;
                        }
                    }
                }
            }

            if(empty($permission)) {
                return false;
            }

            if($permission['status'] == 0) {
                return false;
            }

            $patternReplace = str_replace(["/.*", "\\"], ["", ""], $pattern2);
            $filepath = "/doc/";
            $filepath .= UrlParser::urlToPath(explode("/", $patternReplace), "view");
            $filepath .= ".php";

            return array_merge($permission, ['filepath' => $filepath]);

        } catch (Exception $e) {
            if(ini_get("display_errors") == "1") {
                throw $e;
            }

            return false;
        }
    }

    public function isHavePermission(int $module_id, string $permissionCode): array|bool {
        try {
            global $_SESSION;
            $sessionAuthorization = $_SESSION[ self::$sessionPermissionName ];
            if(empty($sessionAuthorization)) {
                return false;
            }
            
            $result = false;
            foreach($sessionAuthorization as $group) {
                $moduleIndex = array_search($module_id, array_column($group['modules'], "id"));
                if($moduleIndex === FALSE) {
                    continue;
                }

                $permissionIndex = array_search($permissionCode, array_column($group['modules'][$moduleIndex]['permission'], "code"));
                if($permissionIndex !== FALSE) {
                    $result = $group['modules'][$moduleIndex]['permission'][$permissionIndex]['status'];
                    break;
                }
            }

            return $result;

        } catch (Exception $e) {
            return 0;
        }
    }

    public function availableGroup(): array {
        try {
            $sqlGet = $this->db->query("
                SELECT 
                    * 
                FROM admin_module_group amg
                WHERE (amg.`type` = 'single' AND NOT EXISTS (SELECT am.group_id FROM admin_module am WHERE am.group_id = amg.id))
                OR (amg.`type` = 'dropdown')
            ");
            
            return $sqlGet->fetch_all(MYSQLI_ASSOC) ?? [];

        } catch (Exception $e) {
            return [];
        }
    }
}