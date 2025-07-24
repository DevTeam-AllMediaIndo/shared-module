<?php
namespace Allmedia\Shared\Regol\Contracts;

interface RegolStepInterface {

    public function handle(array $request): void;

    public function validate(array $request): array|string;

    public function view(): void;

}