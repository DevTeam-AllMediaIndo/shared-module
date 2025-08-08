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
                'success' => false,
                'message' => $error ?? "Error Response",
                'data' => []
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

        $config = [
            'id' => $data['id'],
            'symbol' => $data['symbol'],
            'date_From' => $data['date_from'],
            'date_To' => $data['date_to'],
        ];

        if(!empty($data['timeframe'])) {
            $config['timeframe'] = $data['timeframe'];
        }

        $prices = $this->request("PriceHistory", $config);
        if(!is_object($prices)) {
            return (object) [
                'success' => true,
                'message' => $resp->message ?? "Invalid Object",
                'data' => []
            ];
        }

        if(is_object($prices->message) && property_exists($prices->message, "status")) {
            if ($prices->message->status != "success") {
                return (object) [
                    'success' => true,
                    'message' => $resp->message ?? "Invalid Message",
                    'data' => []
                ];
            }
        }

        return (object) [
            'success' => true,
            'message' => "",
            'data' => $prices->message
        ];
    }

    public function symbols(array $data): object|bool {
        $required = ['id'];
        foreach($required as $req) {
            if(empty($data[ $req ])) {
                return false;
            }
        }

        $apiData = [
            'id' => $data['id']
        ];

        if(!empty($data['group'])) {
            $apiData['group'] = $data['group'];
        }

        $symbols = $this->request("Symbols", $apiData);
        if(!is_object($symbols)) {
            return (object) [
                'success' => true,
                'message' => $symbols->message ?? "Invalid Object",
                'data' => $symbols->message
            ];
        }

        if(is_object($symbols->message) && property_exists($symbols->message, "status")) {
            if ($symbols->status != "success") {
                return (object) [
                    'success' => false,
                    'message' => $symbols->message ?? "Invalid data",
                    'data' => []
                ];
            }
        }

        return (object) [
            'success' => true,
            'message' => "Berhasil",
            'data' => $symbols->message
        ];
    }

}