<?php

namespace Fhc\Config;

use Fhc\Exception as Exc;

/**
 * Description of AbstractOptions
 *
 * @author fcorrea
 */
abstract class AbstractOptions
{

    protected $data_properties = array();

    public function __construct(Array $options = array())
    {
        $array_constants = $this->getConstants();

        if (!count($array_constants)) {
            throw new Exc\RuntimeException('Required constants properties');
        }

        if (count($options)) {
            foreach ($options as $property => $value) {
                $this->__set($property, $value);
            }
        }
    }

    public function __set($property, $value)
    {
        if (empty($property)) {
            throw new Exc\RuntimeException("Invalid Options property");
        }

        $array_constants = $this->getConstants();

        foreach ($array_constants as $const_property) {

            if ($const_property === $property) {

                $method_name = 'set' . $this->fixMethodName($property);

                if (method_exists($this, $method_name)) {
                    $this->$method_name($value);
                } else {
                    $this->data_properties[$property] = $value;
                }
            }
        }
    }
    
    public function __get($property)
    {
        if (empty($property)) {
            throw new Exc\RuntimeException("Invalid Options property");
        }

        $reflection = new \ReflectionObject($this);
        $array_constants = $reflection->getConstants();

        foreach ($array_constants as $const_property) {

            if ($const_property === $property) {

                $method_name = 'get' . $this->fixMethodName($property);

                if (method_exists($this, $method_name)) {
                    return $this->$method_name();
                } else {
                    return $this->data_properties[$property];
                }
            }
        }
    }

    protected function fixMethodName($name)
    {
        $var_key = str_replace('_', ' ', $name);
        $var_lower = strtolower($var_key);
        $var_ucf = ucwords($var_lower);
        return str_replace(' ', '', $var_ucf);
    }

    private function getConstants()
    {
        $reflection = new \ReflectionObject($this);
        return $reflection->getConstants();
    }

}
