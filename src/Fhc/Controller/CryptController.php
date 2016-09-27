<?php

namespace Fhc\Controller;

use Fhc\Bootstrap\DB\Manager;

/**
 * Description of HomeController
 *
 * @author fcorrea
 */
class CryptController extends BaseLayoutController
{


    public function index()
    {
        $this->view->render();
    }
    
    public function crypt()
    {
        $jsonout = [];
        $jsonout['status'] = false;
        
        $post = $this->getParamsPost();
        
        $normaltext = $post['normaltext'];
        $cryptkey = $post['cryptkey'];
        
        $scret = new \Fhc\Crypt\Secret();
        $crypttext = $scret->encrypt($normaltext, $cryptkey);
        
        if(strlen($crypttext)){
            
            $jsonout['data'] = [
                'normaltext' => $normaltext,
                'cryptkey' => $cryptkey,
                'crypttext' => $crypttext
            ];
            $jsonout['status'] = true;
        }
        
        
        echo json_encode($jsonout);
    }
    
    public function decrypt()
    {
        $jsonout = [];
        $jsonout['status'] = false;
        
        $post = $this->getParamsPost();
        
        $crypttext = $post['crypttext'];
        $cryptkey = $post['cryptkey'];
        
        $scret = new \Fhc\Crypt\Secret();
        $normaltext = $scret->decrypt($crypttext, $cryptkey);
        
        if(strlen($normaltext)){
            
            $jsonout['data'] = [
                'normaltext' => $normaltext,
                'cryptkey' => $cryptkey,
                'crypttext' => $crypttext
            ];
            $jsonout['status'] = true;
        }
        
        
        echo json_encode($jsonout);
    }


}
