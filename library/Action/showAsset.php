<?php
namespace Application\Controller;

function showAsset($assetLocation)
{
    if(!file_exists($assetLocation))die("fichier introuvable");
    header("HTTP/1.0 200 Ok");

    $publicFileInfo = pathinfo($assetLocation);
    switch(strtolower($publicFileInfo["extension"]))
    {
        case "css":
            header('Content-Type: text/css');
            break;
    }

    echo file_get_contents($assetLocation);
}
