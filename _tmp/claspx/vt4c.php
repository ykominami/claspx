<?php
  // verify-token.php
  require 'common/store.php';
  require 'vendor/autoload.php';

  use Firebase\JWT\JWK;
  use Firebase\JWT\JWT;
  use Firebase\JWT\Key;
  use Google\Auth\OAuth2;
  use Dotenv\Dotenv; // Add this line to use Dotenv

  function getBaseDir()
  {
    return __DIR__;
  }
  function getAbsolutePath($path)
  {
    return getBaseDir() . '/' . $path;
  }

  // Load .env file
  $dotenv = Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  // Googleの公開鍵を取得
  $kind = "google_public_keys";

  $store = new Store();
  [$fileitem, $content, $jwtrelated] = $store->load($kind);
   // print_r($jwtrelated);

  $content = "";

?>