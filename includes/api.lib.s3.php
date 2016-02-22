<?php

require './includes/vendor/autoload.php';
require_once('./includes/vendor/apache/log4php/src/main/php/Logger.php');
use Aws\Common\Aws;

class S3 {
 private $s3Client;
 private $logger;

 function __construct() {
    Logger::configure('./config/config.xml');
    $this->logger = Logger::getLogger('myLogger');
    $this->logger->trace("S3::_construct ");
    $aws = Aws::factory('./includes/api.lib.creadential.php');
    $this->s3Client = $aws->get('S3');
 }

 public function put($file) {
   $result = $this->s3Client->putObject(array(
      'Bucket' => 'digelp-video',
      'Key'    => $file,
      'SourceFile'   => $file
   ));
  $this->logger->trace("S3::put" . $result['ObjectURL']);
  return $result['ObjectURL'];
 }

  public function get($key) {
   $result = $this->s3Client->getObject(array(
      'Bucket' => 'digelp-video',
      'Key'    => $key
   ));
   return $get_class['Body'];
 }
}
