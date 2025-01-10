<?php
// api/index.php
// 共通関数などの読み込み
require_once 'functions.php';

$code = "4\/0AanRRrvIQLXXAE8RwzDnqPN9azZpgfwqLH3p657NyIsvF2h5jCE5GIi_E6ds1oHCjhU_YQ";

add_file(CODE_JSON_FILE, "code", $code);

[$codex, $fpath, $state] = file_read(CODE_JSON_FILE);
print_r($codex);

print("\n==============\n");

[$authx, $fpath, $state] = file_read(AUTH_JSON_FILE);
print_r($authx);

print("\n==============\n");

[$users, $fpath, $state] = file_read(USER_JSON_FILE);
if( !isBlank($users) ){
  $users = json_decode($users, true);
  echo $users;
  setUsers($users);
}
else{
  $users = getUsers();
}
var_dump( $users );

add_file(USER_JSON_FILE, "user", $users);
?>
