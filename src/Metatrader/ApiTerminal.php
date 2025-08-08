<?php
namespace Allmedia\Shared\Metatrader;

class ApiTerminal {

    protected string $endpoint = "http://45.76.163.26:5001";
    protected string $server;

    public function __construct(string $server) {
        $this->server = $server;
    }

    public function request(string $command, array $data = []): object {
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

    public function connect(array $data): string|bool {
        $required = ["login", "password"];
        foreach($required as $req) {
            if(empty($data[ $req ])) {
                return false;
            }
        }

        $apiData = [
            'mtlogin' => $data['login'],
            'mtPassw' => $data['password'],
            'mtServr' => $this->server
        ];

        $connect = $this->request("Connect", $apiData);
        if(!$connect->success) {
            return false;
        }

        return $connect->message;
    }


}