<?php
/**
 * XdbEdit
 *
 * Copyright 2010 by Bruno Perner <b.perner@gmx.de>
 *
 * This file is part of XdbEdit, for editing custom-tables in MODx Revolution CMP.
 *
 * XdbEdit is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * XdbEdit is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Quip; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA 
 *
 * @package xdbedit
 */
/**
 * loads the header
 * 
 * @package xdbedit
 * @subpackage controllers
 */
$modx->regClientCSS($xdbedit->config['cssUrl'].'mgr.css');
$modx->regClientStartupScript($xdbedit->config['jsUrl'].'xdbedit.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    Xdbedit.config = '.$modx->toJSON($xdbedit->config).';
	Xdbedit.customconfigs = '.$modx->toJSON($xdbedit->customconfigs).';
    Xdbedit.config.connector_url = "'.$xdbedit->config['connectorUrl'].'";
    Xdbedit.request = '.$modx->toJSON($_GET).';
	
});
</script>');


return '';