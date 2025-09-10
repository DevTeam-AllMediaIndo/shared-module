<?php
namespace Allmedia\Shared\Verihubs;

use Allmedia\Shared\Database\DatabaseInterface;
use Imagick;
use Exception;

class Verihubs {
    
    private $db;

    public function __construct(DatabaseInterface $db)
    {
        //Do your magic here
        $this->db = $db;
    }
    

    public function phoneValidation(string $phoneCode, int $phone): bool|string|int {
        $phone = preg_replace('/[^\d]/', '', $phone);

        /** JJika diawali 0, buang 0 dan tambahkan kode negara */
        if (preg_match('/^0\d+$/', $phone)) {
            $phone = $phoneCode . substr($phone, 1);
        }

        /** Jika tidak diawali dengan + atau 0, langsung tambahkan kode negara */
        elseif (!preg_match('/^' . preg_quote($phoneCode) . '/', $phone)) {
            $phone = $phoneCode . $phone;
        }

        // Cek validitas format [kode][nomor minimal 9 digit]
        if (strlen($phone) < 9) {
            return false;
        }

        return $phone;
    }
    
    protected function getCredential(): array {
        try {
            $sqlGet = $this->db->connect()->query("SELECT * FROM tb_verihub WHERE VERIHUB_STS = -1");
            if($sqlGet->num_rows != 1) {
                return [];
            }

            $assoc = $sqlGet->fetch_assoc();
            return [
                'appID' => $assoc['VERIHUB_APPID'],
                'appKey' => $assoc['VERIHUB_APPKEY'],
                'appKeyVerif' => $assoc['VERIHUB_APPKEY_IDVERIF'],
                'endpoint' => $assoc['VERIHUB_ENDPOINT'],
                'url_callback_otp_sms' => $assoc['VERIHUB_POSTBACK_OTP_SMS']
            ];
            
        } catch (Exception $e) {
            return [];
        }
    }

    protected function request(string $url, array $data = [], array $header = []) {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    ...$header,
                    "accept: application/json",
                    "content-type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $err = curl_error($curl);
            $resp = json_decode($response, true);

            if(!empty($err)) {
                return [
                    'success'   => false,
                    'message'   => $err,
                    'data'      => [],
                    'code'      => $httpCode
                ];
            }

            if(!in_array($httpCode, [200, 201])) {
                return [
                    'success'   => false,
                    'message'   => $resp['message'] ?? "Invalid Response",
                    'error'     => $resp['error_fields'] ?? [],
                    'data'      => [],
                    'code'      => $httpCode
                ];
            }

            return [
                'success'   => true,
                'message'   => $resp['message'] ?? "Request Successfull",
                'data'      => $resp['data'] ?? [],
                'code'      => $httpCode
            ];

        } catch (Exception $e) {
            return [
                'success'   => false,
                'message'   => "[ERROR] {$e->getMessage()} ({$e->getCode()})",
                'data'      => [],
                'code'      => 500
            ];
        }
    } 
    
    public function sendOtp_sms(array $data = []):array  {
        try {
            foreach(['phone', 'otp'] as $required) {
                if(empty($data[ $required ])) {
                    return [
                        'success'   => false,
                        'message'   => "{$required} is required",
                        'data'      => []
                    ];
                }
            }
            if(is_numeric($data['otp']) === FALSE || strlen($data['otp'] < 4)) {
                return [
                    'success'   => false,
                    'message'   => "Invalid OTP value",
                    'data'      => []
                ];
            }

            if(empty($data['mbrid'])) {
                $data['mbrid'] = $data['phone'];
            }

            /** Get Credential */
            $credential = $this->getCredential();
            if(empty($credential)) {
                return [
                    'success'   => false,
                    'message'   => "Invalid Credential",
                    'data'      => []
                ];
            }

            /** Add time_limit default */
            if(empty($data['time_limit'])) {
                $data['time_limit'] = 90;
            }

            /** Add callback_url default */
            if(empty($data['callback_url'])) {
                $data['callback_url'] = $credential['url_callback_otp_sms'];
            }

            /** Add template default */
            if(empty($data['template'])) {
                $data['template'] = '18FX KMNA OTP: $OTP';
            }

            /** Send OTP */
            $endpoint = ($credential['endpoint']."/v2/otp/send"); 
            $header  = [
                "API-Key: ".$credential['appKey'],
                "App-ID: ".$credential['appID'],
            ];

            $requestData = [
                'msisdn' => $data['phone'],
                'otp' => strval($data['otp'])
            ];

            $request = $this->request($endpoint, $requestData, $header);
            
            /** Logging */
            $this->logger($data['phone'], [
                'endpoint' => $endpoint,
                'module' => "/v1/otp/send",
                'message' => $request['message'],
                'data' => $data,
                'response' => $request,
                'code' => $request['code']
            ]); 
           
            if(!$request['success'] || !in_array($request['code'], [200, 201])) {
                return [
                    'success'   => false,
                    'message'   => $request['message'],
                    'data'      => []
                ];
            }

            return [
                'success'   => true,
                'message'   => $request['message'],
                'data'      => []
            ];

        } catch (Exception $e) {
            return [
                'success'   => false,
                'message'   => $e->getMessage(),
                'data'      => []
            ];
        }
    }

    public function validate_otp_sms($phone, $otp): bool|string {
        /** Validate Phone Number */
        $sqlCheckOtp = $this->db->connect()->query("SELECT LOGVER_DATA, LOGVER_DATETIME FROM tb_log_verihub WHERE LOGVER_MBR = {$phone} AND LOGVER_MODULE = '/v1/otp/send' ORDER BY ID_LOGVER DESC LIMIT 1");
        if($sqlCheckOtp->num_rows != 1) {
            return false;
        }

        $logver = $sqlCheckOtp->fetch_assoc();
        $logverdata = json_decode($logver['LOGVER_DATA'], true);
        if($logverdata['otp'] != $otp) {
            return "Invalid OTP";
        }

        if(!empty($logverdata['time_limit'])) {
            $dateNow = time();
            $dateExpired = strtotime(date("Y-m-d H:i:s", strtotime("+".$logverdata['time_limit']." second", strtotime($logver['LOGVER_DATETIME']))));
            if($dateNow > $dateExpired) {
                return "OTP Code Expired";
            }
        }

        return true;
    }

    public function sendOtp_sms_verification(array $data = []): array {
        try {
            foreach(['phone', 'otp'] as $required) {
                if(empty($data[ $required ])) {
                    return [
                        'success'   => false,
                        'message'   => "{$required} is required",
                        'data'      => []
                    ];
                }
            }

            if(is_numeric($data['otp']) === FALSE || strlen($data['otp'] < 4)) {
                return [
                    'success'   => false,
                    'message'   => "Invalid OTP value",
                    'data'      => []
                ];
            }

            if(empty($data['mbrid'])) {
                $data['mbrid'] = $data['phone'];
            }

            /** Get Credential */
            $credential = $this->getCredential();
            if(empty($credential)) {
                return [
                    'success'   => false,
                    'message'   => "Invalid Credential",
                    'data'      => []
                ];
            }

            /** Verify OTP */
            $endpoint = ($credential['endpoint']."/v2/otp/verify"); 
            $header  = [
                "API-Key: ".$credential['appKey'],
                "App-ID: ".$credential['appID'],
            ];

            $requestData = [
                'mbrid' => $data['mbrid'],
                'msisdn' => $data['phone'],
                'otp' => $data['otp']
            ];

            $request = $this->request($endpoint, $requestData, $header);
            
            /** Logging */
            $this->logger($data['mbrid'], [
                'endpoint' => $endpoint,
                'module' => "/v1/otp/verify",
                'message' => $request['message'],
                'data' => $data,
                'response' => $request,
                'code' => $request['code']
            ]); 

            if($request['code'] != 200) {
                $message = $request['message'];
                return [
                    'success'   => false,
                    'message'   => "{$message}",
                    'data'      => []
                ];
            }

            // if(strtolower($request['message']) != "otp has been verified") {
            //     $code = $request['code'];
            //     $message = $request['message'];
            //     return [
            //         'success'   => false,
            //         'message'   => "{$message}",
            //         'data'      => []
            //     ];
            // }
            
            return $request;

        } catch (Exception $e) {
            return [
                'success'   => false,
                'message'   => $e->getMessage(),
                'data'      => []
            ];
        }
    }

    public function send_idVerification(array $data): array {
        try {
            foreach(['mbrid', 'nik', 'name', 'birth_date', 'email', 'phone', 'selfie_photo', 'ktp_photo', 'reference_id'] as $required) {
                if(empty($data[ $required ])) {
                    return [
                        'success'   => false,
                        'message'   => "{$required} is required",
                        'data'      => []
                    ];
                }
            }

            if(is_numeric($data['nik']) === FALSE || strlen($data['nik'] < 16)) {
                return [
                    'success'   => false,
                    'message'   => "Invalid NIK value",
                    'data'      => []
                ];
            }

            /** Get Credential */
            $credential = $this->getCredential();
            if(empty($credential)) {
                return [
                    'success'   => false,
                    'message'   => "Invalid Credential",
                    'data'      => []
                ];
            }

            
            /** Verify ID */
            $data['birth_date'] = date("d-m-Y", strtotime($data['birth_date']));
            $endpoint = ($credential['endpoint']."/data-verification/certificate-electronic/verify"); 
            $header  = [
                "API-Key: ".$credential['appKeyVerif'],
                "App-ID: ".$credential['appID'],
            ];

            $request = $this->request($endpoint, $data, $header);
            if($request['code'] != 200) {
                $message = $request['message'];
                if(!empty($request['error'])) {
                    $errorFields = implode(", ", array_column($request['error'], "message")) ?? "";
                    $message = $errorFields;
                }
                
                /** Logging */
                $this->logger($data['mbrid'], [
                    'endpoint' => $endpoint,
                    'account_id' => $data['account_id'],
                    'module' => "/data-verification/certificate-electronic/verify",
                    'message' => $message,
                    'data' => $data,
                    'response' => $request,
                    'code' => $request['code']
                ]); 
                
                return [
                    'success'   => false,
                    'message'   => "{$message}",
                    'data'      => [],
                ];
            }

            /** Logging */
            $this->logger($data['mbrid'], [
                'endpoint' => $endpoint,
                'account_id' => $data['account_id'],
                'module' => "/data-verification/certificate-electronic/verify",
                'message' => $request['message'],
                'data' => $data,
                'response' => $request,
                'code' => $request['code']
            ]); 

            if(empty($request['data'])) {
                return [
                    'success'   => false,
                    'message'   => "Invalid Data",
                    'data'      => [],
                ];
            }

            if(empty($request['data']['status'])) {
                return [
                    'success'   => false,
                    'message'   => "Invalid Status",
                    'data'      => [],
                ];
            }

            if(strtolower($request['data']['status']) != "verified") {
                return [
                    'success'   => false,
                    'message'   => (empty($request['data']['reject_field']))? "Invalid Error Status" : ("Invalid ".implode(", ", $request['data']['reject_field'])),
                    'data'      => [],
                ];
            }

            return $request;

        } catch (Exception $e) {
            return [
                'success'   => false,
                'message'   => $e->getMessage(),
                'data'      => []
            ];
        }
    }

    public function getFileSize(string $bytes, string $format = "b") {
        try {
            $format = strtoupper($format);
            switch($format) {
                case "GB": return floatval(round($bytes / 1073741824, 2));
                case "MB": return floatval(round($bytes / 1048576, 2));
                case "KB": return floatval(round($bytes / 1024, 2));
                case "B": return floatval($bytes);
            }

            return 0;

        } catch (Exception $e) {
            return 0;
        }
    }

    public function validate_photoSelfie(array $filePost): string | array {
        try {
            if(empty($filePost['tmp_name'])) {
                return "Invalid file path";
            }

            $fileSize = getimagesize($filePost['tmp_name']);
            $width  = $fileSize[0] ?? 0;
            $height = $fileSize[1] ?? 0;
            $bits   = filesize($filePost['tmp_name']) ?? 0;
            $mime   = mime_content_type($filePost['tmp_name']);
            $image  = new Imagick($filePost['tmp_name']);
            $aspectRatio = ($width / $height);

            /** Scaling up */
            if($width <= 480) {
                $width = 480;
                $height = 480 / $aspectRatio;
            }

            if($height < 640) {
                $height = 640;
                $width = 640 * $aspectRatio;
            }

            // if($width < 480 || $height < 640 || $this->getFileSize($bits, "KB") < 100) {
            //     $width = 1280;
            //     $height = 720;
            //     $image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 0.5);
            // }

            $image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1);
            $image->setImageFormat("jpeg");
            $filePost['image_scaling'] = $image->getImageBlob();

            return [
                ...$filePost,
                'type' => $mime
            ];

        } catch (Exception $e) {
            return [
                'success'   => false,
                'message'   => $e->getMessage(),
                'data'      => []
            ];
        }
    }

    public function validate_photoKtp(array $filePost): string | array {
        try {
            if(empty($filePost['tmp_name'])) {
                return "Invalid file path (KTP Photo)";
            }

            $fileSize = getimagesize($filePost['tmp_name']);
            $width  = $fileSize[0] ?? 0;
            $height = $fileSize[1] ?? 0;
            $bits   = filesize($filePost['tmp_name']) ?? 0;
            $mime   = mime_content_type($filePost['tmp_name']);
            $image = new Imagick($filePost['tmp_name']);
            $aspectRatio = ($width / $height);

            /** Scaling up */
            if($width <= 480) {
                $width = 480;
                $height = 480 / $aspectRatio;
            }

            if($height < 360) {
                $height = 360;
                $width = 360 * $aspectRatio;
            }

            // if($width < 480 || $height < 360 || $this->getFileSize($bits, "KB") < 100) {
            //     $width = 1920;
            //     $height = 1080;
            // }
            
            $image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 0.5);
            $image->setImageFormat("jpeg");
            $filePost['image_scaling'] = $image->getImageBlob();

            /** Check Dimension */
            // if($width < 480) {
            //     return "Minimum 480px (horizontal) (KTP Photo)";
            // }

            // if($height < 360) {
            //     return "Minimum 360px (vertical) (KTP Photo)";
            // }
            
            // /** Check File Size */
            // $minimum = $this->getFileSize($bits, "KB");
            // if($minimum < 100) {
            //     return "Minimum file size 100KB (KTP Photo)";
            // }

            // $maximum = $this->getFileSize($bits, "MB");
            // if($maximum > 2) {
            //     return "Maximum file size 4MB (KTP Photo)";
            // }

            return [
                ...$filePost,
                'type' => $mime
            ];


        } catch (Exception $e) {
            return [
                'success'   => false,
                'message'   => $e->getMessage(),
                'data'      => []
            ];
        }
    }

    public function detectFaceLiveness(array $data = []) {
        if(empty($data['mbrid'])) {
            return [
                'success'   => false,
                'message'   => "Invalid User",
                'data'      => []
            ];
        }

        if(empty($data['image'])) {
            return [
                'success'   => false,
                'message'   => "Invalid Image",
                'data'      => []
            ];
        }

        /** Get Credential */
        $credential = $this->getCredential();
        if(empty($credential)) {
            return [
                'success'   => false,
                'message'   => "Invalid Credential",
                'data'      => []
            ];
        }

        /** Detect face liveness */
        $requestData = [
            'image' => $data['image'],
            'is_quality' => true,
            'is_attribute' => true,
            'validate_quality' => true,
            'validate_attribute' => true,
            'validate_nface' => true
        ];

        $endpoint = ($credential['endpoint']."/v1/face/liveness"); 
        $header  = [
            "API-Key: ".$credential['appKeyVerif'],
            "App-ID: ".$credential['appID'],
        ];

        /** Send Request */
        $request = $this->request($endpoint, $requestData, $header);

        /** Logging */
        $this->logger($data['mbrid'], [
            'endpoint' => $endpoint,
            'module' => "/v1/face/liveness",
            'message' => $request['message'],
            'data' => $requestData,
            'response' => $request,
            'code' => $request['code']
        ]);

        return $request;
    }

    public function logger(int $mbrid, array $data = []) {
        try {
            $this->db->insert("tb_log_verihub", [
                'LOGVER_MBR' => $mbrid,
                'LOGVER_ACC' => ($data['account_id'] ?? NULL),
                'LOGVER_ENDPOINT' => $data['endpoint'] ?? "-",
                'LOGVER_MODULE' => $data['module'] ?? "-",
                'LOGVER_MESSAGE' => $data['message'] ?? "-",
                'LOGVER_DATA' => json_encode($data['data'] ?? []),
                'LOGVER_RESPONSE' => json_encode($data['response' ?? []]),
                'LOGVER_CODE' => $data['code'] ?? 0,
                'LOGVER_DATETIME' => date("Y-m-d H:i:s")
            ]);

        } catch (Exception $e) {
            return [];
        }
    }
}