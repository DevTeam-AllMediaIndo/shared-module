<?php
namespace Allmedia\Shared\Regol\Contracts;

interface RegolStepInterface {

    public function __construct();

    public function validate(): bool;

    public function view();

}