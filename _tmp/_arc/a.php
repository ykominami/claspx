<?php
  // require_once './common/functions.php';
  require_once './common/store.php';
  $kind = "google_public_keys";
  $currentTime = getCurrentTimeString();
  $path = __DIR__ . '/data/' . $kind . '/' . $currentTime . '/' . STORE_FILE;

  $fileitem = new FileItem($path, "file");
  if( $fileitem->doesExists() ) {
    $c = $fileitem->read();
    print_r($c);
  }
  else {
    $fileitem->write("");
  }

  $x = json_decode($c, true);
  print_r($x);

  $key2= time() + 100;
  $value2 = "DEF";

  $x[$key2] = $value2;
  print_r($x);

  $x["ghi"]="GH";
  print_r($x);

  $key3= time() + 200;
  $value3 = "PQR";

  $x[$key3] = $value3;
  print_r($x);

  $str = json_encode($x);
  echo $str;
  $fileitem->write($str);
?>
