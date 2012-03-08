<?php
$task=$modx->xdbedit->getTask();
$getObject= dirname(dirname(__FILE__)) . '/' . $task . '/' . basename(__FILE__);
$updateerror=false;
$successmsg='';
if (file_exists($getObject)) {
    $overridden= include_once ($getObject);
    if ($overridden !== false) {
       // return;
    }
}

if ($updateerror){
	return $modx->error->failure($errormsg);	
}

return $modx->error->success($successmsg,$object);