<?php
namespace Allmedia\Shared\Regol\Steps;

use Allmedia\Shared\Regol\Contracts\RegolStepInterface;
use Exception;

class RegolController {

    private RegolCollection $regolCollection;

    public function __construct(RegolCollection $regolCollection) {
        $this->regolCollection = $regolCollection;
    }

    public function render(string $alias) {
        try {
            $activeIndex = 0;
            $steps = $this->regolCollection->all();
            foreach($steps as $i => $s) {
                if($s->pageView == $alias) {
                    $activeIndex = $i;
                }
            }

            $step = $steps[ $activeIndex ];
            $viewData = [
                'steps' => $steps,
                'alias' => $alias,
                'content' => $step->view()
            ];

            extract($viewData, EXTR_SKIP);
            require_once __DIR__ . "/../views/index.php";

        } catch (Exception $e) {
            throw $e;
        }
    }
}