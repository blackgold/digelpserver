<?php
require_once('./includes/api.lib.SurveyPostJsonMessageParser.php');
require_once('./includes/api.lib.dynamodb.php');
require_once('./includes/api.lib.s3.php');
require_once('./includes/api.lib.queryProcessor.php');
require_once('./includes/vendor/apache/log4php/src/main/php/Logger.php');

class ReqController
{
    private $method; // POST,GET
    private $api; // video,survey,get
    private $version; // api version
    private $data; //  
    private $file; //
    private $authSignature; // http header conatining signature
    private $businessId;  // business id in header
    private $validRequest;  // derived from abovee information
    private $surveyMessage; //  test survey post data
    private $logger; // = Logger::getLogger("main");
    private $query; // = Logger::getLogger("main");
    public function __construct()
    {
        Logger::configure('./config/config.xml');
        $this->logger = Logger::getLogger('myLogger');
        $this->validRequest = 0;
        $this->data = array();
        $this->method = strtolower( $_SERVER['REQUEST_METHOD'] );
        $this->logger->trace("ReqController::_construct" . $this->method );
        switch ( $this->method )
        {
            case 'post':
                $path = $_SERVER['REQUEST_URI'];
                $chunks = explode('/', $path);
                if(count($chunks) == 4) {
                    $this->logger->trace("ReqController::_construct chunks " . $chunks[3] . " " . $chunks[2] . "  " . count($chunks));
                    $this->version = $chunks[2];
                    $this->api = $chunks[3];
                    $headers = apache_request_headers();
                    if(!empty($headers)) {
                        $this->logger->trace("ReqController::_construct headers ");
                        $this->authSignature = $headers['Auth'];
                        if(!empty($this->authSignature) ) {
                           $this->logger->trace("ReqController::_construct signature ");
                           if($this->api == "survey") {
                               $this->logger->trace("ReqController::_construct survey ");
                               $this->data = file_get_contents( 'php://input' );
                               if(!empty($this->data)) {
                                   $this->logger->trace("ReqController::_construct data " . $this->data);
                                   $this->surveyMessage = new SurveyPostJsonMessage($this->data);
                                   if($this->surveyMessage->isOk == 1) {
                                       $this->validRequest = 1;
                                       $this->logger->trace("ReqController::_construct data YEEY VALID POST SURVEY REQUEST");
                                   }
                               }
                           }
                           else if($this->api == "video") {
                               $this->logger->trace("ReqController::_construct video - ");
                               $this->businessId = $headers['BusinessId'];
                               $this->logger->trace("ReqController::_construct video -- " . $this->businessId );
                               if(!empty($this->businessId)) {
                                   $this->file = "/tmp/";
                                   $this->file = $this->file . basename( $_FILES['upload-file']['name']);
                                   $this->logger->trace("ReqController::_construct video  --- " . $this->file );
                                   if(move_uploaded_file($_FILES['upload-file']['tmp_name'], $this->file)) {
                                      $this->validRequest = 1;
                                      $this->logger->trace("ReqController::_construct video  --- move done" );
                                   }
                               }
                           }
                           else {
                               $this->validRequest = 0;
                           }
                        }
                    }
                }
                break;
            case 'get':
                $path = $_SERVER['REQUEST_URI'];
                $chunks = explode('/', $path);
                $this->logger->trace("ReqController::_construct path " . $path . " chunks " . $chunks[3] . " " . $chunks[2] . "  " . count($chunks));
                if(count($chunks) == 4)  {
                     $chunks1 = explode('?', $chunks[3]);
                     if(count($chunks1) > 1 ) {
                         $this->version = $chunks[2];
                         $this->api = $chunks1[0];
                         $headers = apache_request_headers();
                         $this->query = $_SERVER['QUERY_STRING'];
                         $this->logger->trace("ReqController::_construct version " . $this->version . " api " . $this->api . " query " . $this->query);
                         if(!empty($this->query)) {
                            if($this->api == "survey") {
                                   $this->validRequest = 1;
                            }
                            else if($this->api == "video") {
                                 $this->validRequest = 1;
                            }
                            else {
                               $this->validRequest = 0;
                            }
                         }
                      }
                }
                break;
            case 'put':
                break;
            default:
                break;
        }
        //$authSignature =  read auth header value
        return $this; 
    }

    public static function respond($status = 200, $body = '', $content_type = 'text/html')
    {
        $httpCode = 'HTTP/1.1 ' . $status . ' ' . self::getHttpCode($status);
        header($httpCode);
        header('Content-type: ' . $content_type . '; charset=utf-8');
        header("Access-Control-Allow-Origin: *");
        if($body != '')
        {
            echo $body;
        }
        else
       {
          $message = '';
          switch($status)
          {
             case 404:
             $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
             break;
          }
          $body = ' ' . $status . ' ' . self::getHttpCode($status) . ' ' . $message . '  ' ; 
          //echo $body; 
       }
    } 

    public static function getHttpCode($status)
    {
        $codes = Array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }
     
    public function getMethod()
    {
        return $this->method;
    }
    
    public function isAuthorized() 
    {
       return $this->validRequest;
    }

    public function validQueryParameters() 
    {
       return $this->validRequest;
    }

   public function  updateDB() 
   {
      $this->logger->trace("ReqController::updateDB");
      //TODO batch upload
      $dynamodb = new DynamoDB();
      $dynamodb->putSurvey($this->surveyMessage->businessId, $this->surveyMessage->timestamp, $this->surveyMessage->survey);
      return 1;
   }   

   public function  getHttpMethod()
   {
      return $this->method;
   }
 
   public function  getApiMethod() 
   {
      return $this->api;
   }

   public function  getApiVersion() 
   {
      return $this->version;
   }

   public function  validVideo()
   {
      return 1;
   }
   
   public function  updateS3()
   {
      $this->logger->trace("ReqController::updateS3");
      //TODO batch upload
      $ts = time();
      $s3 = new S3();
      $url = $s3->put($this->file);
      $dynamodb = new DynamoDB();
      $this->logger->trace("ReqController::updateS3" . $url . "  " . $this->businessId . "  " . $ts);
      $dynamodb->putVideo($this->businessId,$url,$ts);
      return 1;
   }

   public function  getSurvey()
   {
      $this->logger->trace("ReqController::getSurvey");
      $qp = new QueryProcessor(); 
      $qp->processSurveyQuery($this->query);
      return 1;
   }

   public function  getVideo()
   {
      $this->logger->trace("ReqController::getVideo");
      $qp = new QueryProcessor(); 
      $qp->processVideoQuery($this->query);
      return 1;
   }
  
   public function downLoadVideo($key) 
   {
      $this->logger->trace("ReqController::downloadVideo");
      $s3 = new S3();
      $data = $s3->get($key);
      echo $data;
   }
}
 
?>

