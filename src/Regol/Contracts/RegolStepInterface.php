<?php
namespace Allmedia\Shared\Regol\Contracts;

interface RegolStepInterface {

    public function __construct(array $data);

    public function validate(): bool;

    public function view();

}