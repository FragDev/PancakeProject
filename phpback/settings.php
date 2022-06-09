<?php
  setcookie('theme', isset($_POST['theme']) && $_POST['theme'] == 'on' ? 'dark' : 'light', array('path' => '/'));
  header('Location: ' . $_SERVER['HTTP_REFERER']);
?>
