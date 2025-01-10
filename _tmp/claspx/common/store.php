<?php
  require "./common/functions.php";  // common/function.php
  require "./common/fileitem.php";  // common/fileitem.php
  require "./common/jwtrelated.php";  // common/jwtrelated.php
  require "./common/google_public_key.php";  // common/google_public_key.php
  require 'vendor/autoload.php'; // Add this line to include Composer autoload

  use Dotenv\Dotenv; // Add this line to use Dotenv

  class Store{
    public $db = null;
    public $db_path = null;
    public $db_size = 0;

    public function __construct($db = null){
      if($db == null){
        $this->db = [];
      }
    }
    private function getJwtFromGoogle(){
      $gpc = new GooglePublicKey();
      $content = $gpc->get_google_public_key_str();

      return $content;
    }
    public function getAndSaveGoolePublicKey($jwtrelated){
      $gpc = new GooglePublicKey();
      $content = $gpc->get_google_public_key_str();

      $config_fileitem = $jwtrelated->jwt_root_dir_fileitem->addFile(STORE_FILE);
      $currentTime = getCurrentTimeString();
      $dir_item = $jwtrelated->jwt_root_dir_fileitem->addDirectory($currentTime, 0777,  GPK_CACHE_DIR);
      $jwtrelated->saveJwtConfig($currentTime);
      $jwtrelated->setJwtConfigFileitem($config_fileitem);
      $jwtrelated->setJwtDirFileItem($dir_item);
      $dir_item->addFile(JWT_FILE)->write($content);
      $jwtrelated->setJwtFileitem($dir_item->addFile(JWT_FILE));

      return $content;
    }
    private function doesExistsValidJwtStoreDirectory($db_item){
      $jwtRelated = new JWTRelated();
      if( $db_item === null ){
        throw new Exception("GPK_ROOT_DIR level == null");;
      }
      if( $db_item->level !== GPK_ROOT_DIR ){
        return $jwtRelated;
      }
      $jwtRelated->setJwtRootDirFileitem($db_item);

      $config_fileitem = $jwtRelated->jwt_root_dir_fileitem->addFile(STORE_FILE);
      $jwtRelated->setJwtConfigFileitem($config_fileitem);

      if( strlen( $jwtRelated->getJwtContent() ) === 0){
        return $jwtRelated;
      }
      $dir_item = $jwtRelated->jwt_root_dir_fileitem->addDirectory($trimed_string, 0777, GPK_CACHE_DIR);
      if( $dir_item->doesNotExists() ){
        return $jwtRelated;
      }
      $jwtRelated->setJwtDirFileItem($dir_item);
      if( $dir_item->doesNotExists() ){
        return $jwtRelated;
      }
      $jwt_fileitem = $dir_item->addFile(JWT_FILE);
      $jwtRelated->setJwtFileitem($jwt_fileitem);

      $content2 = $jwt_fileitem->read();
      $trimed_string2 = trim($content2, " \t\n\r\0\x0B."); // トリミング処理を強化
      if( strlen($trimed_string2) === 0){
        return $jwtRelated;
      }
      $content3 = $jwtRelated->getJwtContent();
      $trimed_string3 = trim($content3, " \t\n\r\0\x0B."); // トリミング処理を強化
      if( strlen($trimed_string3) === 0){
        return $jwtRelated;
      }
      $jwtRelated->setJwtFileItem($jwt_fileitem);
      $jwtRelated->setState(true);
      return $jwtRelated;
    }

    public function getAndStoreJwtStoreDirectory($db_item){
      $dir_name = getCurrentTimeString();
      $sub_dir = $db_item->addDirectory($dir_name, 0777, GPK_CACHE_DIR);
      $jwt_fileitem = $sub_dir->addFile(JWT_FILE);
      $content = $this->getJwtFromGoogle();
      $jwt_fileitem->write($content);

      return [$jwt_fileitem, $content];
    }

    private function get_file( $kind ) {
      $db_dir_item = $this->get_db_dir_item();
      if( $db_dir_item->doesNotExists() ){
        $db_dir_item->mkdir(0777, true);
      }
      $fileitem = $db_dir_item->addFile(STORE_FILE);
      if( $fileitem->doesNotExists() ){
        $fileitem->write("");
      }
      return $fileitem;
    }

    public function get_db_dir_item($kind) {
      $dir = __DIR__ . '/../data/' . $kind;
      $db_dir_item = new FileItem($dir, "dir");
      return $db_dir_item;
    }
    public function load($kind) {
      $fileitem = null;
      $content = null;
      $jwtrelated = null;
      if ($kind == "google_public_keys") {
        $db_dir_item = $this->get_db_dir_item($kind);
        $db_dir_item->level = GPK_ROOT_DIR;
        // print_r($db_dir_item);

        $jwtrelated = $this->doesExistsValidJwtStoreDirectory($db_dir_item);
        // print_r($jwtrelated);
      }
      else{
        $fileitem = $this->get_file($kind);
        $content = $fileitem->read();
      }
      return [$fileitem, $content, $jwtrelated];
    }

    public function list($kind){
      return $this->load($kind);
    }
    public function save($kind, $data){
      $fileitem = $this->get_file($kind);
      // [$fpath, $file_exist, $size] = $this->get_file($kind);
      // echo "size=$fileitem->size, fpath: $fileitem->path\n";
      $fileitem->write(json_encode($data));
    }
  }
?>
