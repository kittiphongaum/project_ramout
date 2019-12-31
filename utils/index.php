<?php
  ob_start();
  session_start();
  include 'session.php';
  require_once 'common.php';
  require_once 'lib/template.php';
  $template = new template();
  
  $template->set_filenames(array(
    'body' => 'index.html')
  );

  $data = array(
    "menu_item"=>0,
  );
  
  $template->assign_vars($data);
  $template->pparse('body');
?>