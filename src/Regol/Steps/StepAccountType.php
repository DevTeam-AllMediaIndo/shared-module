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
            'demo' => [],
            'categories' => [],
            'products' => [],
            'account' => [
                'category' => $data['account']['category'] ?? "",
            ],
        ];
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