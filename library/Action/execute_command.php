<?php
namespace Oculie;

function execute_command($command)
{
	$processClass = \Oculie::dataDefinition($command)->getDataProcess();
	$process = new $processClass($command);
	$process->start();
}
?>