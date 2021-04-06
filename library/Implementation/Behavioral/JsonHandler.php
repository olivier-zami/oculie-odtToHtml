<?php
namespace Oculie;

class JsonHandler
{
	private $jsonVar = array();
	private $input = array();
	private $data;
    private $jsonHandlerData = array(
        "metaVars"  => array(),
        "vars"       => array()
    );

	public function getData()
	{
		$this->prepare();

		$idx = 0;
		$stack = array();
		$stack[] = &$this->data;

		while(isset($stack[$idx]))
		{
			if(is_array($stack[$idx]))
			{
				foreach($stack[$idx] as $name => $value)
				{
					//echo "<fieldset><legend>".$name.":".gettype($value)."</legend>";
					//echo "<br/>A=>";
					$tmp = preg_replace_callback("|(\\\$[a-zA-Z_]+)(\[[a-zA-Z]+\])|", array($this, "getExpressionWithVariableValue"), $name);
					if($tmp!=$name)
					{
						$stack[$idx][$tmp] = $value;
						unset($stack[$idx][$name]);
						$name = $tmp;
					}
					if(is_array($value) || is_object($value)) $stack[] = &$stack[$idx][$name];
					else
					{
						$tmp = preg_replace_callback("|(\\\$[a-zA-Z_]+)(\[[a-zA-Z]+\])|", array($this, "getExpressionWithVariableValue"), $value);
						if($tmp!=$value)
						{
							$stack[$idx][$name] = $tmp;
						}
					}
					//echo "</fieldset>";
				}

			}
			elseif(is_object($stack[$idx]))
			{
				$object_vars = get_object_vars($stack[$idx]);
				foreach($object_vars as $name => $value)
				{
					//echo "<fieldset><legend>".$name.":".gettype($value)."</legend>";
					//echo "<br/>O=>".$name;
					$tmp = preg_replace_callback("|(\\\$[a-zA-Z_]+)(\[[a-zA-Z]+\])|", array($this, "getExpressionWithVariableValue"), $name);
					if($tmp!=$name)
					{
						$stack[$idx]->{$tmp} = $value;
						unset($stack[$idx]->{$name});
						$name = $tmp;
					}
					//echo "=============>";var_dump($tmp, $name);
					if(is_array($value) || is_object($value)) $stack[] = &$stack[$idx]->{$name};
					else
					{
						//echo "<fieldset><p>name:".$name."    :  ".$value."</p>";
						//preg_match("|(\\\$[a-zA-Z_]+)(\[[a-zA-Z_-]+])|", $value, $match);
						//var_dump($match);
						$tmp = preg_replace_callback("|(\\\$[a-zA-Z_]+)(\[[a-zA-Z_-]+])|", array($this, "getExpressionWithVariableValue"), $value);
						if($tmp!=$value)
						{
							$stack[$idx]->{$name} = $tmp;
						}
						//echo "</fieldset>";
					}
					//echo "</fieldset>";
				}
			}
			$idx++;
		}
		//echo "<hr/>";

		//echo "<fieldset><legend>debug</legend>";var_dump($this->var, $this->jsonVar);echo"<hr/>";var_dump($this->data);echo"</fieldset>";
		return $this->data;
	}

	public function get($varName)
	{
		return $this->var[$varName];
	}

    public function handle($value)
    {
        $this->data = json_decode($value);
    }

	public function set($varName, $value)
	{
		$this->input[$varName] = $value;
	}

	private function getExpressionWithVariableValue($match)
	{
		//echo"=>";var_dump($match, $this->input, $this->jsonVar);
		return $this->jsonVar[substr($match[1], 1)][substr($match[2], 1, -1)];
	}

	private function prepare()
	{
		//echo "<fieldset><legend>".__METHOD__."</legend>";var_dump($this->data);echo "</fieldset>";
		$this->jsonVar = json_decode(json_encode($this->data->{"\$"}), TRUE);
        unset($this->data->{"\$"});
		$stack = array();
		$stack[] = &$this->jsonVar;
		$idx = 0;
		while(isset($stack[$idx]))
		{
			foreach($stack[$idx] as $name =>$value)
			{
				if(is_array($value)){$stack[] = &$stack[$idx][$name]; break;}
				if(preg_match("|\\\$::([a-zA-Z_]+)|", $value, $match) && !empty($match[1]))
				{
					if(isset($this->input[$match[1]])) $stack[$idx][$name] = $this->input[$match[1]];
				}
			}
			$idx++;
		}
	}
}
?>
