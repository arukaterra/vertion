<?php
######################Core Loader#################################
require ROOT_PATH.SYSTEM_PATH.'core/db.php'; //class DB 
require ROOT_PATH.SYSTEM_PATH.'core/loader.php'; // class Loader
require ROOT_PATH.SYSTEM_PATH.'core/controller.php'; // class controll automate system to detect url path
require ROOT_PATH.SYSTEM_PATH.'core/session_controller.php'; // class session  
#############################################################
  
$Application= new Application; 
$Application->initial();
?>