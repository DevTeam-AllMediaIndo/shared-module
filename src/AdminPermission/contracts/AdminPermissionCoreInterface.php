<?php
namespace Allmedia\Shared\AdminPermission\Contracts;

interface AdminPermissionCoreInterface {

    public function getAuthrorizedPermissions(int $adminid): array;

    public function getModule_and_Permissions(int $adminid): array;

    public function hasPermission(array $modulePermission, string $url = ""): array|bool;

    public function isHavePermission(int $module_id, string $permissionCode): array|bool;
    
    public function availableGroup(): array;
}