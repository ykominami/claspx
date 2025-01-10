<?php
// api/index.php
// 共通関数などの読み込み
require_once 'functions.php';

[$users, $fpath, $state] = file_read(USER_JSON_FILE);
if( !isBlank($users) ){
  $users = json_decode($users, true);
  setUsers($users);
}
else{
  $users = getUsers();
}
var_dump($users);
$ret = login("Akira", "password");
echo $ret;

/*
$newUser = make_username("Akira");

var_dump( $users );

add_file(USER_JSON_FILE, "user", $users);
*/
?>
