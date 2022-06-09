<?php
include 'db.php';
session_start();

$size = array('SMALL', 'MEDIUM', 'LARGE')[$_POST['size'] - 1];
$datetime = substr($_POST['time'], 0, 5) . ':00 ' . substr($_POST['time'], -2, 2) . ' ' . $_POST['date'];

try {
  if (!isset($_POST['cooking_id']) || $_POST['cooking_id'] == "") {
    $stm = DB()->prepare('insert into `planned_cooking`(`user_id`, `quantity`, `size`, `prepare_datetime`) VALUES (:user_id, :quantity, :size, STR_TO_DATE(:datetime, "%r %e/%c/%Y"))'); 
    $stm->execute([
      'user_id' => $_SESSION['user_id'],
      'quantity' => $_POST['quantity'],
      'size' => $size,
      'datetime' => $datetime,
    ]);
  } else {
    $stm = DB()->prepare('update `planned_cooking` set `user_id` = :user_id, `quantity` = :quantity, `size` = :size, `prepare_datetime` = STR_TO_DATE(:datetime, "%r %e/%c/%Y") where `id` = :id and `user_id` = :user_id');
    $stm->execute([
      'id' => $_POST['cooking_id'],
      'user_id' => $_SESSION['user_id'],
      'quantity' => $_POST['quantity'],
      'size' => $size,
      'datetime' => $datetime,
    ]);
  }
} catch (PDOException $e) {
  echo "Connection failed: " . $e -> getMessage();
}

header('Location: ../index.php');
