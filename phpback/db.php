<?php
function DB()
{
  try {
    $db = new PDO("mysql:host=" . $_ENV['pan_servername'] . ";port=" . $_ENV['pan_port'] . " ;dbname=" . $_ENV['pan_dbname'], $_ENV['pan_username'], $_ENV['pan_password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $db;
  } catch (PDOException $e) {
    // echo "Connection failed: " . $e -> getMessage();
  }

  return NULL;
}
