<?php
namespace Allmedia\Shared\AdminPermission;

use Allmedia\Shared\AdminPermission\Contracts\ShareViewInterface;
use Throwable;

class SharedViews implements ShareViewInterface {

    public static function render(string $filepath, array $data = []) {
        ob_start();
        extract($data, EXTR_SKIP);

        try {
            include __DIR__ . "/views/$filepath.php";

        } catch (Throwable $e) {
            throw $e;
        }
    }

    public static function render_script(string $filepath, array $data = []) {
        try {
            extract($data, EXTR_SKIP);
            include __DIR__ . "/views/$filepath.php";

        } catch (Throwable $e) {
            throw $e;
        }
    }
}