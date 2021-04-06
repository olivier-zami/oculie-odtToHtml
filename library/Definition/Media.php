<?php
namespace Oculie\Core\Definition;

class Media
{
    const TYPE = [ //TODO: check https://www.iana.org/assignments/media-types/media-types.xhtml#image
        "text/javascript"   => 1,
        "image/bmp"         => 10,
        "image/gif"         => 11,//TODO: verifier l'existence
        "image/jpeg"        => 12,//TODO: verifier l'existence
        "image/png"         => 13,
        "image/tiff"        => 14,
    ];
}