<?php
namespace Oculie;

class ViewManager extends \Oculie
{
	private static $view = array();

	public static function builder($className, $option=array())
	{
		if(isset($option["final"])) parent::setBuilderProcedureFinal($className, $option["final"]);
		return parent::newBuilderProcedure($className);
	}

	public static function display($order)
	{
		switch($order->receipt["status"])
		{
			case 200:
				header("HTTP/1.0 200 Ok");
				break;
		}
		if(isset($order->receipt["content-type"])) header("Content-Type: ".$order->receipt["content-type"]);

		if(!isset($order->process["viewer"]) || empty($order->process["viewer"]))
		{
			echo $order->content;
		}
		else
		{
			$view = parent::show($order->process["viewer"]);//TODO: show par viewManager ?
			//echo $order->footer["viewer"]->getResource();
		}
	}

	public static function newViewer($className=NULL)
    {
		if(is_string($className) && class_exists($className))
		{
			$class = new \ReflectionClass($className);
			$test = $class->getMethods();
			$msg = "";
			$msg .= "<fieldset><legend>"."...".$className."</legend>";
			$msg .= "<pre>".print_r($test, TRUE)."</pre>";
			$msg .= "</fieldset>";
			//echo $msg;
		}

		$viewBuilderDeclaration = new class($className)
		{
			use \DeclarativeBuilderObjectFluentInterfaceTrait;
		};

		$viewBuilderDeclaration->setBuildedClassName($className);
		self::$view[] = $viewBuilderDeclaration;
		return $viewBuilderDeclaration;
    }

	public static function registerView($name, $view)
	{
		return $view;
	}
}
?>
