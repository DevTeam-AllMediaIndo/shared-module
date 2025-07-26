<?php
namespace Allmedia\Shared\Auth\Admin;

use Allmedia\Shared\Auth\Admin\AuthenticationInterface;
use Allmedia\Shared\Database\DatabaseInterface;
use Exception;

class AuthenticationCore extends AuthenticationConfig implements AuthenticationInterface {
    
    public function __construct(?DatabaseInterface $databaseInterface) {
        $this->db = $databaseInterface::connect();
    }

    public function setSessionData(string $token): bool {
        try {
            global $_SESSION, $_COOKIE;
            if(empty($token)) {
                return false;
            }
    
            $_SESSION[ $this->sessionAuthName ] = $_COOKIE[ $this->sessionAuthName ] = $token;
            return true;

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getSessionData(): array|bool {
        try {
            global $_SESSION, $_COOKIE;
            $token = $_SESSION[ $this->sessionAuthName ] ?? $_COOKIE[ $this->sessionAuthName ] ?? "";
            if(empty($token)) {
                return false;
            }
    
            return [
                'token' => $token
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function authentication(): array|bool {
        $authData = self::getSessionData();
        if(!$authData) {
            return false;
        }

        /** Validasi Token */
        $token = $authData['token'] ?? "";
        $sqlCheck = $this->db->query("SELECT * FROM tb_admin WHERE {$this->tokenColumn} = '{$token}' LIMIT 1");
        $user = $sqlCheck->fetch_assoc(); 
        if($sqlCheck->num_rows != 1) {
            return false;
        }

        /** Check Expired */
        if(strtotime($user[ $this->tokenExpiredColumn ]) < strtotime("now")) {
            return false;
        }

        return $user;
    }

    public function logout(): bool {
        global $_SESSION, $_COOKIE;
        $_SESSION[ self::$sessionAuthName ] = "";
        $_COOKIE[ self::$sessionAuthName ] = "";

        session_destroy();
        setcookie( $this->sessionAuthName,"", time());
        return true;
    }
}