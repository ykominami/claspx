<?php
  require_once 'functions.php';

  class JWTRelated {
    public $state;
    public $jwt_fileitem;
    public $jwt_content;
    public $jwt_root_dir_fileitem;
    public $jwt_dir_fileitem;
    public $jwt_config_fileitem;
    public $jwt_config_content;

    public function __construct() {
      $this->state = false;
      $this->jwt_fileitem = null;
      $this->jwt_content = null;
      $this->jwt_root_dir_fileitem = null;
      $this->jwt_dir_fileitem = null;
      $this->jwt_config_fileitem = null;
      $this->jwt_config_content = null;
    }
    public function setJwtFileitem($jwtFileitem) {
      $this->jwt_fileitem = $jwtFileitem;
    }

    private function isValidJwtConfig($content){
      $fullPath = $this->jwt_root_dir_fileitem->path . '/' . $content;
      if( !file_exists($fullPath) ){
        return false;
      }

      return true;
    }

    public function setJwtConfigFileitem($jwtConfigFileItem) {
      $content = $jwtConfigFileItem->read();
      assert( $this->isValidJwtConfig($content) );

      $this->jwt_config_fileitem = $jwtConfigFileItem;
    }
    public function setState($state) {
      $this->state = $state;
    }
    public function setJwtRootDirFileitem($jwtRootDirFileItem) {
      $basename = $jwtRootDirFileItem->basename();
      assert( isNumericString($basename) !== 1 );
      $this->jwt_root_dir_fileitem = $jwtRootDirFileItem;
      $this->jwt_root_dir_fileitem->level = GPK_ROOT_DIR;
    }
    public function setJwtDirFileitem($jwtDirFileItem) {
      $this->jwt_dir_fileitem = $jwtDirFileItem;
      $this->jwt_dir_fileitem->level = GPK_CACHE_DIR;
    }
    public function saveJwtConfig($content) {
      assert( isValidJwtConfig($content) );
      $this->jwt_config_content = $content;
      $this->jwt_config_fileitem->write($content);
    }
    public function getJwtContent() {
      if( $this->jwt_content === null){
        if( $this->jwt_fileitem === null ){
          return "";
        }
        $this->jwt_content = $this->jwt_fileitem->read();
        // print_r($this->jwt_fileitem);
        print "jwt_content: " . strlen($this->jwt_content) . "\n";
      };
      return $this->jwt_content;
    }

  }

?>