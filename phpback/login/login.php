<?php
  include '../db.php';
  session_start();

  try{
    $stm = DB()->prepare('select * from user where mail = :mail and password = :password');
    $stm->execute([
      'mail' => $_POST['mail'],
      'password' => hash('sha512', $_POST['password']),
    ]);
    $rows = $stm->fetchAll();

    if (count($rows) == 1) {
      $_SESSION['login'] = 'success';
      $_SESSION['user_id'] = $rows[0]['id'];
      $_SESSION['name'] = $rows[0]['name'];
    } else {
      $_SESSION['login'] = 'fail';
    }
  } catch(PDOException $e){
    // echo "Connection failed: " . $e -> getMessage();
  }

  header('Location: ' . $_SERVER['HTTP_REFERER']);
?>
