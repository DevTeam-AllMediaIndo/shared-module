<?php
namespace Allmedia\Shared\Metatrader;

class ApiTerminal {

    protected string $endpoint = "http://45.76.163.26:5001";
    protected string $server;

    public function __construct(string $server) {
        $this->server = $server;
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

        return $resp;
    }

    private function isValidToken($token = ""): bool {
        if (!is_string($token)) {
            return false;
        }

        if (strlen($token) < 32) {
            return false;
        }

        if (!(strpos("-", $token) === FALSE)) {
            return false;
        }

        return true;
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
        if(!$this->isValidToken($connect)) {
            return false;
        }

        return $connect;
    }

    public function priceHistory(array $data): object|bool {
        $required = ['id', 'symbol', 'date_from', 'date_to'];
        foreach($required as $req) {
            if(empty($data[ $req ])) {
                return false;
            }
        }
        
        if ($required !== TRUE) {
            return (object) [
                'success'   => false,
                'error'     => $required ?? "Parameter tidak lengkap",
                'message'   => []
            ];
        }

        $config = [
            'id' => $data['id'],
            'symbol' => $data['symbol'],
            'from' => $data['date_from'],
            'to' => $data['date_to'],
        ];

        if(!empty($data['timeframe'])) {
            $config['timeframe'] = $data['timeframe'];
        }

        $prices = $this->request("PriceHistory", $config);
        if(!is_object($prices)) {
            return (object) [
                'success'   => false,
                'error'     => $resp->message ?? "Invalid Object",
                'message'   => ""
            ];
        }

        if(is_object($prices->message) && property_exists($prices->message, "status")) {
            if ($prices->message->status != "success") {
                return (object) [
                    'success'   => false,
                    'error'     => $prices->message->message,
                    'message'   => ""
                ];
            }
        }

        return (object) [
            'success' => true,
            'm message' => "",
            'data' => $prices->message
        ];
    }

}