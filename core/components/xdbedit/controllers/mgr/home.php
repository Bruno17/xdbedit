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
 * loads the editor-page
 * 
 * @package xdbedit
 * @subpackage controllers
 */


$modx->regClientStartupScript($modx->getOption('manager_url').'assets/modext/util/datetime.js');
$modx->regClientStartupScript($modx->getOption('manager_url').'assets/modext/widgets/element/modx.panel.tv.renders.js');
//$modx->regClientStartupScript($context->getOption('manager_url').'assets/modext/widgets/resource/modx.grid.resource.security.js');
//$modx->regClientStartupScript($xdbedit->config['jsUrl'].'widgets/modx.panel.resource.js');
//$modx->regClientStartupScript($context->getOption('manager_url').'assets/modext/widgets/resource/modx.panel.resource.tv.js');
//$modx->regClientStartupScript($context->getOption('manager_url').'assets/modext/sections/resource/create.js');


//$modx->regClientStartupScript($xdbedit->config['jsUrl'].'widgets/comments.grid.js');
$modx->regClientStartupScript($xdbedit->config['jsUrl'].'widgets/grids/'.$xdbedit->getTask().'.grid.js');
$modx->regClientStartupScript($xdbedit->config['jsUrl'].'widgets/xdbedit.panel.js');
$modx->regClientStartupScript($xdbedit->config['jsUrl'].'sections/home.js');


$useEditor = $modx->getOption('use_editor',null,false);
$whichEditor = $modx->getOption('which_editor',null,'');

$plugin=$modx->getObject('modPlugin',array('name'=>$whichEditor));


/* OnRichTextEditorInit */
        if ($useEditor && $whichEditor == 'TinyMCE') {
            $tinyproperties=$plugin->getProperties();
            $tinyUrl=$xdbedit->config['jsUrl'].'tinymce/';
            require_once $xdbedit->config['modelPath'].'tinymce/tinymce.class.php';            
			$tiny = new TinyMCE($modx,$tinyproperties,$tinyUrl);
			if (isset($forfrontend) || $modx->isFrontend()) {
                $def = $modx->getOption('cultureKey',null,$modx->getOption('manager_language',null,'en'));
                $tiny->properties['language'] = $modx->getOption('fe_editor_lang',array(),$def);
                $tiny->properties['frontend'] = true;
                unset($def);
            }
            /* commenting these out as it causes problems with richtext tvs */
            //if (isset($scriptProperties['resource']) && !$resource->get('richtext')) return;
            //if (!isset($scriptProperties['resource']) && !$modx->getOption('richtext_default',null,false)) return;
            $tiny->setProperties($tinyproperties);
            $html = $tiny->initialize();
		   
            //$modx->event->output($html);
            //unset($html);
        }
/* OnRichTextBrowserInit */
if ($useEditor && $whichEditor == 'TinyMCE') {
    //$modx->regClientStartupScript($tiny->config['assetsUrl'].'jscripts/tiny_mce/tiny_mce_popup.js');
    $modx->regClientStartupScript($tiny->config['assetsUrl'].'jscripts/tiny_mce/langs/'.$tiny->properties['language'].'.js');
    $modx->regClientStartupScript($tiny->config['assetsUrl'].'tiny.browser.js');
    //$modx->event->output('Tiny.browserCallback');
}
/*
$js="
        console.log('test');
        var els = Ext.query('.modx-richtext');
        Ext.each(els,function(el,i) {
            el = Ext.get(el);
			MODx.loadRTE(el.dom.id);
            //tinyMCE.execCommand('mceAddControl', false, el.dom.id);
        },this);	
";
*/
/*
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {

//MODx.loadRTE();	
'.$js.'
	
});
</script>');
*/
$output = '
<div id="xdbedit-panel-object-div"></div>
<div id="xdbedit-panel-objects-div"></div>
';

return $output;
