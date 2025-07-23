<?php
namespace Allmedia\Shared\AdminPermission\Contracts;

interface ShareViewInterface {
    
    public static function render(string $filepath, array $data = []);
    public static function render_script(string $filepath, array $data = []);

}