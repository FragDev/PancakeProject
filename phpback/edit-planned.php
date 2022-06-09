<?php
  include 'db.php';
  session_start();

  try{
    $stm = DB()->prepare('select * from planned_cooking where id = :id');
    $stm->execute([
      'id' => $_GET['id'],
      'user_id' => $_SESSION['user_id'],
    ]);
  } catch(PDOException $e){
    // echo "Connection failed: " . $e -> getMessage();
  }
  
  header('Location: ../plannify.php?id=' . $_GET['id']);
?>
