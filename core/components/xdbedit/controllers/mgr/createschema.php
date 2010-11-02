<?php

$form='
<form method="post">
packageName:<input type="text" name="packageName"/><br/>
prefix:<input type="text" name="prefix"/><br/>
tables(optional):<input type="text" name="tableList"/><br/>
<input type="submit" name="create">
</form>
';

if (isset($_POST['create'])){

$prefix=$_POST['prefix'];
$packageName = $_POST['packageName'];
//$tablename = $scriptProperties['tablename'];
$tableList = isset($_POST['tableList']) && !empty($_POST['tableList'])?$_POST['tableList']:null; 
//$tableList = array(array('table1'=>'classname1'),array('table2'=>'className2'));
$restrictPrefix = true;

$packagepath = $modx->getOption('core_path') . 'components/'.$packageName.'/';
$modelpath = $packagepath.'model/';
$schemapath = $modelpath.'schema/';
$schemafile = $schemapath.$packageName.'.mysql.schema.xml';


if (!file_exists($schemafile)){
    $manager= $modx->getManager();
    $generator= $manager->getGenerator();

    if (!is_dir($packagepath)) {
        mkdir($packagepath, 0777);
    }
    if (!is_dir($modelpath)) {
        mkdir($modelpath, 0777);
    }
    if (!is_dir($schemapath)) {
        mkdir($schemapath, 0777);
    }
    //Use this to create a schema from an existing database
    $xml= $generator->writeSchema($schemafile, $packageName, 'xPDOObject', $prefix, $restrictPrefix, $tableList);

    //Use this to generate classes and maps from your schema
    // NOTE: by default, only maps are overwritten; delete class files if you want to regenerate classes
    $generator->parseSchema($schemafile, $modelpath);    
} 

//$modx->addPackage($packageName,$modelpath,$prefix);
//$classname = strtoupper(substr($tablename, 0, 1)) . substr($tablename, 1) . '';

/*

    if ($dataobject=$modx->getObject($classname,array('id'=>$_GET['resId']))){
        $hook->setValues($dataobject->toArray());
  

    //$errorMsg = '<pre>'.print_r($dataobject->toArray(),true).'</pre>';  
    //$hook->addError('error_message',$errorMsg);  
}	
*/  	
}
return $form;