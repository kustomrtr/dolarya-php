<?php
require 'vendor/autoload.php';
require 'config.php';
//include 'src/chromephp/ChromePhp.php';

$pb = new Pushbullet\Pushbullet($api_key);

Pushbullet\Connection::setCurlCallback(function ($curl) {
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
});

$json = json_decode(str_replace("var dolartoday = \n", "", file_get_contents($url)));
$valorBs = $json->{"USD"}->{"transferencia"};
$filename = dirname(__FILE__) . "/dolar.txt";
$dolarhoy = $valorBs . " Bs.";
$cambio = false;

try {
  $dolarfile = fopen($filename, "r") or die("Unable to open file!");
    if($dolarhoy == fgets($dolarfile) ) {
      echo 'El valor es el mismo ' . $dolarhoy;
      //ChromePhp::log('Hello console!');
    } else {
      echo 'El valor es diferente ' . $dolarhoy;
      $cambio = true;
    }
} catch(Exception $e) {
  echo 'Error ' . $e;
}

if($cambio) {
  try {
    $pb->allDevices()->pushNote($dolarhoy, "Nuevo valor del dolar: " . $dolarhoy);
    //ChromePhp::log('Push sent.');
    $dolarfile = fopen($filename, "w") or die("Error al abrir el archivo!");
    fwrite($dolarfile, $dolarhoy);
    fclose($dolarfile);
  } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
  }
}

?>
