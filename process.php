<?php
require_once('./includes/api.lib.dynamodb.php');

$rlist = array();
$file = fopen("/tmp/r.csv","r");

while(! feof($file))
  {
    $read= fgetcsv($file);
    $rlist[] = array(
                 'TableName' => 'BusinessTable',
                 'Item' => array(
                     'BusinessId'      => array('N' => $read[0]),
                     'BusinessName'    => array('S' => $read[1]),
                     'State'            => array('S' => $read[4]),
                     'City'            => array('S' => $read[3]),
                     'Street'            => array('S' => $read[2]),
                     'Zip'            => array('S' => $read[5])
                   )
                 );
    
  }

  print_r($rlist);
fclose($file);
      $dynamodb = new DynamoDB();
      $dynamodb->putBusiness($rlist);
?>
