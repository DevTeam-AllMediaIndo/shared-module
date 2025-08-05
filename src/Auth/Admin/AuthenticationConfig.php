<?php
namespace Allmedia\Shared\Auth\Admin;

class AuthenticationConfig {

    protected $db;
    protected $table = "tb_admin";
    protected $tokenColumn = "ADM_TOKEN";
    protected $tokenExpiredColumn = "ADM_TOKEN_EXPIRED";
    public string $sessionAuthName = "app_crm_token";

    /**
     * Summary of setTable
     * Set table name
     * @param string $tableName
     * @return void
     */
    public function setTable(string $tableName) {
        $this->table = $tableName;
    }

    /**
     * Summary of setTokenColumn
     * Set Column Name of token column
     * @param string $columnName
     * @return void
     */
    public function setTokenColumn(string $columnName) {
        $this->tokenColumn = $columnName;
    }

    /**
     * Summary of setTokenExpiredColumn
     * Set Column name of token expired
     * @param string $tokenExpiredColumn
     * @return void
     */
    public function setTokenExpiredColumn(string $tokenExpiredColumn) {
        $this->tokenExpiredColumn = $tokenExpiredColumn;
    }

    /**
     * Summary of setSessionAuthName
     * Set Column name of token expired
     * @param string $sessionAuthName
     * @return void
     */
    public function setSessionAuthName(string $sessionAuthName) {
        $this->sessionAuthName = $sessionAuthName;
    }
}