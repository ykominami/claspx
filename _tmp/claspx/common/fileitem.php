<?php

  class FileItem{
    public $path;
    public $isFile;
    public $size;
    public $itemExists;
    public $level;

    public function __construct($path, $kind, $level = null){
      $this->level = $level;

      $path2 = null;
      $path3 = null;
      // 全ての条件が満たされているかどうかを示すフラグ
      $this->valid =  "UNDEF";

      $this->size = 0;
      $this->itemExists = false;

      // echo "path: $path\n";
      // echo "kind: $kind\n";
      // echo "__construct path: $path\n";
      if( $path ){
        $path2 = $path;
        if( $kind == "file" ){
          $path2 = dirname($path);
          // echo "__construct path2: $path2\n";
        }
        // echo "Fileitem __const path2: $path2\n";

        $path3 = $this->ensureAbsoluteDirectory($path2);
        // echo "__construct path3 A: $path3\n";
        if( $kind == "file" ){
          $path3 = $path3 . '/' . basename($path);
          // echo "__construct path3 B: $path3\n";
          try{
            touch($path3);
            $this->valid = "VALID";
          }
          catch(Exception $e){
            echo "An error occurred because the condition was true.";
          }
        }
        else{
          $this->valid = "VALID";
        }
      }

      $this->path = $path3;
      // echo "__construct this->path: $this->path\n";

      if( file_exists($this->path) ){
        $this->itemExists = true;
        if( is_file($this->path) ){
          $this->isFile = true;
          $this->size = filesize($this->path);
        }
        else {
          $this->isFile = false;
        }
        // ファイルシステム上の判定(ファイルであるかディレクトリであるか)と引数での指定が一致しているれがvalidフラグを立てる
        if( $kind == "file") {
          if( $this->isFile ){
            $this->valid = "VALID";
          }
          else{
            $this->valid = "INVALID";
          }
        }
        else {
          if( !$this->isFile ){
            $this->valid = "VALID";
          }
          else{
            $this->valid = "INVALID";
          }
        }
      }
      else{
        $this->itemExists = false;
      }
    }
    public function ensureAbsoluteDirectory($path2){
      echo "ensureAbsoluteDirectory path2: $path2\n";
      ensureDirectoryExists($path2);
      $path3 = convertToAbsolutePath($path2);
      echo "ensureAbsoluteDirectory path3: $path3\n";
      $path4 = rtrim($path3, "/");
      echo "ensureAbsoluteDirectory path4: $path4\n";
      return $path4;
    }

    public function basename(){
      return basename($this->path);
    }
    public function doesExists(){
      $this->itemExists = file_exists($this->path);
      return $this->itemExists;
    }
    public function doesNotExists(){ // 修正: メソッド名を統一
      return !$this->doesExists();
    }
    public function isFile(){
      return $this->isFile;
    }
    public function isDirectory(){
      return !$this->isFile;
    }
    public function mkdir($permission, $recursive) {
      $ret = false;
      if( $this->isDirectory() && $this->doesNotExists() ){ // 修正: メソッド名を統一
        echo $this->path . "|||\n\n";

        if( $this->doesNotExists() ){
          $ret = mkdir($this->path, $permission, $recursive);
          if( $ret ){
            $this->itemExists = true;
          }
          else{
            echo "Failed to create directory: $this->path";
          }
        }
      }
      return $ret;
    }
    public function rmdir($recursive = false) {
      if( $recursive ){
        $ret = $this->removeDirectoryRecursive($this->path);
      }
      else{
        $ret = $this->removeDirectory($this->path);
      }
      if( $ret ){
        $this->itemExists = false;
      }
      return $ret;
    }
    public function addDirectory($dir, $permission = 0777, $level = null) {
      echo "Fileitem addDirectory dir: $dir | this->path=$this->path\n";
      $newFileItem = null;
      assert( $level != null, "level is null");
      assert( $this->level != null, "$this->level is null");
      assert( $this->level + 1 == $level, "level error");

      if( $this->isDirectory() && $this->doesExists() ){
        $newDirectory = $this->path . "/" . $dir;
        $newFileItem = new FileItem($newDirectory, "dir", $level);
        // $newFileItem->level = $level;
        if( $newFileItem->doesNotExists() ){ // 修正: メソッド名を統一
          $newFileItem->mkdir($permission, false);
        }
      }

      return $newFileItem;
    }

    public function addFile($filename) {
      $newFileItem = null;
      if( $this->isDirectory() ){
        $newFileItem = new FileItem($this->path . '/' . $filename , "file");
      }

      return $newFileItem;
    }
    /**
     * 指定ディレクトリ以下を再帰的に削除する関数
     *
     * @param string $dir 削除するディレクトリへのパス
     * @return bool 成功した場合 true、失敗した場合 false
     */
    function removeDirectoryRecursive(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectoryRecursive($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
    }
    public function removeDirectory($dir) {
      if (!is_dir($dir)) {
        return false;
      }
      $ret = unlink($dir);
      return $ret;
    }

    public function write($content) {
      $ret = false;
      if( $this->isFile() && $this->doesExists() ){
        $ret = file_write($this->path, $content);
        if( $ret ){
          $this->size = filesize($this->path);
          $this->itemExists = true;
        }
      }
      return $ret;
    }
    public function read() {
      $c = "";
      if( $this->isFile() ){
        if( file_exists($this->path) ){
          $size = filesize($this->path);
          $file = fopen($this->path, "r");
          if($size > 0){
            $c = fread($file, $size);
            // print_r(["name" => "file_read", "size" => $size, "c" => $c, "cs" => strlen($c), "path" => $this->path]);
          }
          fclose($file);
        }
      }
      return $c;
    }

  }
?>