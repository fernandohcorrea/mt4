<?php

namespace Fhc\Bootstrap\DB\Adapter;

/**
 * Description of SqllitePdo
 *
 * @author fcorrea
 */
final class SqllitePdo extends \PDO
{

    private $db_path;
    
    public function __construct($options)
    {
        $this->parseOptions($options);
        parent::__construct('sqlite:' . $this->db_path);
        $this->setAttribute(
           \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION
        );
    }

    private function parseOptions($options){
        $required = [
            'filepath',
            'filename'
        ];
        
        foreach ($required as $req) {
            if(empty($options[$req])){
                throw new \Fhc\Exception\RuntimeException("Mysql Connection error. Require $req.");
            }
        }
        
        $this->db_path = APPATH . DS . $options['filepath'] . DS . $options['filename'];
        
        if(!file_exists($this->db_path) || !is_readable($this->db_path)){
            throw new \Fhc\Exception\RuntimeException("Sqllite DB file({$this->db_path}) is untouchable ");
        }
        
    }
}
