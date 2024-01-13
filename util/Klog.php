<?php
namespace Util;

use Katzgrau\KLogger\Logger;
use Psr\Log\LogLevel;
/**
 * Clase que maneja los log del sistema
 *
 * @author Josemi
 */
class Klog {
    const LOG_DIR = '/log/';
    private static $logPath;
    private static $log = [];
    
    public static function emergency($message) {
        self::hayLog(LogLevel::EMERGENCY);
        self::$log[LogLevel::EMERGENCY]->emergency($message);
    }
    
    public static function alert($message) {
        self::hayLog(LogLevel::ALERT);
        self::$log[LogLevel::ALERT]->alert($message);
    }
    
    public static function critical($message) {
        self::hayLog(LogLevel::CRITICAL);
        self::$log[LogLevel::CRITICAL]->critical($message);
    }
    
    public static function error($message) {
        self::hayLog(LogLevel::ERROR);
        self::$log[LogLevel::ERROR]->error($message);
    }
    
    public static function warning($message) {
        self::hayLog(LogLevel::WARNING);
        self::$log[LogLevel::WARNING]->warning($message);
    }
    
    public static function notice($message) {
        self::hayLog(LogLevel::NOTICE);
        self::$log[LogLevel::NOTICE]->notice($message);
    }

    public static function info($message) {
        self::hayLog(LogLevel::INFO);
        self::$log[LogLevel::INFO]->info($message);
    }
    
    public static function debug($message) {
        self::hayLog(LogLevel::DEBUG);
        self::$log[LogLevel::DEBUG]->debug($message);
    }
    
    
    private static function hayLog($loglevel){
        self::hayLogPath();
        if(!isset(self::$log[$loglevel])){
            self::$log[$loglevel] = new Logger(self::$logPath, $loglevel, array('prefix' => $loglevel.'_'));
        }
    }
    
    private static function hayLogPath() {
        if(empty(self::$logPath)){
            $year = date('Y');
            $month = date('m');
            self::$logPath = DIR_ROOT.self::LOG_DIR.$year.'/'.$month;
        }
    }
}
