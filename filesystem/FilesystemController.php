<?php

namespace Fhc\Controller;

/**
 * Description of HomeController
 *
 * @author fcorrea
 */
class FilesystemController extends BaseLayoutController
{
    
    private $cfg;
    
    public function __construct()
    {
        $this->cfg =  \Fhc\Config\Loader::get('filesystem_*');
    }

    public function index()
    {
        $this->loadFilesystemNode();
        $this->view->render();
    }

    private function loadFilesystemNode($node)
    {
        $ret = [];
        $root_path = realpath( implode(DS, [
            APPATH,
            $this->cfg['filesystem_root_path']
        ]));
                
        $path = $root_path;

        $fileInt = new \FilesystemIterator($path);
        
        foreach ($fileInt as $key => $file) {
            $ret[] = $file;
            print_r([$file]);
        }
        die(__FILE__.'['.__LINE__.']');
        
    }

}
