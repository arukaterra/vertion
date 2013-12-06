<?php 


// if yu want to add authorize class.. please add in this frame -----------------------
// it can be use for auto load module
require ROOT_PATH.APPLICATION_PATH.'__constructor/theConstructor.php'; // class construct auto loader
require ROOT_PATH.APPLICATION_PATH.HELPER_PATH.'authorize.php';  // class auth




//--------------------------------------------------------------------------------------

############################ Core Control###############################

// require 'application/modules/view/'.$adsBlockController->controller().'.php';
require ROOT_PATH.SYSTEM_PATH.'core/common.php'; // executor
#################################################################

?>