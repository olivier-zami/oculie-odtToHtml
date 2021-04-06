<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 14/02/2020
 * Time: 14:10
 */

namespace Oculie;

function getFilesFromDirectory($directory)
{
    $dirFile = [realpath($directory)];
    for($i=0;$i<count($dirFile);$i++)
    {
        if(!is_dir($dirFile[$i]))continue;
        foreach(scandir($dirFile[$i]) as $fileName)
        {
            if(in_array($fileName, [".", ".."]))continue;
            $dirFile[] = realpath($dirFile[$i]."/".$fileName);
        }
    }
    scandir($directory);

    return $dirFile;
}