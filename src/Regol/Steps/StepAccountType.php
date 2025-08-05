<?php
namespace Allmedia\Shared\Regol\Steps;

use Allmedia\Shared\Regol\Contracts\RegolStepInterface;
use Exception;

class StepAccountType implements RegolStepInterface {

    public $pageTitle = "Rate & Jenis Real Account";
    public $pageView = "rate-jenis-account";
    public $data = [];

    public function __construct(array $data) {
        $this->data = [
            'demo_login' => $data['demo_login'] ?? 0,
            'real_category' => $data['real_category'] ?? "-",
            'real_rtype' => $data['real_rtype'],
            'categories' => $data['categories'] ?? [],
        ];

        $this->data['pagePrev'] = $data['pagePrev'] ?? "javascript:void(0)";
    }

    public function validate(): bool {
        return empty($this->data['demo']);
    }

    public function view() {
        try {
            ob_start();
            extract($this->data, EXTR_SKIP);
            require_once __DIR__ . "/../views/{$this->pageView}.php";
            return ob_get_clean();

        } catch (Exception $e) {
            throw $e;
        }
    }
}