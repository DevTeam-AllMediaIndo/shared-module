<?php
namespace Allmedia\Share\Auth\Admin;

interface AuthenticationInterface {

    /**
     * Summary of setSessionData
     * @param array $data
     * @return bool
     */
    public function setSessionData(string $token): bool;

    /**
     * Summary of getSessionData
     * return array on success and false on failed
     * @return array|bool 
     */
    public function getSessionData(): array|bool; 

    /**
     * Summary of authentication
     * return array on success and false on failed
     * @return array|bool
     */
    public function authentication(): array|bool;

}