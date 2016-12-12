<?php
require 'vendor/autoload.php';
include 'src/chromephp/ChromePhp.php';

$api_key = 'API KEY';
$url = 'JSON URL';
$pb = new Pushbullet\Pushbullet($api_key);

Pushbullet\Connection::setCurlCallback(function ($curl) {
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
});

$json = json_decode(str_replace("var dolartoday = \n", "", file_get_contents($url)));
$valorBs = $json->{"USD"}->{"transferencia"};
$filename = "dolar.txt";
$dolarhoy = $valorBs . " Bs.";
$cambio = false;

$dolarfile = fopen($filename, "r");
while (!feof($dolarfile)) {
  $line_of_text = fgets($dolarfile);
  if($dolarhoy == $line_of_text) {
    echo 'El valor es el mismo ' . $dolarhoy;
    ChromePhp::log('Hello console!');
  } else {
    echo 'El valor es diferente ' . $dolarhoy;
    $cambio = true;
  }
}
fclose($dolarfile);

if($cambio) {
  try {
    $pb->allDevices()->pushNote($dolarhoy, "Nuevo valor del dolar: " . $dolarhoy);
    ChromePhp::log('Push sent.');
    $dolarfile = fopen($filename, "w") or die("Error al abrir el archivo!");
    fwrite($dolarfile, $dolarhoy);
    fclose($dolarfile);
  } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
  }
}

?>
