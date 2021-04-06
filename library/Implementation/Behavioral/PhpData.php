<?php
namespace Oculie\Core\Handler;

class PhpData
{
    private $source;
    //TODO: unifier l'Behavior avec xmlHa...

    public function setResource($source)//must be a resource Pattern
    {
        $this->source = $source;
    }

    public function getResource()
    {
        return new class ($this->source)//return Resource + trait::asCode()
        {
            private $content;

            public function __construct($resourceContent)
            {
                $this->content = $resourceContent;
            }

            public function asCode()
            {
                $srcCode = trim($this->content);
                $srcCode = str_replace("<?php", "", $srcCode);
                $srcCode = str_replace("?>", "", $srcCode);
                $cfg_array = eval("return ".$srcCode.";");

                $this->source;
            }
        };
    }
}
?>