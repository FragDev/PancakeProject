<?php
  session_start();

  unset($_SESSION['login']);
  unset($_SESSION['user_id']);
  unset($_SESSION['name']);

  header('Location: ' . $_SERVER['HTTP_REFERER']);
?>
