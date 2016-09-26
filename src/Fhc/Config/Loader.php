<?php

namespace Fhc\Config;

/**
 * Configuration manager
 * Load INI files and parse it.
 * 
 * @author Fernando H Corrêa fernandohcorrea(TO)gmail.com
 */
class Loader {

    private static $instance;
    private static $cfgData;
    private static $defaultDataName = 'cfg';
    private static $fileCfg;
    private static $dirCfg;

    public static function init($cfgFile) {
        if (!self::$instance) {

            self::$fileCfg = (is_string($cfgFile) and strlen($cfgFile) > 4) ? $cfgFile : null;

            $class = __CLASS__;
            self::$instance = new $class;
        }

        return self::$instance;
    }

    private function __construct() {
        if (self::$fileCfg) {
            list(self::$dirCfg, self::$fileCfg, $dataName) = self::parsePathFile(self::$fileCfg);
            self::$cfgData[self::$defaultDataName] = self::parseFile(self::$dirCfg, self::$fileCfg, $dataName);
        }
    }

    public static function loadCfg($cfgFile) {
        $dataReturn = FALSE;
        list($cfgPath, $fileCfg, $dataRoot) = self::parsePathFile($cfgFile);

        if ($dataRoot !== self::$defaultDataName) {
            self::$cfgData[$dataRoot] = self::parseFile($cfgPath, $fileCfg, $dataRoot);
            $dataReturn = self::$cfgData[$dataRoot];
        }

        return $dataReturn;
    }

    private static function parsePathFile($pathFile) {
        $matches = array();
        if (preg_match('@^(/)?(.*/)?(([\-\.\w\d]+).(ini|php))$@im', $pathFile, $matches)) {
            list($pathFile, $bar, $dirCfg, $fileCfg, $dataRoot) = $matches;
            $dirCfg = ($dirCfg != "/") ? $dirCfg : null;
            $fileCfg = $fileCfg;
            return array($dirCfg, $fileCfg, $dataRoot);
        } else {
            throw new \Fhc\Exception\RuntimeException('Arquivo de configuração ' . self::$fileCfg . ' não é um arquivo válido', 0);
        }
    }

    private static function parseFile($dirCfg, $fileCfg, $dataRoot)
    {
        $cfgData = NULL;
        $includePaths = explode(PATH_SEPARATOR, get_include_path());
        $filePathCfg = null;
        if (count($includePaths)) {
            foreach ($includePaths as $incPath) {
                $lastBar = (substr($incPath, strlen($incPath) - 1) != "/") ? "/" : null;
                if (is_file($incPath . $lastBar . $dirCfg . $fileCfg)) {
                    $filePathCfg = $incPath . $lastBar . $dirCfg . $fileCfg;
                    break;
                }
            }
        }
        
        if (!is_null($filePathCfg)) {
            $cfgData = parse_ini_file($filePathCfg);
            if (!count($cfgData)) {
                throw new \Fhc\Exception\RuntimeException('Arquivo de configuração ' . $fileCfg . ' não contém configurações', 0);
            }
        } else {
            throw new \Fhc\Exception\RuntimeException('Arquivo de configuração ' . $fileCfg . ' não encontrado', 0);
        }

        return $cfgData;
    }

    public static function get($cfgName, $dataName = NULL)
    {
        $cfgOut = NULL;
        if (strpos($cfgName, '*')) {
            $cfgOut = self::getCfgGroup($cfgName, $dataName);
        } else {
            $cfgOut = self::getCfg($cfgName, $dataName);
        }

        if (is_null($cfgOut)) {
            throw new \Fhc\Exception\RuntimeException('Parametro de configuração não encontrado. [' . $cfgName . ']', 0);
        }

        return $cfgOut;
    }

    private static function getCfgGroup($cfgName, $dataName = NULL) {
        $cfgOut = NULL;
        $cfgName = '/^' . str_replace('*', '.*', $cfgName) . '$/im';
        $useDataName = (isset(self::$cfgData[$dataName])) ? $dataName : self::$defaultDataName;

        foreach (self::$cfgData[$useDataName] as $key => $value) {
            if (preg_match($cfgName, $key)) {
                $cfgOut[$key] = $value;
            }
        }

        return $cfgOut;
    }

    private static function getCfg($cfgName, $dataName = NULL) {
        $cfgOut = NULL;
        $useDataName = (isset(self::$cfgData[$dataName])) ? $dataName : self::$defaultDataName;

        if (isset(self::$cfgData[$useDataName][$cfgName])) {
            $cfgOut = self::$cfgData[$useDataName][$cfgName];
        }
        return $cfgOut;
    }

}
