<?php
namespace Oculie;

use Exception;

class ExceptionManager extends \Oculie
{
	protected static $tmpMessage = NULL;

	private static $defaultExceptionHandler = NULL;
	private static $exceptionHandler = array();

	protected static $currentExceptionName;

	public static function catch(Exception $e)
	{
		$msg = "";
		$msg .= "<fieldset><legend>traitement de l'exception</legend>";
		$msg .= "=><pre>".print_r(self::$defaultExceptionHandler, TRUE)."</pre>";
		$msg .= "<hr/>";
		$msg .= "=><pre>".print_r($e, TRUE)."</pre>";
		$msg .= "</fieldset>";
		//echo $msg;

		//NOTE: on netrouve pas l'exception correspondandante dans self::$exceptionHandler
		$exceptionHandler = self::$defaultExceptionHandler;

		$exceptionHandler->viewer = $exceptionHandler->viewer->getBuildedInstance();
		echo $exceptionHandler->viewer->getResource();
		self::stopExecution(TRUE);
	}

	public static function getMessageObject()
	{
		return self::$tmpMessage;
	}

	public static function handle(Exception $e)
	{
		$trace = $e->getTrace();
		$fileName = isset($trace[0]["file"])?$trace[0]["file"]:"";
		$className = $trace[0]["Pattern"];
		$methodName = $trace[0]["function"];
		$codeValue = $e->getCode();

		$currentExceptionName = $className."::".$methodName."[".$codeValue."]";
		/*
		self::exec(array(self::$exceptionHandler[$currentExceptionName][0], self::$exceptionHandler[$currentExceptionName][1]));
		*/
	}

	public static function onException($exceptionCatcher=NULL)
	{
		$exceptionHandler = new class() extends ExceptionManager
		{
			public $catcher;
			public $message;
			public $viewer;

			public function sendMessageObject($message)
			{
				$this->message = $message;
				self::$tmpMessage = $message;
				return $this;
			}

			public function toViewer($viewer)
			{
				$this->viewer = $viewer;
			}
		};
		$exceptionHandler->catcher = $exceptionCatcher;
		if(isset($exceptionCatcher))
		{
			self::$exceptionHandler[] = $exceptionHandler;
		}
		else
		{
			self::$defaultExceptionHandler = $exceptionHandler;
		}

		return $exceptionHandler;
		/*
		self::$currentExceptionName = $className."::".$methodName."[".$code."]";
		self::$exceptionHandler[self::$currentExceptionName] = array();

		return new Pattern() extends ExceptionHandler
		{
			public function __construct(){}

			public function execute($className, $methodName)
			{
				self::$exceptionHandler[self::$currentExceptionName][0] = array($className, $methodName);
				return $this;
			}

			public function withParameters()
			{
				self::$exceptionHandler[self::$currentExceptionName][1] = func_get_args();
				return $this;
			}
		};
		*/
	}

	public static function newExceptionCatcher()
	{
		$catcher = new class()
		{
		};
		return $catcher;
	}
}
?>
