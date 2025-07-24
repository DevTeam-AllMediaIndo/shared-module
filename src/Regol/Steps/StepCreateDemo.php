<?php
namespace Allmedia\Shared\Regol\Steps;

use Allmedia\Shared\Regol\Contracts\RegolStepInterface;

class StepCreateDemo implements RegolStepInterface {

    public function handle(array $request): void {
    }

    public function validate(array $request): array|string {
        return [];
    }

    public function view(): void {

    }
}