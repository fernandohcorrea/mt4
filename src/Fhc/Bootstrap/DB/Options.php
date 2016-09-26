<?php

namespace Fhc\Bootstrap\DB;

use Fhc\Config\AbstractOptions;

/**
 * Description of Options
 *
 * @author fcorrea
 */
class Options extends AbstractOptions
{

    const MANAGER_TYPE = 'manager_type';
    const FILE = 'file';

    private $manager_type;
    private $file;

    function getFile()
    {
        return $this->file;
    }

    function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    function getManagerType()
    {
        return $this->manager_type;
    }

    function setManagerType($manager_type)
    {
        $this->manager_type = $manager_type;
        return $this;
    }

}
