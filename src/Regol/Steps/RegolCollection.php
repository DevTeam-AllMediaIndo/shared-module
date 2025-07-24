<?php
namespace Allmedia\Shared\Regol\Steps;

use Allmedia\Shared\Regol\Contracts\RegolStepInterface;

class RegolCollection {

    private array $items = [];

    public function add(RegolStepInterface $regolStepInterface) {
        $this->items[] = $regolStepInterface;
        return $this;
    }

    public function all() {
        return $this->items;
    }

}