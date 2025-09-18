<?php
namespace Allmedia\Shared\Metatrader;

define("CHANGE_MASTER_PASSWORD", 0);
define("CHANGE_INVESTOR_PASSWORD", 1);

class ApiManager {

    protected string $endpoint;
    protected string $tokenManager;

    public function __construct(string $tokenManager, string $endpoint = "http://45.76.163.26:5000") {
        $this->tokenManager = $tokenManager;
        $this->endpoint = $endpoint;
    }

    public function request(string $command, array $data = []) {
        $curl   = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "{$this->endpoint}/{$command}?".http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15000,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        $resp = json_decode($response);
        $status = $resp->status ?? $resp->result ?? false;

        $error = str_replace("45.76.163.26", "**.**.***.**", $error);
        if(!empty($error)) {
            return (object) [
                'success'   => false,
                'error'     => $error ?? "Error Response",
                'message'   => ""
            ];
        }

        if(!is_object($resp) || $status != "success") {
            return (object) [
                'success'   => false,
                'error'     => $resp->message ?? "Invalid Object",
                'message'   => ""
            ];
        }

        if(is_object($resp->message) && property_exists($resp->message, "status")) {
            if ($resp->message->status != "success") {
                return (object) [
                    'success'   => false,
                    'error'     => $resp->message->message,
                    'message'   => ""
                ];
            }
        }

        return (object) [
            'success'   => true,
            'error'     => "",
            'message'   => $resp->message
        ];
    }

    public function createAccount(array $data): object|int {
        $required = ["master_pass", "investor_pass", "group", "fullname", "email", "leverage", "comment"];
        foreach($required as $req) {
            if(empty($data[ $req ])) {
                return -1;
            }
        }

        $data['id'] = $this->tokenManager;
        $data['master_pass'] = rtrim(base64_encode($data['master_pass']), "=");
        $data['investor_pass'] = rtrim(base64_encode($data['investor_pass']), "=");
        $data['fullname'] = rtrim(base64_encode($data['fullname']), "=");
        $data['email'] = rtrim(base64_encode($data['email']), "=");
        $request = $this->request("AccountCreate", $data);
        if(!$request->success) {
            return 0;
        }     

        return $request->message;
    }

    public function deposit(array $data): object|int {
        $required = ["login", "amount", "comment"];
        foreach($required as $req) {
            if(empty($data[ $req ])) {
                return -1;
            }
        }

        if(is_numeric($data['login']) === FALSE && $data['login'] <= 0) {
            return -1;
        }

        if(is_numeric($data['amount']) === FALSE && $data['amount'] <= 0) {
            return -1;
        }

        $data['id'] = $this->tokenManager;
        $request = $this->request("Deposit", $data);
        if(!$request->success) {
            return 0;
        }     

        return $request->message;
    }

    public function changePassword(array $data): object|int {
        $required = ["login", "password"];
        foreach($required as $req) {
            if(empty($data[ $req ])) {
                return -1;
            }
        }

        $data['password_type'] = !empty($data['password_type'])? $data['password_type'] : CHANGE_MASTER_PASSWORD;
        if(is_numeric($data['password_type']) === FALSE) {
            return -1;
        }

        if(is_numeric($data['login']) === FALSE && $data['login'] <= 0) {
            return -1;
        }

        $data['id'] = $this->tokenManager;
        $request = $this->request("ChangePassword", $data);
        if(!$request->success) {
            return 0;
        }

        return (object) [
            'success' => true,
            'message' => "Success",
            'data' => [
                'password' => $request->message
            ]
        ];
    }

    public function accountBulk(array $data): object|int {
        $required = ["logins"];
        foreach($required as $req) {
            if(empty($data[ $req ])) {
                return -1;
            }
        }

        $data['id'] = $this->tokenManager;
        $request = $this->request("AccountBulk", $data);
        if(!$request->success) {
            return 0;
        }

        return (object) [
            'success' => true,
            'message' => "Success",
            'data' => $request->message
        ];
    }

}