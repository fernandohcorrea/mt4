<?php

namespace Fhc\View\Base;

use Fhc\Config\Loader;
use Fhc\Exception as Exc;

/**
 * Description of AbsView
 *
 * @author fcorrea
 */
abstract class AbsView
{

    /**
     * Dados da View
     * @var Array
     */
    protected $data_view = [];
    
    /**
     * Configirações da View
     * @var Array
     */
    protected $cfg;
    
    /**
     * Caminho do Template
     * @var String
     */
    protected $templates_path;
    
    /**
     * Extensão do Template
     * @var String
     */
    protected $templates_extension;
    
    /**
     * Template 
     * @var String
     */
    protected $template;
    
    /**
     * Flag de Render do template
     * @var boolean
     */
    protected $flg_rendered = FALSE;

    /**
     * Construtor
     * 
     * @param string $template
     * @param array $data Array de Tuplas com dados do template
     */
    public function __construct($template = NULL, Array $data = Array())
    {
        $this->parseCfg();
        $this->setTemplate($template);

        if (count($data)) {
            foreach ($data as $property => $value) {
                $this->__set($property, $value);
            }
        }
    }

    /**
     * Parse de configurações
     * 
     * @throws Exc\RuntimeException
     */
    protected function parseCfg()
    {
        $cfg_root = 'view';
        $this->cfg = Loader::get($cfg_root . '*');
        if (!is_array($this->cfg)) {
            throw new Exc\RuntimeException('Config error: Required view_(templates_path, templates_extension...)');
        }

        $required = [
            'templates_path', 'templates_extension'
        ];

        foreach ($required as $cfg_key) {
            $cfg_idx = $cfg_root . '_' . $cfg_key;

            if (!isset($this->cfg[$cfg_idx]) || is_null($this->cfg[$cfg_idx])) {
                throw new Exc\RuntimeException("Config required[$cfg_idx]");
            }

            switch ($cfg_key) {
                case 'templates_path':
                    $this->templates_path = $this->cfg[$cfg_idx];
                    break;

                case 'templates_extension':
                    $this->templates_extension = $this->cfg[$cfg_idx];
                    break;
            }
        }
    }

    /**
     * Obter Template(Caminho)
     * 
     * @return string
     */
    public function getTemplate()
    {
        return realpath(
            implode(DS, [
                APPATH,
                $this->templates_path,
                $this->template . '.' . $this->templates_extension,
            ])
        );
    }

    /**
     * Define Template
     * @param set $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Define data 
     * Merge de array com dados existentes
     * 
     * @param array $data
     */
    public function setData(Array $data)
    {
        $this->data_view = array_merge_recursive($this->data_view, $data);
    }

    /**
     * Magic Method Set
     * 
     * @param string $property
     * @param string $value
     * @throws Exc\RuntimeException
     */
    public function __set($property, $value)
    {
        if (empty($property)) {
            throw new Exc\RuntimeException("Invalid data property");
        }

        $method_name = 'set' . $this->fixMethodName($property);

        if (method_exists($this, $method_name)) {
            $this->{$method_name}($value);
        } else {
            $this->data_view[$property] = $value;
        }
    }

    /**
     * Magic Method Get
     * 
     * @param type $property
     * @return type
     * @throws Exc\RuntimeException
     */
    public function __get($property)
    {
        if (empty($property)) {
            throw new Exc\RuntimeException("Invalid data property");
        }

        $method_name = 'get' . $this->fixMethodName($property);

        if (method_exists($this, $method_name)) {
            return $this->{$method_name}();
        } else {
            return $this->data_view[$property];
        }
    }

    /**
     * Corrige nome do method
     * 
     * @param string $name
     * @return string
     */
    private function fixMethodName($name)
    {
        $var_key = str_replace('_', ' ', $name);
        $var_lower = strtolower($var_key);
        $var_ucf = ucwords($var_lower);
        return str_replace(' ', '', $var_ucf);
    }

    /**
     * Obter Flag Render
     * @return boolean
     */
    public function getFlgRendered()
    {
        return $this->flg_rendered;
    }

    /**
     * Render da View
     * 
     * @param array $data
     * @return boolean
     */
    public function render(Array $data = null)
    {
        if ($this->getFlgRendered()) {
            return false;
        }

        if (count($data)) {
            $data = array_merge_recursive($this->data_view, $data);
        } else {
            $data = $this->data_view;
        }

        $include_template = $this->getTemplate();
        

        extract($data);
        include_once $include_template;
        return $this->flg_rendered = TRUE;
        
        
    }

}
