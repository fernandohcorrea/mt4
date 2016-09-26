<?php

namespace Fhc\Bootstrap\DB;

use Fhc\Config\Loader;

/**
 * Description of Manager
 *
 * @author fcorrea
 */
class Manager
{

    private static $instance;
    private $cfg;
    private $dbs;

    /**
     * Obter instância
     * 
     * @return \Fhc\Bootstrap\DB\Manager
     */
    public static function getInstance()
    {
        if (!self::$instance) {

            $class = __CLASS__;
            self::$instance = new $class;
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->parseCfg();
        $this->buildConnections();
    }

    private function parseCfg()
    {
        $cfgRoot = 'db_manager_';
        $data_connections = Loader::get($cfgRoot . '*');

        if (count($data_connections)) {
            foreach ($data_connections as $key => $config) {
                $parts = explode('_', str_replace($cfgRoot, '', $key));

                if (count($parts) != 2) {
                    throw new \Fhc\Exception\RuntimeException('Config error: db_manager_<name>_(type, ...)');
                }

                list($connection_name, $cfgKey) = $parts;
                $this->cfg[$connection_name][$cfgKey] = $config;
            }
        }
    }

    private function buildConnections()
    {
        if (count($this->cfg)) {
            foreach ($this->cfg as $name_connection => $cfgData) {

                if (empty($cfgData['type'])) {
                    throw new \Fhc\Exception\RuntimeException('Config error: db_manager_<name>_type required)');
                }

                $pdo = null;

                switch (strtoupper($cfgData['type'])) {
                    case 'SQLITE':
                        $pdo = new Adapter\SqllitePdo($cfgData);
                        break;

                    case 'MYSQL':
                        $pdo = new Adapter\MysqlPdo($cfgData);
                        break;

                    default:
                        throw new \Fhc\Exception\RuntimeException("Invalid type of connection");
                        break;
                }

                $this->dbs[$name_connection] = $pdo;
            }
        }
    }

    /**
     * Obter Connexão.
     * 
     * @param string $name
     * @return \PDO
     */
    public function getConn($name)
    {
        if (isset($this->dbs[$name])) {
            return $this->dbs[$name];
        } else {
            throw new \Fhc\Exception\RuntimeException("Connection($name) not found");
        }
    }

    public function __destruct()
    {
        if (count($this->dbs)) {
            foreach ($this->dbs as $name_connection => $pdoConnection) {
                unset($this->dbs[$name_connection]);
            }
        }
    }

}
