<?php

namespace Fhc\Bootstrap\DB\Adapter;

/**
 * Description of SqllitePdo
 *
 * @author fcorrea
 */
final class MysqlPdo extends \PDO
{

    public function __construct($options)
    {
        $this->parseOptions($options);
        
        $dsn = 'mysql:dbname=%s;host=%s;port=%s';
        $dsn = sprintf(
                $dsn,
                $options['dbname'],
                $options['host'],
                $options['port']
        );
        
        parent::__construct($dsn, $options['user'], $options['password'],[
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION
        ]);
    }

    private function parseOptions($options)
    {
        $required = [
            'host',
            'dbname',
            'user',
            'password',
            'port'
        ];
        
        foreach ($required as $req) {
            if(empty($options[$req])){
                throw new \Fhc\Exception\RuntimeException("Mysql Connection error. Require $req.");
            }
        }
        
    }
}
