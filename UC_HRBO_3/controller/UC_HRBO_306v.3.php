<?php
  ob_start();
  session_start();
  require '../../session.php';
  require_once '../../common.php';
  require_once '../../lib/template.php';

  $template = new template();
  $template->set_filenames(array(
    'body' => '../view/UC_HRBO_306v.3.html')
  );

  $data = array(
    "menu_item"=>3,
  );
  
  $template->assign_vars($data);
  $template->pparse('body');
?>