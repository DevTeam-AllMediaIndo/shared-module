<?php
namespace Allmedia\Shared\Regol\Steps;

use Allmedia\Shared\Regol\Contracts\RegolStepInterface;
use Exception;

class StepProfilePerusahaan implements RegolStepInterface {

    public $pageTitle = "Profile Perusahaan";
    public $pageView = "profile-perusahaan";
    public $data = [];
    
    public function __construct(array $data)
    {
        $this->data = [
            'company' => [
                'name' => $data['company']['name'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'fax' => $data['fax'],
                'email' => $data['email'],
                'homepage' => $data['homepage']
            ]
        ];
    }
    
    public function validate(): bool {
        return empty($this->data['demo']);
    }

    public function view() {
        try {
            ob_start();
            extract($this->data, EXTR_SKIP);
            require_once __DIR__ . "/../views/{$pageView}.php";
            return ob_get_clean();

        } catch (Exception $e) {
            throw $e;
        }
    }

}