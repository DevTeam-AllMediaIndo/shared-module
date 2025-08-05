<?php
namespace Allmedia\Shared\Metatrader;

class ApiManager {

    protected string $endpoint = "http://45.76.163.26:5001";
    protected string $tokenManager;

    public function __construct(string $tokenManager) {
        $this->tokenManager = $tokenManager;
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

}