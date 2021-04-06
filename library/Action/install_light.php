<?php
if(!class_exists(Oculie::class))require_once(__DIR__ . "/../../oculie.php");

define("LOCAL_DIR_DEFINITION", "C:\\Users\\ozami\\Projects\\dashboard\\src\\oculie\\Definition");
if(file_exists($file="D:\\src\\oculie\\oculie.php"))
{
	copy($file, "C:\\Users\\ozami\\Projects\\dashboard\\src\\oculie\\oculie.php");
	copy("D:\\src\\oculie\\bin\\console", "C:\\Users\\ozami\\Projects\\dashboard\\bin\\console");
	
	//!TODO: copier partiellement autoload.ini.php dans le repertoire source ...
	copy("D:\\src\\oculie\\conf\\autoload.ini.php", "C:\\Users\\ozami\\Projects\\dashboard\\conf\\autoload.ini.php");
	
	//!
	if(!file_exists($dirname = "C:\\Users\\ozami\\Projects\\dashboard\\src\\oculie\\core\\behavior\\eventManager"))mkdir($dirname, 0777, TRUE);
	copy("D:\\src\\oculie\\core\\behavior\\eventManager\\controllerMethods.php", "C:\\Users\\ozami\\Projects\\dashboard\\src\\oculie\\core\\behavior\\eventManager\\controllerMethods.php");
	
	if(!file_exists($dirname = "C:\\Users\\ozami\\Projects\\dashboard\\src\\oculie\\core\\behavior\\Model"))mkdir($dirname, 0777, TRUE);
	copy("D:\\src\\oculie\\core\\behavior\\Model\\event.php", $dirname."\\event.php");
	
	if(!file_exists($dirname = LOCAL_DIR_DEFINITION."\\dto"))mkdir($dirname, 0777, TRUE);
	copy(dirname($file)."/Definition/dto/CommandProcessor.php", LOCAL_DIR_DEFINITION."\\dto\\CommandProcessor.php");
	copy(dirname($file)."/Definition/dto/Request.php", LOCAL_DIR_DEFINITION."\\dto\\Request.php");
	copy(dirname($file)."/Definition/Action.php", LOCAL_DIR_DEFINITION."\\Action.php");
	
	//***************************************************************************************************
	
	$autoloadData = Oculie::getAsArrayConfigurationFile(__DIR__."/../../conf/autoload.ini.php");
	$remoteAutoloadData = [];
	foreach($autoloadData as $className => $classFileName)
	{
		$remoteAutoloadData[$className] = $argv[3].substr($classFileName, strlen(Oculie::getDirectory(Oculie::SRC_DIR)));
		if(!is_dir($dirname = dirname($remoteAutoloadData[$className]))) mkdir($dirname, 0777, TRUE);
		copy($classFileName, $remoteAutoloadData[$className]);
	}
	//if(!file_exists($dirname=))mkdir($dirname, 0777, TRUE);//TODO merger copier dans autoload.ini.php
	//var_dump($remoteAutoloadData);
}
else echo "\nAbsence de source USB";
?>