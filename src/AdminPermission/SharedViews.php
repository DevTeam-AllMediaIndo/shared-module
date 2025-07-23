<?php
namespace App\Shared\AdminPermission;

use App\Shared\InterfaceSharedView;
use App\Shared\SystemInfo;
use Throwable;

class SharedViews implements InterfaceSharedView {

    public static function render(string $filepath, array $data = []) {
        ob_start();
        extract($data, EXTR_SKIP);

        try {
            include __DIR__ . "/views/$filepath.php";

        } catch (Throwable $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }
        }
    }

    public static function render_script(string $filepath, array $data = []) {
        try {
            extract($data, EXTR_SKIP);
            include __DIR__ . "/views/$filepath.php";

        } catch (Throwable $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }
        }
    }
}