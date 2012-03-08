<?php
/**
 * xdbedit
 *
 * @author Bruno Perner
 *
 *
 * @package xdbedit
 */
/**
 * @package xdbedit
 * @subpackage controllers
 */
require_once dirname(dirname(__FILE__)).'/model/xdbedit/xdbedit.class.php';
$xdbedit = new Xdbedit($modx);

$modx->xdbedit=& $xdbedit;

return $xdbedit->initialize('mgr');