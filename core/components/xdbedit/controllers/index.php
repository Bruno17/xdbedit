<?php
/**
 * stellenboerse
 *
 * @author Bruno Perner
 *
 *
 * @package stellenboerse
 */
/**
 * @package stellenboerse
 * @subpackage controllers
 */
require_once dirname(dirname(__FILE__)).'/model/xdbedit/xdbedit.class.php';
$xdbedit = new Xdbedit($modx);
return $xdbedit->initialize('mgr');