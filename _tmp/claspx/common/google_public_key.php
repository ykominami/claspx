<?php
// common/google_public_key.php
/*
function get_google_public_key_list(){
    $url = 'https://www.googleapis.com/oauth2/v3/certs'; // 取得したいURLを指定
    $content = file_get_contents($url); 
    return json_decode($content, true);
  }

  function get_google_public_key($key){
    $google_public_key_list = get_google_public_key_list();
    $content = $google_public_key_list[$key];
    return $content;
  }
*/
  class GooglePublicKey {
    private $google_public_key_list = null;
    private $google_public_key = null;
    private $google_public_key_str = null;

    public function __construct(){
      $this->google_public_key_list = $this->get_google_public_key_list();
    }

    public function get_google_public_key_str(){
      $url = $_ENV['GOOGLE_PUBLIC_KEY_URL']; // Use environment variable
      ; // 取得したいURLを指定
      $google_public_key_str = file_get_contents($url);
      return $google_public_key_str;
    }
    public function get_google_public_key_list(){
      $url = $_ENV['GOOGLE_PUBLIC_KEY_URL']; // Use environment variable
      $google_public_key_str = file_get_contents($url); 
      return json_decode($google_public_key_str, true);
    }


    public function get_google_public_key($key){
      $google_public_key_list = $this->google_public_key_list;
      $content = $google_public_key_list[$key];
      return $content;
    }
  }
?>
