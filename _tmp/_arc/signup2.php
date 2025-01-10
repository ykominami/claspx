<?php
  header("Content-Type: application/json");

  session_start();

  $newUser = ['name' => 'User1'];
  http_response_code(201); // Created
  echo json_encode($newUser);
?>