<?php

namespace Fhc\Bootstrap;

use Fhc\Config\Loader;

/**
 * Description of Api
 *
 * @author Fernando H Corrêa <fernandohcorrea@gmail.com>
 */
class Load
{

    private static $instance;
    private static $iniFile;
    private static $Router;
    private static $DbManager;
    

    public static function config($iniFile = NULL)
    {
        try {
            if (!self::$instance) {

                self::$iniFile = (is_string($iniFile) and strlen($iniFile) > 4) ? $iniFile : null;

                $class = __CLASS__;
                self::$instance = new $class;
            }
            
            return self::$instance;
            
        } catch (\Exception $exc) {
            $out_exp = array(
                'Message' => $exc->getMessage(),
                'Code' => $exc->getCode(),
            );
            
            if(FHCENV == 'DEV'){
                $out_exp['file'] = $exc->getFile();
                $out_exp['line'] = $exc->getLine();
                $out_exp['trace'] = $exc->getTrace();
            }
            
            if( in_array($out_exp['Code'], range(400, 599)) ){
                http_response_code($exc->getCode());
            } else {
                http_response_code(500);
            }
            
            error_log("Exception: {$exc->getMessage()}. File - {$exc->getFile()}[{$exc->getLine()}] - code({$exc->getCode()})");
            die;
        }
    }

    private function __construct()
    {
        Loader::init(self::$iniFile);
        self::buildDBManager();
        self::buildRouter();
    }

    private static function buildRouter()
    {
        self::$Router = new Router();
        self::$Router->run();
    }
    
    private static function buildDBManager()
    {
        self::$DbManager = DB\Manager::getInstance();
    }

}
