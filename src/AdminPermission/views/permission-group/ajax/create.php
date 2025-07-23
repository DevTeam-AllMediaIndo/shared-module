<?php
use App\Models\Helper;
use Config\Core\Database;
use Allmedia\Shared\AdminPermission\Models\PermissionGroup;

if(!$isAllowed) {
    JsonResponse([
        'code'      => 200,
        'success'   => false,
        'message'   => "Authorization Failed",
        'data'      => []
    ]);
}

$data = Helper::getSafeInput($_POST);
foreach(['group_name', 'group_type', 'group_icon'] as $req) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "{$req} diperlukan",
            'data'      => []
        ]);
    }
}

/** Get Max ID */
$order = PermissionGroup::maxGroupId();
if($order == 0) {
    JsonResponse([
        'code'      => 200,
        'success'   => false,
        'message'   => "Gagal membuat nomor order",
        'data'      => []
    ]);
}

/** Insert */
$insert = Database::insert("admin_module_group", [
    'group' => $data['group_name'],
    'type' => $data['group_type'],
    'icon' => $data['group_icon'],
    'min_level' => 0,
    'order' => $order
]);

if(!$insert) {
    JsonResponse([
        'code'      => 200,
        'success'   => false,
        'message'   => "Gagal membuat modul grup baru",
        'data'      => []
    ]);
}

JsonResponse([
    'code'      => 200,
    'success'   => true,
    'message'   => "Berhasil membuat modul grup baru",
    'data'      => []
]);