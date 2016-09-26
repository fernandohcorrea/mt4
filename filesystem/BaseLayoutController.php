<?php

namespace Fhc\Controller;

/**
 * Description of BaseLayoutController
 *
 * @author fcorrea
 */
abstract class BaseLayoutController extends Base\AbsController
{

    public function preDispatch()
    {
        session_start();
        parent::preDispatch();
        
        if($this->isAjaxRequest()){
            header('Content-Type: application/json; charset=utf-8');
            $this->view = null;
            return $this;
        } 
        
        $this->buildMenu();
    }
    
    private function buildMenu()
    {
        $controller = $this->controller_name;
        
        $out = [];
        
        $out['page_head_title'] =  strtoupper($controller);
        
        $out['sidebarLeftMenu'] = [
                '/' => [
                    'menu_name' => 'Dashboard',
                    'icon' => 'fa-dashboard',
                    'cls' => ($controller == 'home') ? 'active' : null,
                ],
                '/ssh' => [
                    'menu_name' => 'SSH',
                    'icon' => 'fa-terminal',
                    'cls' => ($controller == 'ssh') ? 'active' : null,
                ],
                '/crypt' => [
                    'menu_name' => 'Crypt',
                    'icon' => 'fa-slack',
                    'cls' => ($controller == 'crypt') ? 'active' : null,
                ],
                '/filesystem' => [
                    'menu_name' => 'FileSystem',
                    'icon' => 'fa-cloud-upload',
                    'cls' => ($controller == 'filesystem') ? 'active' : null,
                ],
        ];
        
        $this->view->setData($out);
    }

}
