<?php
  include 'db.php';
  session_start();

  try{
    $stm = DB()->prepare('delete from planned_cooking where id = :id and user_id = :user_id limit 1');
    $stm->execute([
      'id' => $_GET['id'],
      'user_id' => $_SESSION['user_id'],
    ]);
  } catch(PDOException $e){
    // echo "Connection failed: " . $e -> getMessage();
  }
  
  header('Location: ' . $_SERVER['HTTP_REFERER']);
?>
