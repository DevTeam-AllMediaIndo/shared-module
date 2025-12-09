<?php
namespace Allmedia\Shared\FileUpload;

use Aws\S3\Exception\S3Exception;
use CURLFile;
use Exception;

// if (!class_exists('Aws\\S3\\S3Client')) {
//     require_once __DIR__ . '/vendor/autoload.php';
// }

/**
 * Simple FileUpload helper that builds S3 URLs and holds upload config.
 */
class FileUpload
{
    private const DEFAULT_CURL_URL = 'https://upload-aws.techcrm.net/api/upload';

    private string $curlUrl;
    private string $region = '';
    private string $bucket = '';
    private string $folder = '';

    public array $error_messages = [
        UPLOAD_ERR_OK => 'There is no error, the file uploaded with success',
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
    ];

    /**
     * Construct with optional config overrides. Fallback to env variables.
     * @param array{curlUrl?:string, region?:string, bucket?:string, folder?:string}|null $config
     */
    public function __construct(?array $config = null)
    {
        $this->curlUrl = $config['curlUrl'] ?? $this->getEnv('FILE_UPLOAD_URL', self::DEFAULT_CURL_URL);
        $this->region = $config['region'] ?? $this->getEnv('AWS_REGION', '');
        $this->bucket = $config['bucket'] ?? $this->getEnv('AWS_BUCKET', '');
        $this->folder = $config['folder'] ?? $this->getEnv('AWS_FOLDER', '');
    }

    private function getEnv(string $key, string $default = ''): string
    {
        $val = $_ENV[$key] ?? getenv($key);
        if ($val === false || $val === null) {
            return $default;
        }
        return (string) $val;
    }

    /** Setters (fluent) */
    public function setCurlUrl(string $url): self
    {
        $this->curlUrl = $url;
        return $this;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function setBucket(string $bucket): self
    {
        $this->bucket = $bucket;
        return $this;
    }

    public function setFolder(string $folder): self
    {
        $this->folder = $folder;
        return $this;
    }

    /** Getters */
    public function getCurlUrl(): string
    {
        return $this->curlUrl;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getBucket(): string
    {
        return $this->bucket;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    /**
     * Build base S3 URL. Returns empty string when required parts missing.
     */
    public function awsUrl(): string
    {
        if ($this->bucket === '' || $this->region === '') {
            return '';
        }

        $folder = trim($this->folder, '/');
        $base = sprintf('https://%s.s3.%s.amazonaws.com', $this->bucket, $this->region);

        return $folder === '' ? $base : $base . '/' . $folder;
    }

    /**
     * Build full S3 file URL for a given filename.
     */
    public function awsFile(?string $filename = null): string
    {
        if (empty($filename)) {
            return '';
        }

        $base = $this->awsUrl();
        if ($base === '') {
            return '';
        }

        return rtrim($base, '/') . '/' . ltrim($filename, '/');
    }

    public function upload_single(array $files, string $file_prefix = "upload", bool $compress = false, int $quality = 25) {
        try {
            if(empty($files) || $files['error'] != 0) {
                return $error_messages[ $files['error'] ] ?? "[ERROR] Upload file gagal";
            }

            /** file info */
            $file_info = pathinfo($files['name']);

            /** check if extension allowed */
            $image_ext = ["application/pdf", "pdf", "image/png", "image/jpeg", "image/jpg", "image/webp", "png", "jpg", "jpeg", "webp"];
            if(!in_array($files['type'], $image_ext) && !in_array($file_info['extension'], $image_ext)) {
                return "[Invalid file type], Mohon upload file ".implode(", ", $image_ext);
            }

            /** check image size */
            $image_size = getimagesize($files['tmp_name']);
            if(!is_array($image_size)) {
                return "Fail to get detail of image";
            }

            /** check dimension */
            $image_width    = $image_size[0] ?? 0;
            $image_height   = $image_size[1] ?? 0;
            if(!$image_width || !$image_height) { // Cek Dimensi
                return "Invalid Dimension";    
            } 

            $mime = mime_content_type($files['tmp_name']);
            if($mime === FALSE) {
                return "Invalid File Type";
            }

            $upload = $this->upload(new CURLFile($files['tmp_name'], $mime, $files['name']));
            if(!is_array($upload) || !array_key_exists("filename", $upload)) {
                return $upload ?? "Failed to upload image";
            }

            $default = [
                'size' => $files['size'],
                'extension' => $file_info['extension'],
                'mime' => $mime,
            ];

            return array_merge($default, $upload);

        } catch (Exception $e) {
            return $e->getMessage();
            
        } catch (S3Exception $s3) {
            return $s3->getAwsErrorMessage();
        }
    }

    private function upload(CURLFile $file) {
        try {
            if(empty($this->folder)) {
                return "Invalid AWS Folder";
            }

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->curlUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'directory' => $this->folder,
                    'files'=> $file
                ),
            ]);
    
            $response = curl_exec($curl);
            $error = curl_error($curl);
    
            if(!empty($error)) {
                return $error ?? "Failed to upload image";
            }
            
            $resp = json_decode($response);
            if(!is_object($resp) || !property_exists($resp, "success")) {
                return "Invalid Response";
            }
    
            if(!$resp->success) {
                return $resp->message ?? "Invalid Message";
            }
    
            $result = $resp->results[0] ?? [];
            if(!$result) {
                return "Invalid Result";
            }
    
            return [
                'filename' => $result->newName,
                'url' => $result->url,
                'dir' => $result->key,
            ];

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}