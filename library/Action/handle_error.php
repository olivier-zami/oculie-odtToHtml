<?php
namespace Oculie;

function handle_error($e)
{
	echo "<fieldset><legend>Erreur non gerée</legend><pre>";
	var_dump($e);die();
	echo "<pre></fieldset>";
}
?>