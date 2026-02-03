<?php
namespace Allmedia\Shared\Metatrader;

use Allmedia\Shared\SystemInfo;
use Exception;

class ApiTerminal {

    protected string $endpoint;
    protected string $server;

    public function __construct(string $server, string $endpoint = "http://45.76.163.26:5001") {
        $this->endpoint = $endpoint;
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
            'mtPassw' => trim(base64_encode($data['password'])),
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

    public function orderSend(array $data): object {
        try {
            $required = ['id', 'symbol', 'operation', 'volume'];
            foreach($required as $req) {
                if(empty($data[ $req ])) {
                    return (object) [
                        'success' => false,
                        'message' => "invalid {$req}",
                        'data' => []
                    ];
                }
            }
            
            if(is_numeric($data['volume']) === FALSE || $data['volume'] <= 0) {
                return (object) [
                    'success' => false,
                    'message' => "invalid volume: " . $data['volume'],
                    'data' => []
                ];
            }

            /** Validasi Operation */
            if(!in_array($data['operation'], ApiVariable::operations())) {
                return (object) [
                    'success' => false,
                    'message' => "invalid operation: " . $data['operation'],
                    'data' => []
                ];
            }

            /** SL (opsional) */
            if(isset($data['sl']) && $data['sl'] !== '') {
                if(is_numeric($data['sl']) === FALSE || $data['sl'] < 0) {
                    return (object) [
                        'success' => false,
                        'message' => "invalid SL: " . $data['sl'],
                        'data' => []
                    ];
                }
            }

            /** TP (opsional) */
            if(isset($data['tp']) && $data['tp'] !== '') {
                if(is_numeric($data['tp']) === FALSE || $data['tp'] < 0) {
                    return (object) [
                        'success' => false,
                        'message' => "invalid TP: " . $data['tp'],
                        'data' => []
                    ];
                }
            }

            /** 
             * Price
             * Opsional saat operation buy / sell
             * Required saat operation buylimit, selllimit, buystop, sellstop
             * */
            if(isset($data['price']) && $data['price'] !== '') {
                if(is_numeric($data['price']) === FALSE || $data['price'] < 0) {
                    return (object) [
                        'success' => false,
                        'message' => "invalid Price: " . $data['price'],
                        'data' => []
                    ];
                }
            }

            if(in_array($data['operation'], [ApiVariable::$operationBuyLimit, ApiVariable::$operationSellLimit, ApiVariable::$operationBuyStop, ApiVariable::$operationSellStop])) {
                if($data['price'] <= 0) {
                    return (object) [
                        'success' => false,
                        'message' => "Price is required for pending order",
                        'data' => []
                    ];
                }
            }

            /** Order Send */
            $orderEndpoint = in_array($data['operation'], [ApiVariable::$operationBuy, ApiVariable::$operationSell]) ? "OrderSend" : "OrderPlace";
            $orderSend = $this->request($orderEndpoint, $data);
            if(!is_object($orderSend) || !property_exists($orderSend, "status")) {
                return (object) [
                    'success' => false,
                    'message' => $orderSend->message ?? "Invalid Response",
                    'data' => []
                ];
            }

            if($orderSend->status != "success") {
                return (object) [
                    'success' => false,
                    'message' => $orderSend->message ?? "Order Place Failed",
                    'data' => []
                ];
            }

            $ticket = $orderSend->message->ticket ?? false;
            if(!$ticket) {
                return (object) [
                    'success' => false,
                    'message' => "Invalid Ticket",
                    'data' => []
                ];
            }

            return (object) [
                'success' => true,
                'message' => "Berhasil",
                'data' => $orderSend->message
            ];
            
            
        } catch (Exception $e) {
            return (object) [
                'success' => false,
                'message' => "Internal Server Error (500)",
                'data' => []
            ];
        }
    }

    public function orderClose(array $data): object {
        try {
            $required = ['id', 'ticket'];
            foreach($required as $req) {
                if(empty($data[ $req ])) {
                    return (object) [
                        'success' => false,
                        'message' => "invalid {$req}",
                        'data' => []
                    ];
                }
            }

            $isPendingOrder = $data['is_pending'] ?? 0;
            if(!in_array($isPendingOrder, [0,1])) {
                return (object) [
                    'success' => false,
                    'message' => "Invalid is_pending value: {$isPendingOrder}",
                    'data' => []
                ];
            }

            $closeConfig = [
                'id' => $data['id'],
                'id_ticket' => $data['ticket']
            ];

            $method = ($isPendingOrder) ? "OrderPlaceClose" : "OrderClose";
            $orderClose = $this->request($method, $closeConfig);
            if(!is_object($orderClose) || !property_exists($orderClose, "status")) {
                return (object) [
                    'success' => false,
                    'message' => $orderSend->message ?? "Invalid Response",
                    'data' => []
                ];
            }

            if($orderClose->status != "success") {
                return (object) [
                    'success' => false,
                    'message' => $orderClose->message ?? "Order Close Failed",
                    'data' => []
                ];
            }

            $ticket = $orderClose->message->ticket ?? false;
            if(!$ticket) {
                return (object) [
                    'success' => false,
                    'message' => "Invalid Ticket",
                    'data' => []
                ];
            }

            return (object) [
                'success' => true,
                'message' => "",
                'data' => $orderClose->message
            ];


        } catch (Exception $e) {
            return (object) [
                'success' => false,
                'message' => "Internal Server Error (500)",
                'data' => []
            ];
        }
    }

    public function accountSummary(array $data): object {
        try {
            $required = ["id"];
            foreach($required as $req) {
                if(empty($data[ $req ])) {
                    return (object) [
                        'success' => false,
                        'message' => "{$req} is required",
                        'data' => []
                    ];
                }
            }

            $accountSummary = $this->request("AccountSummary", ['id' => $data['id']]);
            if(!is_object($accountSummary) || !property_exists($accountSummary, "status")) {
                return (object) [
                    'success' => false,
                    'message' => "Invalid Message",
                    'data' => []
                ];
            }

            if($accountSummary->status !== "success") {
                return (object) [
                    'success' => false,
                    'message' => $accountSummary->message ?? "Invalid Object",
                    'data' => []
                ];
            }

            return (object) [
                'success' => true,
                'message' => "Successfull",
                'data' => $accountSummary->message
            ];

        } catch (Exception $e) {
            return (object) [
                'success' => false,
                'message' => (SystemInfo::isDevelopment())? $e->getMessage() : "Internal Server Error",
                'data' => []
            ];
        }
    }

    public function openedOrders(array $data) {
        try {
            $required = ["id"];
            foreach($required as $req) {
                if(empty($data[ $req ])) {
                    return (object) [
                        'success' => false,
                        'message' => "{$req} is required",
                        'data' => []
                    ];
                }
            }

            $openedOrders = $this->request("OpenedOrders", ['id' => $data['id']]);
            if(!is_object($openedOrders) && property_exists($openedOrders, "status")) {
                if ($openedOrders->status != "success") {
                    return (object) [
                        'success' => false,
                        'message' => $openedOrders->message ?? "Invalid data",
                        'data' => []
                    ];
                }
            }

            return (object) [
                'success' => true,
                'message' => "Berhasil",
                'data' => $openedOrders->message
            ];

        } catch (Exception $e) {
            return (object) [
                'success' => false,
                'message' => (SystemInfo::isDevelopment())? $e->getMessage() : "Internal Server Error",
                'data' => []
            ];
        }
    }

    public function historyOrders(array $data): object {
        try {
            $required = ["id", "from", "to"];
            foreach($required as $req) {
                if(empty($data[ $req ])) {
                    return (object) [
                        'success' => false,
                        'message' => "{$req} is required",
                        'data' => []
                    ];
                }
            }

            $requestData = [
                'id' => $data['id'],
                'date_From' => date("Y-m-d", strtotime($data['from'])),
                'date_To' => date("Y-m-d", strtotime($data['to'])),
            ];

            $openedOrders = $this->request("OpenedOrders", $requestData);
            if(!is_object($openedOrders) && property_exists($openedOrders, "status")) {
                if ($openedOrders->status != "success") {
                    return (object) [
                        'success' => false,
                        'message' => $openedOrders->message ?? "Invalid data",
                        'data' => []
                    ];
                }
            }

            return (object) [
                'success' => true,
                'message' => "Berhasil",
                'data' => $openedOrders->message
            ];

        } catch (Exception $e) {
            return (object) [
                'success' => false,
                'message' => (SystemInfo::isDevelopment())? $e->getMessage() : "Internal Server Error",
                'data' => []
            ];
        }
    }

    public function orderModify(array $data): object {
        try {
            $required = ['id', 'ticket'];
            foreach($required as $req) {
                if(empty($data[ $req ])) {
                    return (object) [
                        'success' => false,
                        'message' => "invalid {$req}",
                        'data' => []
                    ];
                }
            }

            /** stopLoss Validation */
            if(isset($data['sl']) && $data['sl'] !== '') {
                if(is_numeric($data['sl']) === FALSE || $data['sl'] < 0) {
                    return (object) [
                        'success' => false,
                        'message' => "invalid SL: " . $data['sl'],
                        'data' => []
                    ];
                }
            }

            /** take profit validation */
            if(isset($data['tp']) && $data['tp'] !== '') {
                if(is_numeric($data['tp']) === FALSE || $data['tp'] < 0) {
                    return (object) [
                        'success' => false,
                        'message' => "invalid TP: " . $data['tp'],
                        'data' => []
                    ];
                }
            }

            /** isPending */
            $isPendingTransaction = $data['is_pending'] ?? 0;
            if(!in_array($isPendingTransaction, [0,1])) {
                return (object) [
                    'success' => false,
                    'message' => "Invalid is_pending value: {$isPendingTransaction}",
                    'data' => []
                ];
            }

            $modifyData = [
                'id' => $data['id'],
                'ticket' => $data['ticket'],
                'sl' => $data['sl'],
                'tp' => $data['tp'],
            ];

            /** Order Modify */
            $endpointUrl = ($isPendingTransaction == 1) ? "OrderPlaceModify" : "OrderModify";
            $orderModify = $this->request($endpointUrl, $modifyData);
            if(!is_object($orderModify) || !property_exists($orderModify, "status")) {
                return (object) [
                    'success' => false,
                    'message' => $orderModify->message ?? "Invalid Response",
                    'data' => []
                ];
            }

            if($orderModify->status != "success") {
                return (object) [
                    'success' => false,
                    'message' => $orderModify->message ?? "Order Modify Failed",
                    'data' => []
                ];
            }

            $ticket = $orderModify->message->ticket ?? false;
            if(!$ticket) {
                return (object) [
                    'success' => false,
                    'message' => "Invalid Ticket",
                    'data' => []
                ];
            }

            return (object) [
                'success' => true,
                'message' => "Berhasil",
                'data' => $orderModify->message
            ];

        } catch (Exception $e) {
            return (object) [
                'success' => false,
                'message' => (SystemInfo::isDevelopment())? $e->getMessage() : "Internal Server Error",
                'data' => []
            ];
        }
    }
   
    public function openedPendingOrders(array $data): object {
        try {
            $required = ["id"];
                if(empty($data[ $req ])) {
                    return (object) [
                        'success' => false,
                        'message' => "{$req} is required",
                        'data' => []
                    ];
                }
            }

            $openedPendingOrders = $this->request("OpenedPendingOrders", ['id' => $data['id']]);
            if(!is_object($openedPendingOrders) && property_exists($openedPendingOrders, "status")) {
                if ($openedPendingOrders->status != "success") {
                    return (object) [
                        'success' => false,
                        'message' => $openedPendingOrders->message ?? "Invalid data",
                        'data' => []
                    ];
                }
            }

            if($openedPendingOrders->status !== "success") {
                return (object) [
                    'success' => false,
                    'message' => $openedPendingOrders->message ?? "Invalid Object",
                    'data' => []
                ];
            }

            return (object) [
                'success' => true,
                'message' => "Berhasil",
                'data' => $openedPendingOrders->message
            ];

        } catch (Exception $e) {
            return (object) [
                'success' => false,
                'message' => (SystemInfo::isDevelopment())? $e->getMessage() : "Internal Server Error",
                'data' => []
            ];
        }
    }
    
}