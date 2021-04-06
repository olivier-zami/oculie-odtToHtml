<?php
namespace Oculie;

class Debug
{
    public static function enableLog($logStatus)
    {
        self::$showLogStatus = (bool)$logStatus;
    }

    public static function enableVerboseMode($verboseMode)
    {
        self::$verboseStatus = (bool)$verboseMode;
    }

    public static function useDisplay($callable=NULL)
    {
        self::$display = $callable;
    }

    public static function dump($var=NULL, $die=TRUE)
    {
        $trace = debug_backtrace();
        $file = $trace[0]["file"];
        $line = $trace[0]["line"];
        $msg = "<fieldset><legend>".($die?"Die":"Dump")." in ".$file." @ line ".$line."</legend><pre>";
        $msg .= print_r($var, TRUE);
        $msg .= "</pre></fieldset>";
        if($die) die($msg);
        else echo $msg;
    }

    public static function log($message="", $silentMode=FALSE)
    {
        if(!self::$showLogStatus)return;

        $trace = debug_backtrace();
        $file = $trace[0]["file"];
        $line = $trace[0]["line"];
        if($silentMode)
        {
            self::$log[] = $message;
        }
        else
        {
            $log = "";
            if(!empty(self::$log))
            {
                $log = "\n<ul>\n\t<li>".implode("</li>\n\t<li>", self::$log)."</li>\n</ul>\n";
                self::$log = [];
            }
            echo "<br/>\n".(self::$verboseStatus ? "In \"".$file."\" @ line \"".$line."\"".__METHOD__." " : "=").">".$message.$log;
        }
    }

    public static function trace($var=NULL, $die=FALSE)
    {
        $trace = debug_backtrace();
        $file = $trace[0]["file"];
        $line = $trace[0]["line"];
        $msg = "<fieldset><legend>".($die?"Die":"Dump")." in ".$file." @ line ".$line."</legend><pre>";
        $msg .= print_r($trace[1], TRUE)."<hr/>";
        $msg .= print_r($var, TRUE);
        $msg .= "</pre></fieldset>";
        if($die) die($msg);
        else echo $msg;
    }

    /*
     * Routines & Properties
     */

    private static $showLogStatus   = FALSE;
    private static $verboseStatus   = FALSE;
    private static $log             = [];
    private static $display         = NULL;
}
?>