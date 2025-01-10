<?php
  // require_once './common/functions.php';
  require_once './common/store.php';
  $kind = "google_public_keys";

  $store = new Store($kind);
  [$fileitem, $content] = $store->load($kind);
  print_r($content);

?>
