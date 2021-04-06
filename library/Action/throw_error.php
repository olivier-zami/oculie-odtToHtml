<?php
namespace Oculie;

function throw_error($errno, $errstr, $errfile, $errline)
{
	//if (!(error_reporting() & $errno)) return;
	
	switch ($errno) {
    case E_USER_ERROR:
        echo "<b>Mon ERREUR</b> [$errno] $errstr<br />\n";
        echo "  Erreur fatale sur la ligne $errline dans le fichier $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Arrêt...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<b>Mon ALERTE</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        echo "<b>Mon AVERTISSEMENT</b> [$errno] $errstr<br />\n";
        break;

    default:
        echo "Type d'erreur inconnu : [$errno] $errstr<br />\n";
        break;
    }
	
	//return TRUE;
	
	throw new ErrorException("TODO : ErrorException....... ".__FILE__." @ line ".__LINE__);
}
?>